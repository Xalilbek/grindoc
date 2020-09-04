<?php

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/Icra_Muddeti_Deyisdirilmesi.php';
require_once DIRNAME_INDEX . 'prodoc/functions/settings.php';
require_once DIRNAME_INDEX . 'prodoc/functions/file.php';

define('DOCUMENT_KEY', 'icra_muddeti_deyisdirilmesi');

$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$id = getInt('id',0);
$sened_hazirla = getInt('sened_hazirladan');
$taskID = (int)get('taskId')[0];
$related_document_id = isset($_POST['related_document_id'][0]) ? $_POST['related_document_id'][0] : '';
$fromWhere = true;
if($sened_hazirla)
{
    $fromWhere = false;
}


$razilasma = [
    [
        "InputType" => "array",
        "ColumnName" => "yoxlayanShexs"
    ],
];

$baqlanti_sened_nomre = [
            [
                "IsRequired" => $fromWhere,
                "InputType" => "id",
                "ColumnName" => "document_id",
                "Title" => "Sənədin nömrəsi"
            ],
];

$fields = [
    [
        "InputType" => "text.date",
        "ColumnName" => "senedin_tarixi"
    ],
    [
        "IsRequired" => isPdfFileRequired() == true && $id == 0,
        "InputType" => "arrayOfIds",
        "ColumnName" => "sened_fayl",
        "IsRequiredErrorMessage" => "Pdf faylı seçilməyib!",
        "customValidation" => 'checkPdfExistense'
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "qoshma_fayl"
    ],
    [
        "IsRequired" => true,
        "InputType" => "text.datetime",
        "ColumnName" => "icra_muddeti_muraciet_olunan_tarix",
        "IsRequiredErrorMessage" => "Müraciət olunan tarix vacib sahedi"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "qisa_mezmun"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "poa_user_id"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd",
    ],
];

$baqlanti_form = new Form($baqlanti_sened_nomre);
$baqlanti_form->check();
$baqlantiFormInserted = $baqlanti_form->collectDataToBeInserted();

$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

$razilasma_gonder = new Form($razilasma);
$razilasma_gonder->check();
$razilasmadata = $razilasma_gonder->collectDataToBeInserted();
try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();
    $sened_id = '';

    if($sened_hazirla)
    {
        $sened_id = $related_document_id;
    }
    else
    {
        $sened_id = $baqlantiFormInserted['document_id'];
    }

    if ($id) {
        $sened_fayl = $dataToBeInserted['sened_fayl'];
        $qoshma_fayl = $dataToBeInserted['qoshma_fayl'];
        unset($dataToBeInserted['sened_fayl']);
        unset($dataToBeInserted['qoshma_fayl']);

        $taskCommand = new \Model\InternalDocument\IcraMuddetiDeyisdirilmesi($id);
        unset($dataToBeInserted['poa_user_id']);
        $taskCommand->edit($dataToBeInserted,$user);

        updateFileId($taskCommand->getInfo()['document_id'],$sened_fayl,'daxil_olan_senedler_sened_fayl');
        updateFileId($taskCommand->getInfo()['document_id'],$qoshma_fayl,'daxil_olan_senedler_qoshma_fayl');

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($taskCommand->getData()['document_id']);

        $user->editInternalDocument(
            $intDoc,
            DOCUMENT_KEY,
            false
        );

        $documentId = $taskCommand->getData()['document_id'];
    } else {
        $sened_fayl = $dataToBeInserted['sened_fayl'];
        $qoshma_fayl = $dataToBeInserted['qoshma_fayl'];
        unset($dataToBeInserted['sened_fayl']);
        unset($dataToBeInserted['qoshma_fayl']);
        $taskCommand = \Model\InternalDocument\IcraMuddetiDeyisdirilmesi::create($dataToBeInserted, $user);

        updateFileId($taskCommand->getInfo()['document_id'],$sened_fayl,'daxil_olan_senedler_sened_fayl');
        updateFileId($taskCommand->getInfo()['document_id'],$qoshma_fayl,'daxil_olan_senedler_qoshma_fayl');

        $documentId = $taskCommand->getData()['document_id'];
        $id =$documentId;
        $_POST['related_document_menu'] = ['incoming'];
        $_POST['related_document_id'] = [$sened_id];
        $_POST['ishe_tik'] = ['0'];
        $_POST['netice'] = ['0'];
        $_POST['bind_to_document'] = '1';

        createRelation($documentId);

        $form->saveFiles($documentId, 'daxil_olan_senedler', PRODOC_FILES_SAVE_PATH);
    }

    $rey_muellifi   = DB::fetchColumn("SELECT rey_muellifi FROM tb_daxil_olan_senedler WHERE id = $sened_id");

    $tesdiqleyecekler = [];

    if(!empty($razilasmadata['yoxlayanShexs'][0]))
    {
        foreach ($razilasmadata['yoxlayanShexs'] as $value)
        {
            $tesdiqleyecekler[] = array(
                $value,
                (int)2,
                (int)0,
                (int)0,
                $user->tmzle('viza')
            );
        }
    }

    if(!empty($rey_muellifi)) {
        $tesdiqleyecekler[] = array(
            $rey_muellifi,
            (int)3,
            (int)0,
            (int)0,
            $user->tmzle('tesdiqleme')
        );
    }

    daxiliSenedinTestiqlemesiniElaveEtSerbest($taskCommand->getId(), DOCUMENT_KEY, $documentId, $tesdiqleyecekler, $taskID);

    pdo()->commit();
    $responseArr=array('id'=>$id);
    $user->success_msg("Ok!",$responseArr);
} catch (BaseException $e) {
    print json_encode([
        'status' => 'error',
        'errors' => [nl2br($e->getMessage())]
    ]);
} catch (Exception $e) {
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var']
    ]);
} finally {
    pdo()->rollBack();
}

function fayllariYarat($form, $outgoingDocument)
{
    $chixanSenedId = $outgoingDocument->getId();
    $form->saveFiles($chixanSenedId, 'chixan_senedler', PRODOC_FILES_SAVE_PATH);
}