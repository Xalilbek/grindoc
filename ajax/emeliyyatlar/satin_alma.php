<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/SatinAlmaSifaris.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/SatinAlmaXidmet.php';
$user = new User();

define('DOCUMENT_KEY', 'satin_alma');

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$id = getInt('id');


$sifaris_formasi = [
    [
         "InputType" => "text.date",
         "ColumnName" => "sifaris_tarixi"
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "sifaris_tipi",
        "IsRequiredErrorMessage" => "Sifariş tipi vacib sahədi"
    ],
    [
        "InputType" => "text.date",
        "ColumnName" => "senedin_tarixi"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd"
    ],
];


$SifarisForm = new Form($sifaris_formasi);
$SifarisForm->check();

$sifaris_xidmet = [
    [
        "InputType" => "array",
        "ColumnName" => "malin_kodu"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "mal_adi"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "olcu_vahidi"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "miqdar"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "mebleq"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "gun"
    ],
];

$XidmetForm = new Form($sifaris_xidmet);
$XidmetForm->check();

$dataToBeInsertedChixanSened = $XidmetForm->collectDataToBeInserted();
$sifarisFormasdata           = $SifarisForm->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    if ($id) {
        $taskCommand = new \Model\InternalDocument\SatinAlmaSifaris($id);
        $taskCommand->edit($sifarisFormasdata);

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($taskCommand->getData()['document_id']);

        $user->editInternalDocument(
            $intDoc,
            DOCUMENT_KEY,
            false
        );

        $sifarisAll = DB::fetchColumnArray("SELECT id FROM tb_prodoc_satinalma_sifaris_xidmet WHERE parent_id='$id'");
        if (!empty($sifarisAll))
        {
            pdof()->query("DELETE FROM tb_files WHERE module_entry_id IN  (". implode(",", $sifarisAll) .") ");
        }
        pdof()->query("DELETE FROM tb_prodoc_satinalma_sifaris_xidmet WHERE parent_id='$id'");

        $documentId = $taskCommand->getData()['document_id'];
    }
    else
    {
        $taskCommand = \Model\InternalDocument\SatinAlmaSifaris::create($sifarisFormasdata, $user);
        $id = $taskCommand->getId();
        $document  = $user->createInternalDocumentNumber($taskCommand->getId(), DOCUMENT_KEY, false, true);
        createRelation($document->getId());

        $documentId = $document->getId();
    }

    $XidmetDocuments = XidmetYarat($dataToBeInsertedChixanSened, $id);
    fayllariYarat($XidmetDocuments);

    daxiliSenedinTestiqlemesiniElaveEt($taskCommand->getId(), DOCUMENT_KEY, $documentId, '_' . $sifarisFormasdata['sifaris_tipi']);

    pdo()->commit();
    $responseArr=array('id'=>$id);
    $user->success_msg("Ok!",$responseArr);

} catch (Exception $e) {
    pdo()->rollBack();

    print json_encode([
        'status' => 'error',
        'errors' => [
            $e->getMessage()
        ]
    ]);
}

function XidmetYarat($dataToBeInsertedChixanSened, $numberingData)
{
    global $user;

    $malin_kodu  = $dataToBeInsertedChixanSened['malin_kodu'];
    $mal_adi     = $dataToBeInsertedChixanSened['mal_adi'];
    $olcu_vahidi = $dataToBeInsertedChixanSened['olcu_vahidi'];
    $miqdar      = $dataToBeInsertedChixanSened['miqdar'];
    $mebleq      = $dataToBeInsertedChixanSened['mebleq'];
    $gun         = $dataToBeInsertedChixanSened['gun'];
    $parent      = $numberingData;

    $tableDatasCount = count($malin_kodu);

    $XidmetDocuments = [];

    for ($i = 0; $i < $tableDatasCount; $i++)
    {
        $dataToBeInsertedChixanSened['malin_kodu']  = $malin_kodu[$i];
        $dataToBeInsertedChixanSened['mal_adi']     = $mal_adi[$i];
        $dataToBeInsertedChixanSened['olcu_vahidi'] = $olcu_vahidi[$i];
        $dataToBeInsertedChixanSened['miqdar']      = $miqdar[$i];
        $dataToBeInsertedChixanSened['mebleq']      = $mebleq[$i];
        $dataToBeInsertedChixanSened['gun']         = $gun[$i];
        $dataToBeInsertedChixanSened['parent_id']   = $parent;

        $XidmetDocument = \Model\InternalDocument\SatinAlmaXidmet::create($dataToBeInsertedChixanSened, $user);
        $XidmetDocuments[] = $XidmetDocument->getId();
    }

    return $XidmetDocuments;
}
function fayllariYarat($XidmetDocuments)
{
    global $userId;
    for ($i = 0, $len = count($XidmetDocuments); $i < $len; ++$i) {
        $name =  sprintf("document_%s", $i);
        $XidmetSenedId = $XidmetDocuments[$i];

        if (!isset($_FILES[$name])) {
            continue;
        }

        $files = saveFiles($name, PRODOC_FILES_SAVE_PATH, false);
        for ($j = 0, $lenj = count($files); $j < $lenj; ++$j) {
            SQL::insert('tb_files', [
                'module_name'           => 'satinAlma_fayl',
                'module_entry_id'       => $XidmetSenedId,
                'file_original_name'    => $files[$j]['file_original_name'],
                'file_actual_name'      => $files[$j]['file_actual_name'],
                'created_by'            => $userId,
            ]);
        }
    }
}
