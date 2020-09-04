<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/functions/settings.php';
require_once DIRNAME_INDEX . 'prodoc/functions/file.php';

$user = new User();
if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$showOutgoingDocumentContainer = get('show_outgoing_document_container', null);
$bindsToOutgoingDocument = !is_null($showOutgoingDocumentContainer);
$showIncomingDocumentContainer = get('show_incoming_document_container', null);
$bindsToIncomingDocument = !is_null($showIncomingDocumentContainer);

$id = getInt('id', 0);
$update = $id > 0;

$tableName = 'tb_daxil_olan_senedler';
$elave_shexsler= [
    [
        "InputType" => "array",
        "ColumnName" => "ishtirakchi"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "melumat"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "mesul_shexs"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "kurator"
    ],
];

$fields = [
    [
        "IsRequired" => $bindsToOutgoingDocument,
        "InputType" => "id",
        "ColumnName" => "outgoing_document_id",
        "Title" => "Xaric olan sənədin nömrəsi"
    ],
    [
        "IsRequired" => $bindsToIncomingDocument,
        "InputType" => "id",
        "ColumnName" => "incoming_document_id",
        "Title" => "Daxil olan sənədin nömrəsi"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "senedin_nomresi"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "document_number",
        "IsRequired" => !$update,
        "Title" => "Sənədin daxil olma №-si"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "editable_with_select"
    ],
    [
        "InputType" => "text.datetime",
        "ColumnName" => "icra_edilme_tarixi"
    ],
    [
        "InputType" => "text.datetime",
        "ColumnName" => "senedin_daxil_olma_tarixi"
    ],
    [
        "InputType" => "text.datetime",
        "ColumnName" => "senedin_tarixi"
    ],
    [
        "IsRequired" => !$bindsToOutgoingDocument,
        "Title" => "Göndərən təşkilat",
        "InputType" => "select",
        "ColumnName" => "gonderen_teshkilat"
    ],
    [

        "Title" => "Göndərən qurum",
        "InputType" => "select",
        "ColumnName" => "gonderen_aidiyyati_tabeli_id"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "derkenar_metn_id"
    ],
    [
        "Title" => "Göndərən şəxs",
        "InputType" => "select",
        "ColumnName" => "gonderen_shexs"
    ],
    [
        "Title" => "Məktubun qısa məzmunu",
        "InputType" => "id",
        "ColumnName" => "qisa_mezmun_id"
    ],
    [
        "Title" => "Göndərən təşkilatın №-si",
        "InputType" => "text",
        "ColumnName" => "gonderen_teshkilatin_nomresi"
    ],
    [
        "IsRequired" => !$bindsToOutgoingDocument,
        "Title" => "Məktubun tipi",
        "InputType" => "select",
        "ColumnName" => "mektubun_tipi"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "mektubun_alt_tipi"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "mektubun_tipi_third"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "tibb_muessisesi"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "nazalogiya"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "mektubun_mezmunu"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "mektub_nezaretdedir"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "daxil_olma_yolu_id"
    ],
    [
        "Title" => "Vərəqlərin sayı",
        "InputType" => "text",
        "ColumnName" => "vereq_sayi"
    ],
    [
        "Title" => "Qoşma vərəqlərinin sayı",
        "InputType" => "text",
        "ColumnName" => "qoshma_sayi"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "mektubun_qisa_mezmunu"
    ],
    [
        "IsRequired" => isPdfFileRequired() && $id == 0,
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
        "Title" => "Rəy müəllifi",
        "InputType" => "select",
        "ColumnName" => "rey_muellifi"
    ],
    [
        "Title" => "Yoxlayan şəxs",
        "InputType" => "select",
        "ColumnName" => "yoxlayan_shexs"
    ],
    [
        "Title" => "Sənədin tipi",
        "InputType" => "id",
        "ColumnName" => "sened_tip"
    ],
];

$form = new Form($fields);

$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

$elave_shexslerForm = new Form($elave_shexsler);
$elave_shexslerForm->check();
$dataToBeInsertedElave_shexsler = $elave_shexslerForm->collectDataToBeInserted();
$editing = $id > 0;

if ($editing) {
    $incomingDocument = new Document($id);
}

try {
    DB::exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    $icraMuddetinDeyishdirilmesidir = false;
    if ($editing) {
        if (
            $incomingDocument->fullUpdateAllowed() &&
            (int)$dataToBeInserted['outgoing_document_id'] > 0 &&
            !array_key_exists($dataToBeInserted['outgoing_document_id'], OutgoingDocument::getOpenedAndApprovedOutgoingDocuments()) &&
            (int)$dataToBeInserted['outgoing_document_id'] !== (int)$incomingDocument->getInfo()['outgoing_document_id']
        ) {
            throw new Exception('Outgoing document is not correct');
        }
    } else {
        if (
            (int)$dataToBeInserted['outgoing_document_id'] > 0 &&
            !array_key_exists($dataToBeInserted['outgoing_document_id'], OutgoingDocument::getOpenedAndApprovedOutgoingDocuments())
        ) {
            throw new Exception('Outgoing document is not correct');
        }
    }


    if ($id) {
        $id_quote =$id;
        $dataToBeInserted['qeyd'] = $dataToBeInserted['mektubun_qisa_mezmunu'];
        $sened_fayl = $dataToBeInserted['sened_fayl'];
        $qoshma_fayl = $dataToBeInserted['qoshma_fayl'];
        unset($dataToBeInserted['sened_fayl']);
        unset($dataToBeInserted['qoshma_fayl']);

        $incomingDocument->edit($dataToBeInserted);

        updateFileId($incomingDocument->getId(),$sened_fayl,'daxil_olan_senedler_sened_fayl');
        updateFileId($incomingDocument->getId(),$qoshma_fayl,'daxil_olan_senedler_qoshma_fayl');

        DB::query("DELETE FROM tb_daxil_olan_senedler_elave_shexsler
                                WHERE daxil_olan_sened_id=".$id_quote);
        if ($dataToBeInserted['sened_tip']==1){
            $melumatlar=$dataToBeInsertedElave_shexsler["melumat"];
            $dataToBeInsertedElave_shexsler=array();
            $dataToBeInsertedElave_shexsler["melumat"]=$melumatlar;
        }
        elseif ($dataToBeInserted['sened_tip']==2){
            unset($dataToBeInsertedElave_shexsler["melumat"]);
        }
        $group=NULL;

        foreach ($dataToBeInsertedElave_shexsler as $key=> $shexsler){

            foreach ($shexsler as $shexs){
                if($shexs!=""){
                    $explode = explodeGroupOrPerson($shexs);
                    SQL::insert('tb_daxil_olan_senedler_elave_shexsler', [
                        'tip'  => $key,
                        'daxil_olan_sened_id'   => $incomingDocument->getId(),
                        'user_id'    => $explode['shexs'],
                        'group_id'   => $explode['group']
                    ]);
                }

            }
        }

    } else {
        $dataToBeInserted['TenantId'] = $user->getActiveTenantId();
        $dataToBeInserted['tip'] = Document::TIP_HUQUQI;

        if(getMuddet('activ_son_tarix') == 1 && $dataToBeInserted['sened_tip'] == 2)
        {
            $gunSonIcraTarixi   = getMuddet('son_icra_tarix_gun');
            if($gunSonIcraTarixi != "")
            {
                $res = getDeadline($dataToBeInserted['senedin_daxil_olma_tarixi'], $gunSonIcraTarixi);
                $dataToBeInserted['son_icra_tarixi'] = convertValueToSQLFormat('text.datetime', $res);
            }
        }

        if($dataToBeInserted['sened_tip'] == 1)
        {
            $dataToBeInserted['icra_edilme_tarixi'] = NULL;
        }

        $dataToBeInserted['qeyd'] = $dataToBeInserted['mektubun_qisa_mezmunu'];

        $sened_fayl = $dataToBeInserted['sened_fayl'];
        $qoshma_fayl = $dataToBeInserted['qoshma_fayl'];
        unset($dataToBeInserted['sened_fayl']);
        unset($dataToBeInserted['qoshma_fayl']);

        $incomingDocument = Document::create($dataToBeInserted, $user);

        updateFileId($incomingDocument->getId(),$sened_fayl,'daxil_olan_senedler_sened_fayl');
        updateFileId($incomingDocument->getId(),$qoshma_fayl,'daxil_olan_senedler_qoshma_fayl');

        if (isset($_POST['skan_senedler']) && is_array($_POST['skan_senedler']))
        {
            foreach ($_POST['skan_senedler'] as $sened){
                $sql = "SELECT sened_tip, sened_id, file_id FROM tb_prodoc_skan_files_senedler";
                SQL::insert('tb_prodoc_skan_files_senedler', [
                    'sened_tip'  => 'daxil_olan_fiziki',
                    'sened_id'   => $incomingDocument->getId(),
                    'file_id'    => $sened
                ]);
            }
        }

        $outgoingDocId = (int)$dataToBeInserted['outgoing_document_id'];
        if ($outgoingDocId) {
            require_once DIRNAME_INDEX . 'prodoc/includes/outgoing_document.php';
            $outgoingDocType = getOutgoingDocumentTypeExtraId($outgoingDocId);

            if ($outgoingDocType === 'icra_muddeti') {
                $sql = "
                    SELECT document_number, v_prodoc_outgoing_document_relation.daxil_olan_sened_id
                    FROM v_prodoc_outgoing_document_relation
                    LEFT JOIN v_daxil_olan_senedler_corrected
                        ON v_prodoc_outgoing_document_relation.daxil_olan_sened_id = v_daxil_olan_senedler_corrected.id
                    WHERE v_prodoc_outgoing_document_relation.outgoing_document_id = '$outgoingDocId'
                ";

                $icraMuddetiDaxilOlanSened = DB::fetch($sql);
                $icraMuddetiDaxilOlanSenedNomre = $icraMuddetiDaxilOlanSened['document_number'];
                $icraMuddetinDeyishdirilmesidir = true;

                $testiqlenen_tarix = get('testiqlenen_tarix');
                if (empty($testiqlenen_tarix)) {
                    throw new Exception('Təsdiqlənən tarixi qeyd etmədiniz!');
                }

                $testiqlenen_tarix = convertValueToSQLFormat('text.datetime', $testiqlenen_tarix);

                DB::update('tb_daxil_olan_senedler', [
                    'icra_edilme_tarixi' => $testiqlenen_tarix
                ], $icraMuddetiDaxilOlanSened['daxil_olan_sened_id']);
            }
        }
        $group=NULL;

        foreach ($dataToBeInsertedElave_shexsler as $key=> $shexsler){

            foreach ($shexsler as $shexs){
                if($shexs!=""){
                    $explode = explodeGroupOrPerson($shexs);
                    SQL::insert('tb_daxil_olan_senedler_elave_shexsler', [
                        'tip'  => $key,
                        'daxil_olan_sened_id'   => $incomingDocument->getId(),
                        'user_id'    => $explode['shexs'],
                        'group_id'   => $explode['group']
                    ]);
                }

            }
        }

    }
    $id=$incomingDocument->getId();

    $document_number = pdof()->query("Select document_number from tb_daxil_olan_senedler where id=".$id)->fetch();


//   start---- Notifications

    if ($icraMuddetinDeyishdirilmesidir) {

        foreach (executorsOfDocument($incomingDocument->getId(),'all',false) as $shexs){
            $user->sendNotifications( true, true,
                'icra_muddeti_cavab',
                "", "",
                $incomingDocument->getId(),
                $shexs,
                "icra_muddeti_cavab",
                "",
                "",
                "",
                $icraMuddetiDaxilOlanSenedNomre,
                "daxil_olan_sened"
            );
        }



        $user->sendNotifications( true, true,
            'icra_muddeti_cavab',
            "", "",
            $id,
            $dataToBeInserted['rey_muellifi'],
            "icra_muddeti_cavab",
            "",
            "",
            "",
            $icraMuddetiDaxilOlanSenedNomre,
            "daxil_olan_sened",
            "icra_muddeti_cavab"
        );

        $user->sendNotifications( true, true,
            'icra_muddeti_cavab',
            "", "",
            $id,
            $dataToBeInserted['yoxlayan_shexs'],
            "icra_muddeti_cavab",
            "",
            "",
            "",
            $icraMuddetiDaxilOlanSenedNomre,
            "daxil_olan_sened",
            "icra_muddeti_cavab"
        );
    } else {
        if($dataToBeInserted['outgoing_document_id'] > 0){
            $derkenar_id= pdof()->query("Select derkenar_id from v_prodoc_outgoing_document_relation where outgoing_document_id=".$dataToBeInserted['outgoing_document_id'])->fetch();
            if ($derkenar_id['derkenar_id']!=null && $derkenar_id['derkenar_id']!=''&& $derkenar_id['derkenar_id']>0){

                $task = new Task($derkenar_id['derkenar_id']);

                if ($task->isSubTask()){

                    $parentTask=$task->getMainTask($task);
                    $kurator= pdof()->query("Select user_id from tb_derkenar_elave_shexsler where tip='kurator' and derkenar_id=".$parentTask->id);
                    if($kurator!=null){
                        $user->sendNotifications( true, true,
                            'sorguya_cavab_kurator',
                            "", "",
                            $id,
                            $kurator[0],
                            "sorguya_cavab_kurator",
                            "",
                            "",
                            "",
                            "",
                            "daxil_olan_sened",
                            "yeni_sened"
                        );
                    }

                }
                else{
                    $kurator=pdof()->query("SELECT user_id FROM tb_derkenar_elave_shexsler WHERE tip='kurator' AND derkenar_id=".$derkenar_id['derkenar_id'])->fetch();

                    if($kurator!=null){
                        $user->sendNotifications( true, true,
                            'sorguya_cavab_kurator',
                            "", "",
                            $id,
                            $kurator[0],
                            "sorguya_cavab_kurator",
                            "",
                            "",
                            "",
                            "",
                            "daxil_olan_sened",
                            "yeni_sened"
                        );
                    }
                }
            }




            $mesulIcra = pdof()->query("SELECT DISTINCT tb_derkenar.mesul_shexs from tb_derkenar LEFT JOIN tb_daxil_olan_senedler on tb_derkenar.daxil_olan_sened_id= tb_daxil_olan_senedler.id WHERE daxil_olan_sened_id in(SELECT t1.daxil_olan_sened_id FROM
    tb_prodoc_muraciet AS t1
    LEFT JOIN tb_prodoc_appeal_outgoing_document AS t2 ON t1.id = t2.appeal_id WHERE outgoing_document_id =".$dataToBeInserted['outgoing_document_id'].")")->fetch();
            $rey_veren = pdof()->query("SELECT DISTINCT rey_muellifi from tb_derkenar LEFT JOIN tb_daxil_olan_senedler on tb_derkenar.daxil_olan_sened_id= tb_daxil_olan_senedler.id WHERE daxil_olan_sened_id in(SELECT t1.daxil_olan_sened_id FROM
    tb_prodoc_muraciet AS t1
    LEFT JOIN tb_prodoc_appeal_outgoing_document AS t2 ON t1.id = t2.appeal_id WHERE outgoing_document_id =".$dataToBeInserted['outgoing_document_id'].")")->fetch();

            if($mesulIcra!=null){
                $user->sendNotifications( true, true,
                    'sorguya_cavab',
                    "", "",
                    $id,
                    $mesulIcra[0],
                    "sorguya_cavab",
                    "",
                    "",
                    "",
                    $document_number[0],
                    "daxil_olan_sened",
                    "yeni_sened"
                );
            }
            if($rey_veren!=null){
                $user->sendNotifications( true, true,
                    'sorguya_cavab',
                    "", "",
                    $id,
                    $rey_veren[0],
                    "sorguya_cavab",
                    "",
                    "",
                    "",
                    $document_number[0],
                    "daxil_olan_sened",
                    "yeni_sened"
                );
            }





        }

        if ($dataToBeInserted['yoxlayan_shexs'] > 0)
        {
            $sessionUserInfo = $user->getUserInfo();
            $username = $user->tmzle($sessionUserInfo['user_name']);
            $user->sendNotifications( true, true,
                'prodoc_yoxlamaya_gonderilib',
                $username, "",
                $id,
                $dataToBeInserted['yoxlayan_shexs'],
                "prodoc_yoxlamaya_gonderilib",
                "",
                "",
                "",
                $document_number[0],
                "daxil_olan_sened",
                "yoxlama"
            );
        }
        else if ($dataToBeInserted['rey_muellifi'] > 0 && !$editing)
        {
            $sessionUserInfo = $user->getUserInfo();
            $username = $user->tmzle($sessionUserInfo['user_name']);
            $user->sendNotifications( true, true,
                'prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra',
                $username, "",
                $id,
                $dataToBeInserted['rey_muellifi'],
                "prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra",
                "",
                "",
                "",
                $document_number[0],
                "daxil_olan_sened",
                "derkenar"
            );
        }

    }
//   end---- Notifications
//    $form->saveFiles($incomingDocument->getId(), 'daxil_olan_senedler', PRODOC_FILES_SAVE_PATH);
//    $form->saveFileIds($incomingDocument->getId());

    pdo()->commit();
    $responseArr=array('sened_id'=>$id);
    $user->success_msg("Ok!",$responseArr);

} catch (Exception $e) {
    pdo()->rollBack();
    print json_encode([
        'status' => 'error',
        'errors' => [$e->getMessage()]
    ]);
    exit();
}