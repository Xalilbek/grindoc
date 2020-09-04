<?php

use History\History;

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/UmumiForma.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
require_once DIRNAME_INDEX . 'prodoc/functions/settings.php';
require_once DIRNAME_INDEX . 'prodoc/functions/file.php';

define('DOCUMENT_KEY', 'umumi_forma');

$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$id = getInt('id',0);

$icraya_gondere_shexsler = [
    [
        "InputType" => "array",
        "ColumnName" => "ishtirakchi"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "mesul_shexs"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "kurator"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "melumat"
    ],
];

$fromWhere = false;

if(isset($_POST['sened_novu']) && $_POST['sened_novu']>0){
    $sened_novu = $_POST['sened_novu'];
    $set = DB::fetchColumn("Select id from tb_sened_novu where [key]='xidmeti_mektub'");
    $fromWhere=($set==$sened_novu);
}



$elave_shexsler = [
    [
        "InputType" => "array",
        "ColumnName" => "melumat"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "yoxlayanShexs"
    ],
    [
        "IsRequired" => !$fromWhere,
        "InputType" => "id",
        "ColumnName" => "rey_muellifi",
        "IsRequiredErrorMessage" => "Rey müəllifi vacib sahədi"
    ],
    [
        "IsRequired" => $fromWhere,
        "InputType" => "id",
        "ColumnName" => "kime",
        "IsRequiredErrorMessage" => "Kimə vacib sahədi"
    ],
];

$fields = [
    [
        "IsRequired" => true,
        "InputType" => "text.datetime",
        "ColumnName" => "senedin_tarixi",
        "IsRequiredErrorMessage" => "Sənədin tarixini seçmədiniz"
    ],
    [
        "IsRequired" => isPdfFileRequired() && $id == 0,
        "InputType" => "arrayOfIds",
        "ColumnName" => "sened_fayl",
        "IsRequiredErrorMessage" => "Pdf faylı seçilməyib!",
        "customValidation" => 'checkPdfExistense'
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "sened_novu",
        "IsRequiredErrorMessage" => "Sənədin növü vacib sahədi"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "qisa_mezmun"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "qoshma_fayl"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd",
    ],
    [
        "Title" => "Sənəd",
        "InputType" => "file",
        "ColumnName" => "sened"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "poa_user_id"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "imzali"
    ],
    [
        "InputType" => "text.datetime",
        "ColumnName" => "icra_edilme_tarixi"
    ],
];

$datilSenedUpdate = [
    [
        "InputType" => "select",
        "ColumnName" => "derkenar_metn_id"
    ],
    [
        "Title" => "Senəd tipi",
        "InputType" => "id",
        "ColumnName" => "sened_tip"
    ],
    [
        "InputType" => "text.datetime",
        "ColumnName" => "icra_edilme_tarixi"
    ],
];


$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

$daxilSened = new Form($datilSenedUpdate);
$daxilSened->check();
$daxilSenedInserted = $daxilSened->collectDataToBeInserted();

$icraya_gonder = new Form($elave_shexsler);
$icraya_gonder->check();
$icrayaGonderdata = $icraya_gonder->collectDataToBeInserted();

$elave_shexslerForm = new Form($icraya_gondere_shexsler);
$elave_shexslerForm->check();
$dataToBeInsertedElave_shexsler = $elave_shexslerForm->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    if ($id) {
        $id_quote =$id;
        $sened_fayl = $dataToBeInserted['sened_fayl'];
        $qoshma_fayl = $dataToBeInserted['qoshma_fayl'];
        unset($dataToBeInserted['sened_fayl']);
        unset($dataToBeInserted['qoshma_fayl']);

        $taskCommand = new \Model\InternalDocument\UmumiForma($id);
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

        $id_quote =$documentId;

        DB::query("DELETE FROM tb_daxil_olan_senedler_elave_shexsler
                                WHERE daxil_olan_sened_id=".$id_quote);

    } else {

        $sened_fayl = $dataToBeInserted['sened_fayl'];
        $qoshma_fayl = $dataToBeInserted['qoshma_fayl'];
        unset($dataToBeInserted['sened_fayl']);
        unset($dataToBeInserted['qoshma_fayl']);

        $taskCommand = \Model\InternalDocument\UmumiForma::create($dataToBeInserted, $user);

        updateFileId($taskCommand->getInfo()['document_id'],$sened_fayl,'daxil_olan_senedler_sened_fayl');
        updateFileId($taskCommand->getInfo()['document_id'],$qoshma_fayl,'daxil_olan_senedler_qoshma_fayl');
//        $document = $user->createInternalDocumentNumber($taskCommand->getId(), DOCUMENT_KEY, false, true);
        $documentId = $taskCommand->getData()['document_id'];
        createRelation($documentId);
        $id= $taskCommand->getId();

    }

    $tesdiqleyecekler = [];

    if(!empty($icrayaGonderdata['yoxlayanShexs'][0]))
    {
        foreach ($icrayaGonderdata['yoxlayanShexs'] as $value)
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

    if(!empty($icrayaGonderdata['rey_muellifi']) && !$fromWhere)
    {
        $tesdiqleyecekler[]	 = array (
            (int)$icrayaGonderdata['rey_muellifi'],
            (int)3,
            (int)0,
            (int)0,
            $user -> tmzle('derkenar')
        );
    }

    if(!empty($icrayaGonderdata['kime']))
    {
        $tesdiqleyecekler[]	 = array (
            (int)$icrayaGonderdata['kime'],
            (int)3,
            (int)0,
            (int)0,
            $user -> tmzle('tesdiqleme')
        );
    }

    daxiliSenedinTestiqlemesiniElaveEtSerbest($taskCommand->getId(), DOCUMENT_KEY, $documentId, $tesdiqleyecekler, $documentId);

    if ($daxilSenedInserted['sened_tip']==1){
        $melumatlandiricilar=$dataToBeInsertedElave_shexsler["melumat"];
        $dataToBeInsertedElave_shexsler=array();
        $dataToBeInsertedElave_shexsler["melumat"]=$melumatlandiricilar;
    }
    elseif ($daxilSenedInserted['sened_tip']==2){
        unset($dataToBeInsertedElave_shexsler["melumat"]);
    }

    foreach ($dataToBeInsertedElave_shexsler as $key=> $shexsler){
        foreach ($shexsler as $shexs){
            if($shexs!="") {
                $explode = explodeGroupOrPerson($shexs);
                SQL::insert('tb_daxil_olan_senedler_elave_shexsler', [
                    'tip'  => $key,
                    'daxil_olan_sened_id'   => $documentId,
                    'user_id' => $explode['shexs'],
                    'group_id' =>$explode['group']
                ]);
            }
        }
    }

    $son_icra_tarixi    = NULL;
    $teleb_olunan_tarix = NULL;

    if(getMuddet('activ_son_tarix_daxili') == 1 && $daxilSenedInserted['sened_tip'] == 2)
    {
        $gunSonIcraTarixi   = getMuddet('son_icra_tarix_gun_daxili');
        if($gunSonIcraTarixi != "") {
            $res = getDeadline($dataToBeInserted['senedin_tarixi'], $gunSonIcraTarixi);
            $son_icra_tarixi = convertValueToSQLFormat('text.datetime', $res);
        }
    }

    if($daxilSenedInserted['sened_tip'] == 2)
    {
        $teleb_olunan_tarix =  $daxilSenedInserted['icra_edilme_tarixi'];
    }

    SQL::update('tb_daxil_olan_senedler', [
        'derkenar_metn_id' => $daxilSenedInserted['derkenar_metn_id'],
        'sened_tip'        => $daxilSenedInserted['sened_tip'],
        'qisa_mezmun_id'        => $dataToBeInserted['qisa_mezmun'],
        'icra_edilme_tarixi' => $teleb_olunan_tarix,
        'son_icra_tarixi' => $son_icra_tarixi
    ], $documentId);

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
