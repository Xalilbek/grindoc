<?php

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/Icra_Sexsin_Deyisdirilmesi.php';
require_once DIRNAME_INDEX . 'prodoc/functions/settings.php';

define('DOCUMENT_KEY', 'icra_sexsin_deyisdirilmesi');

$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$id = getInt('id',0);
$taskID = (int)get('taskId')[0];

$razilasma = [
        [
            "InputType" => "arrayOfIds",
            "ColumnName" => "yoxlayanShexs"
        ],
];

$hemIcraciShexs = [
    [
        "InputType" => "array",
        "ColumnName" => "hemIcraciShexs"
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
        "InputType" => "id",
        "ColumnName" => "yeni_icra_eden_sexs",
        "IsRequiredErrorMessage" => "Yeni icra edən şəxs vacib sahedi"
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
    [
//        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "related_document_id",
        "IsRequiredErrorMessage" => "Sənədin nömrəsi vacib sahedi"
    ],
];

$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

$razilasma_gonder = new Form($razilasma);
$razilasma_gonder->check();
$razilasmadata = $razilasma_gonder->collectDataToBeInserted();

$hemIcraciShexs_gonder = new Form($hemIcraciShexs);
$hemIcraciShexs_gonder->check();
$hemIcraciShexsdata = $hemIcraciShexs_gonder->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    $Id = getRequiredPositiveInt('related_doc_id');


    $sechilmish_yeni_icra_shexs = DB::fetchAll("SELECT
                                                      mesul_shexs,
                                                      CONCAT ( Adi, ' ', Soyadi ) AS user_ad 
                                                     FROM
                                                     v_derkenar
                                                         LEFT JOIN v_users ON USERID = mesul_shexs 
                                                     WHERE daxil_olan_sened_id = ".$Id);

    foreach ($sechilmish_yeni_icra_shexs as $sechilmish){
        if($sechilmish['mesul_shexs']==$dataToBeInserted['yeni_icra_eden_sexs']){

            $errors[]= sprintf("\"%s\" artıq bu sənəddə daha öncədən icraçı kimi təyin olunub.", $sechilmish['user_ad']);
            if(count($errors)>0){
                print json_encode([
                    'status' => 'error',
                    'errors' => $errors
                ]);

                exit();
            }
        }
    }

    if ($id) {

        $id_quote =$id;
        $sened_fayl = $dataToBeInserted['sened_fayl'];
        $qoshma_fayl = $dataToBeInserted['qoshma_fayl'];
        unset($dataToBeInserted['sened_fayl']);
        unset($dataToBeInserted['qoshma_fayl']);

        $taskCommand = new \Model\InternalDocument\IcraSexsinDeyisdirilmesi($id);
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
        DB::query("DELETE FROM tb_icra_sexsin_deyisdirilmesi_hem_icraci_sexsler WHERE parent_id='$id'");
    } else {

        $sened_fayl = $dataToBeInserted['sened_fayl'];
        $qoshma_fayl = $dataToBeInserted['qoshma_fayl'];
        unset($dataToBeInserted['sened_fayl']);
        unset($dataToBeInserted['qoshma_fayl']);


        $taskCommand = \Model\InternalDocument\IcraSexsinDeyisdirilmesi::create($dataToBeInserted, $user);

        updateFileId($taskCommand->getInfo()['document_id'],$sened_fayl,'daxil_olan_senedler_sened_fayl');
        updateFileId($taskCommand->getInfo()['document_id'],$qoshma_fayl,'daxil_olan_senedler_qoshma_fayl');

        $documentId = $taskCommand->getData()['document_id'];
        $id = $taskCommand->getData()['document_id'];

        $_POST['related_document_menu'] = ['incoming'];
        $_POST['related_document_id'] = [$Id];
        $_POST['ishe_tik'] = ['0'];
        $_POST['netice'] = ['0'];
        $_POST['bind_to_document'] = '1';

        createRelation($documentId);

    }

    HemIcraciShexsYarat($hemIcraciShexsdata, $taskCommand->getId());

    $eleva_shexsler = DB::fetchAll("SELECT user_id AS id FROM v_derkenar_elave_shexsler WHERE derkenar_id = {$dataToBeInserted['related_document_id']} ");

    $rey_muellifi   = DB::fetchColumn("SELECT rey_muellifi FROM tb_daxil_olan_senedler WHERE id = $Id");

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

    $_POST['related_document_id']   = [$_POST['related_doc_id']];
    $_POST['related_document_menu'] = [$_POST['related_document_menu']];

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
        'errors' => [$e->getMessage()]
    ]);
} finally {
    pdo()->rollBack();
}

function HemIcraciShexsYarat($hemIcraciShexsdata, $id)
{
    if(!empty($hemIcraciShexsdata['hemIcraciShexs'][0])) {
        $yoxlayanShexsler = $hemIcraciShexsdata['hemIcraciShexs'];
        $tableDatasCount = count($yoxlayanShexsler);

        for ($i = 0; $i < $tableDatasCount; $i++) {
            $hemIcraciShexsdata['hemIcraciShexs'] = $yoxlayanShexsler[$i];

            pdof()->query("INSERT INTO tb_icra_sexsin_deyisdirilmesi_hem_icraci_sexsler (parent_id, hemIcraciShexs) VALUES ('$id', {$hemIcraciShexsdata['hemIcraciShexs']})");
        }
    }
}