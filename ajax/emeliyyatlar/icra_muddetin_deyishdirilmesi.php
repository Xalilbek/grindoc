<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';
include_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
include_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
include_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
include_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';
include_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
include_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
require_once DIRNAME_INDEX . 'prodoc/includes/outgoing_document.php';
$user = new User();

use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$taskId = getInt('taskId', 0);
$id = getInt('id', 0);

$fizikiShexseGonderilir = getInt('teyinat') === OutgoingDocument::TEYINAT_FIZIKI_SHEXS;

$chixanSenedFields = [
    [
        "IsRequired" => true,
        "Title" => "Kim göndərir",
        "InputType" => "arrayOfIds",
        "ColumnName" => "kim_gonderir"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd"
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "teyinat",
        "Title" => "Təyinat"
    ],
    [
        "Title" => "Məktubun qısa məzmunu",
        "InputType" => "id",
        "ColumnName" => "qisa_mezmun_id"
    ],
    [
        "IsRequired" => !$fizikiShexseGonderilir,
        "InputType" => "id",
        "ColumnName" => "gonderen_teshkilat",
        "Title" => "Göndərən təşkilat"
    ],
    [
        "IsRequired" => $fizikiShexseGonderilir,
        "InputType" => "id",
        "ColumnName" => "gonderen_shexs",
        "Title" => "Göndərən şəxs"
    ],
    [
        "IsRequired" => false,
        "InputType" => "text",
        "ColumnName" => "icra_muddeti_vereq_sayi"
    ],
    [
        "IsRequired" => true,
        "InputType" => "text.datetime",
        "ColumnName" => "icra_muddeti_muraciet_olunan_tarix",
        "Title" => "Müraciət olunan tarix"
    ],
    [
        "IsRequired" => false,
        "InputType" => "text",
        "ColumnName" => "icra_muddeti_qeyd"
    ],
    [
        "Title" => "Sənəd",
        "InputType" => "arrayOfIds",
        "ColumnName" => "sened_fayl"
    ],
    [
        "Title" => "Sənəd",
        "InputType" => "arrayOfIds",
        "ColumnName" => "qoshma_fayl"
    ],
    [
        "IsRequired" => true,
        "Title" => "Daxil olan sənəd",
        "InputType" => "id",
        "ColumnName" => "daxil_olan_sened_id",
        "IsRequiredErrorMessage" => "Sənəd seçmək vacib sahədir"
    ],
];
$chixanSenedForm = new Form($chixanSenedFields);
$chixanSenedForm->check();

$butunTestiqleyenFields = [
    [
        "IsRequired" => false,
        "InputType" => "arrayOfIds",
        "ColumnName" => TestiqleyecekShexs::TIP_RAZILASHDIRAN
    ],
    [
        "IsRequired" => false,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => TestiqleyecekShexs::TIP_CHAP_EDEN
    ],
    [
        "IsRequired" => false,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => TestiqleyecekShexs::TIP_REDAKT_EDEN
    ],
    [
        "IsRequired" => false,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => TestiqleyecekShexs::TIP_VISA_VEREN
    ],
    [
        "IsRequired" => false,
        "InputType" => "arrayOfIds",
        "ColumnName" => "kim_gonderir"
    ],
];

$butunTestiqleyenForm = new Form($butunTestiqleyenFields);
$butunTestiqleyenForm->check();

$numberingFields = [
    [
        "InputType" => "text",
        "ColumnName" => "document_number",
        "IsRequired" => true,
        "Title" => "Sənədin daxil olma №-si"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "editable_with_select"
    ],
];

$numberingForm = new Form($numberingFields);
$numberingForm->check();

$dataToBeInsertedChixanSened = $chixanSenedForm->collectDataToBeInserted();
$butunTestiqleyenler         = $butunTestiqleyenForm->collectDataToBeInserted();
$numberingData               = $numberingForm->collectDataToBeInserted();

try {
    DB::exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    $isEdit = $id > 0;

    if ($isEdit) {
        deleteOldRelation($id);
        deleteOldApprovingUsers($id);
    }

    $muraciet_tip_icra_muddeti = DB::fetchColumn(
        "SELECT id FROM tb_prodoc_muraciet_tip WHERE silinib = 0 AND (extra_id = 'icra_muddeti')"
    );

    if (FALSE === $muraciet_tip_icra_muddeti) {
        $muraciet_tip_icra_muddeti = DB::insertAndReturnId('tb_prodoc_muraciet_tip', [
            'ad' => 'İcra müddətinin dəyişdirilməsi',
            'serbest' => 0,
            'emeliyyat' => 0,
            'silinib' => 0,
            'extra_id' => 'icra_muddeti'
        ]);
    }

    $doc = new Document($dataToBeInsertedChixanSened['daxil_olan_sened_id']);
    $documentDeadline = strtotime($doc->data['icra_edilme_tarixi']);

    checkNewDeadline(
        strtotime($dataToBeInsertedChixanSened['icra_muddeti_muraciet_olunan_tarix']),
        $documentDeadline
    );

    $dataToBeInsertedChixanSened['muraciet_tip_id'] = $muraciet_tip_icra_muddeti;

    $outgoingDocument = chixanSenediYarat($dataToBeInsertedChixanSened, $numberingData);
    fayllariYarat($outgoingDocument);
    $confirmingUsers = getConfirmingUsers($butunTestiqleyenler);

    if ($taskId) {
        $netice = neticeniGotur($dataToBeInsertedChixanSened['daxil_olan_sened_id']);

        $data = [
            'tip' => Appeal::TIP_SENED_HAZIRLA,
            'daxil_olan_sened_id' => $dataToBeInsertedChixanSened['daxil_olan_sened_id'],
            'derkenar_id' => $taskId,
            'netice_id' => $netice,
            'outgoingDocuments' => [$outgoingDocument],
            'document_elaqelendirme' => 0
        ];

        $appeal = Appeal::create($data);
    } else {
        $netice = neticeniGotur($dataToBeInsertedChixanSened['daxil_olan_sened_id']);

        $data = [
            'tip' => Appeal::TIP_SENED_HAZIRLA,
            'daxil_olan_sened_id' => $dataToBeInsertedChixanSened['daxil_olan_sened_id'],
            'derkenar_id' => NULL,
            'netice_id' => $netice,
            'outgoingDocuments' => [$outgoingDocument],
            'document_elaqelendirme' => 0
        ];

        $appeal = Appeal::create($data, $user);
    }

    $confirmingUsers = $appeal->getConfirmingUsers($confirmingUsers, [$dataToBeInsertedChixanSened['daxil_olan_sened_id']]);
    $confirmation = new \Service\Confirmation\Confirmation($outgoingDocument);
    $confirmation->createConfirmingUsers($confirmingUsers);

    $doc =& $outgoingDocument;
    $confirmationn = new \Service\Confirmation\Confirmation($doc);
    $currentOrder = $confirmationn->getCurrentOrder();
    $confirmationnn= $confirmationn->getCurrentApprovingUsers();
    $id=   $doc->getId();
    if (isset($currentOrder)){

        $sql = "
             SELECT user_id,tip,  document_number, (SELECT CONCAT(Adi,' ' ,Soyadi) as user_name FROM tb_users WHERE USERID=v_chixan_senedler.created_by) as user_name, 
            related_record_id, teyinat FROM tb_prodoc_testiqleyecek_shexs LEFT JOIN v_chixan_senedler on 
            v_chixan_senedler.id=related_record_id  WHERE [order] = {$currentOrder} AND related_class = 'OutgoingDocument' AND related_record_id ='$id'
        ";
        $users = DB::fetchAll($sql);

        $mesul_shexsler = pdof()->query("SELECT top 1 t1.*, t2.outgoing_document_id, v_daxil_olan_senedler.created_by, rey_muellifi,
        ( SELECT mesul_shexs FROM tb_derkenar WHERE id = ( SELECT parentTaskId FROM tb_derkenar WHERE id = derkenar_id AND parentTaskId > 0 ) ) AS mesul_shexs,
        (SELECT CONCAT(Adi,' ' ,Soyadi) from tb_users where USERID=(Select mesul_shexs from tb_derkenar 
        WHERE id=(SELECT parentTaskId FROM tb_derkenar WHERE id=derkenar_id and parentTaskId>0 ) ))
         as mesul_shexs_ad  FROM
        tb_prodoc_muraciet AS t1 LEFT JOIN tb_prodoc_appeal_outgoing_document AS t2 ON t1.id = t2.appeal_id LEFT JOIN v_daxil_olan_senedler on v_daxil_olan_senedler.id=daxil_olan_sened_id 
WHERE
    t2.outgoing_document_id = '".$id."' 
ORDER BY
    t2.outgoing_document_id DESC")->fetch();


        $tip="";

        foreach ($users as $tUser) {
            if ($tUser['tip']=='mesul_shexs'&&$mesul_shexsler['mesul_shexs']!=0&&$mesul_shexsler['mesul_shexs']!=''&&$mesul_shexsler['mesul_shexs']!=null){

                switch ($tUser['teyinat'])
                {
                    case '3': $tip.="aidiyyati_orqan";break;
                    case '4': $tip.="fiziki_shexs";break;
                    case '5': $tip.="tabeli_qurum";break;
                    default: exit(); break;
                }
                $user->sendNotifications( true, true,
                    $tip,
                    $tUser['user_name'], "",
                    $tUser['related_record_id'],
                    $mesul_shexsler['mesul_shexs'],
                    $tip,
                    "",
                    "",
                    "",
                    $tUser['document_number'],
                    "xaric_olan_sened",
                    "derkenar"
                );

                $user->sendNotifications( true, true,
                    $tip."_qeydiyyatci",
                    $tUser['user_name'], $mesul_shexsler['mesul_shexs_ad'],
                    $tUser['related_record_id'],
                    $mesul_shexsler['created_by'],
                    $tip."_qeydiyyatci",
                    "",
                    "",
                    "",
                    $tUser['document_number'],
                    "xaric_olan_sened",
                    "derkenar"
                );



            }
            else if ($tUser['tip']==TestiqleyecekShexs::TIP_REY_MUELIFI){

                switch ($tUser['teyinat'])
                {
                    case '3': $tip.="aidiyyati_orqan";break;
                    case '4': $tip.="fiziki_shexs";break;
                    case '5': $tip.="tabeli_qurum";break;
                    default: exit(); break;
                }

                $user->sendNotifications( true, true,
                    $tip,
                    $tUser['user_name'], "",
                    $tUser['related_record_id'],
                    $mesul_shexsler['rey_muellifi'],
                    $tip,
                    "",
                    "",
                    "",
                    $tUser['document_number'],
                    "xaric_olan_sened",
                    "derkenar"
                );

                $user->sendNotifications( true, true,
                    $tip."_qeydiyyatci",
                    $tUser['user_name'], $mesul_shexsler['mesul_shexs_ad'],
                    $tUser['related_record_id'],
                    $mesul_shexsler['created_by'],
                    $tip."_qeydiyyatci",
                    "",
                    "",
                    "",
                    $tUser['document_number'],
                    "xaric_olan_sened",
                    "derkenar"
                );



            }
            else{

                $user->sendNotifications( true, true,
                    $tUser['tip'],
                    $tUser['user_name'], "",
                    $tUser['related_record_id'],
                    $tUser['user_id'],
                    $tUser['tip'],
                    "",
                    "",
                    "",
                    $tUser['document_number'],
                    "xaric_olan_sened",
                    $tUser['tip']
                );
            }
        }
    }

    pdo()->commit();
    $user->success_msg();

} catch (Exception $e) {
    pdo()->rollBack();

    print json_encode([
        'status' => 'error',
        'errors' => [
            $e->getMessage()
        ]
    ]);
    exit();
}

function chixanSenediYarat($dataToBeInsertedChixanSened, $numberingData)
{
    global $user, $id;
    unset($dataToBeInsertedChixanSened['sened_fayl']);
    unset($dataToBeInsertedChixanSened['qoshma_fayl']);
    $dataToBeInsertedChixanSened = \Util\ArrayUtils::omit(
        $dataToBeInsertedChixanSened, ['daxil_olan_sened_id']
    );

    if ($id) {
        $outgoingDocument = new OutgoingDocument((int)$id);

        if (!$outgoingDocument->duzelishEdeBiler()) {
            throw new Exception('Bu sənədə düzəliş edə bilmərsiz!');
        }

        $outgoingDocument->edit($dataToBeInsertedChixanSened);
    } else {
        $imzalayan_shexsler = $dataToBeInsertedChixanSened['kim_gonderir'];
        $dataToBeInsertedChixanSened['kim_gonderir'] = null;

        $outgoingDocument = OutgoingDocument::create($dataToBeInsertedChixanSened, $user);

        foreach ($imzalayan_shexsler as $shexs_id){
            \DB::insert('tb_chixan_senedler_imzalayan_shexsler', [
                'user_id' => $shexs_id,
                'document_id' => $outgoingDocument->getId(),
            ]);
        }
    }

    if (getProjectName() !== TS) {
        $outgoingDocumentData = $outgoingDocument->getData();
        if ((int)$outgoingDocumentData['document_number_id'] === 0) {
            $documentNumberGeneral = new DocumentNumberGeneral($outgoingDocument, [
                'manualDocumentNumber' => $numberingData['document_number'],
                'editable_with_select' => $numberingData['editable_with_select'],
            ]);

            $documentNumberId = $documentNumberGeneral->assignNumber();
            $outgoingDocument->setDocumentNumberId($documentNumberId);
        }
    }

    return $outgoingDocument;
}

function fayllariYarat($outgoingDocument)
{
    $chixanSenedId = $outgoingDocument->getId();

    if (isset($_POST['sened_fayl']) && is_array($_POST['sened_fayl'])) {
        $fileIds = array_map('intval', $_POST['sened_fayl']);


        foreach ($fileIds as $value) {

            DB::update('tb_files', ['module_entry_id' => $chixanSenedId], $value);
        }
    }

    $chixanSenedId = $outgoingDocument->getId();

    if (isset($_POST['qoshma_fayl']) && is_array($_POST['qoshma_fayl'])) {
        $fileIds = array_map('intval', $_POST['qoshma_fayl']);

        foreach ($fileIds as $value) {

            DB::update('tb_files', ['module_entry_id' => $chixanSenedId], $value);
        }
    }
}

function getConfirmingUsers($butunTestiqleyenler)
{
    $confirmingUsers = [];
    foreach ($butunTestiqleyenler as $tip => $testiqleyenler) {
        if (is_array($testiqleyenler)) {
            for ($j = 0, $len_j = count($testiqleyenler); $j < $len_j; ++$j) {
                $confirmingUsers[] = [
                    'type' => $tip,
                    'user_id' => $testiqleyenler[$j]
                ];
            }
        } else {
            $confirmingUsers[] = [
                'type' => $tip,
                'user_id' => $testiqleyenler
            ];
        }
    }

    return $confirmingUsers;
}
function neticeniGotur($dsId)
{
    $netice = NULL;
    $d = new Document($dsId);
    if ($d->neticeniDaxilEdeBiler()) {
        $netice = getInt('netice');
        if ($netice === 0) {
            throw new Exception('Nəticəni qeyd etmədniz!');
        }
    }

    return $netice;
}
function checkNewDeadline($newDeadline, $oldDeadline)
{
    $cariTarix = time();

    if ($newDeadline < $oldDeadline) {
        throw new Exception(
            'Müraciət olunan tarixi səhv qeyd etdiniz - sənədin cari icra tarixinden əvvəlki tarix qeyd oluna bilməz'
        );
    }

    if ($newDeadline < $cariTarix) {
        throw new Exception(
            'Müraciət olunan tarixi səhv qeyd etdiniz - cari tarixinden əvvəlki tarix ola bilməz.'
        );
    }
}

