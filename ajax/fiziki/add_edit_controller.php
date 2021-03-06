<?php

use Model\LastExecution\LastExecution;

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/functions/settings.php';
require_once DIRNAME_INDEX . 'prodoc/functions/file.php';
$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$showOutgoingDocumentContainer = get('show_outgoing_document_container', null);
$bindsToOutgoingDocument = !is_null($showOutgoingDocumentContainer);

$showIncomingDocumentContainer = get('show_incoming_document_container', null);
$bindsToIncomingDocument = !is_null($showIncomingDocumentContainer);

$fromWhereFororganization=false;
if($_POST['hardan_daxil_olub']>0){
    $hardan_daxil_olub = $_POST['hardan_daxil_olub'];
    $set = DB::fetchColumn("Select id from tb_prodoc_daxil_olma_menbeleri where [key]='qurum'");
    $fromWhereFororganization=($set==$hardan_daxil_olub);
    $fromWhere=false;

}else {
    $fromWhere=true;
}

$icrayaGonder = isset($_POST['icraya_gonder']) && $_POST['icraya_gonder'] === "on";
$gonderenShexsVacib = true;

$muraciet_eden = getInt('muraciet_eden_tip_id');
$set = (int)DB::fetchColumn("Select id from tb_prodoc_muraciet_eden_tip where extra_id='imzali'");
$gonderenShexsVacib=!($set===$muraciet_eden);

$id = getInt('id',0);

$update = $id > 0;

$daxilOlanSenedFields = [
    [
        "IsRequired" => $bindsToOutgoingDocument,
        "InputType" => "id",
        "ColumnName" => "outgoing_document_id",
        "Title" => "Sənədin nömrəsi"
    ],
    [
        "IsRequired" => $bindsToIncomingDocument,
        "InputType" => "id",
        "ColumnName" => "incoming_document_id",
        "Title" => "Daxil olan sənədin nömrəsi"
    ],
    [
        "InputType" => "text.datetime",
        "ColumnName" => "senedin_daxil_olma_tarixi"
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
        "ColumnName" => "senedin_tarixi"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "derkenar_metn_id"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "daxil_olma_yolu_id"
    ],
    [
        "IsRequired" => $fromWhereFororganization,
        "Title" => "Göndərən təşkilat",
        "InputType" => "select",
        "ColumnName" => "gonderen_teshkilat"
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
        "IsRequired" => false,
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
        "InputType" => "text.datetime",
        "ColumnName" => "icra_edilme_tarixi"
    ],
    [
        "IsRequired" => false,
        "Title" => "Vərəqlərin sayı",
        "InputType" => "text",
        "ColumnName" => "vereq_sayi"
    ],
    [
        "IsRequired" => false,
        "Title" => "Qoşma vərəqlərinin sayı",
        "InputType" => "text",
        "ColumnName" => "qoshma_sayi"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "mektubun_qisa_mezmunu"
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
        "IsRequired" => $icrayaGonder,
        "Title" => "Rəy müəllifi",
        "InputType" => "id",
        "ColumnName" => "rey_muellifi"
    ],
    [
        "Title" => "Yoxlayan şəxs",
        "InputType" => "id",
        "ColumnName" => "yoxlayan_shexs"
    ],
    [
        "Title" => "Sənədin tipi",
        "InputType" => "id",
        "ColumnName" => "sened_tip"
    ],
];

$elave_shexsler = [
    [
        "InputType" => "array",
        "ColumnName" => "ishtirakchi"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "mesul_shexs"
    ],

    [
        "InputType" => "array",
        "ColumnName" => "melumat"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "kurator"
    ],
];


$tekrar_eyni_checked = get('tekrar_eyni_checked');
$fizikiFields = [
    [
        "IsRequired" => !$bindsToOutgoingDocument,
        "InputType" => "select",
        "ColumnName" => "muraciet_eden_tip_id",
        "Title" => "Müraciət edən"
    ],
    [
        "IsRequired" => $gonderenShexsVacib && !$bindsToOutgoingDocument,
        "InputType" => "text",
        "ColumnName" => "muraciet_eden",
        "Title" => "Göndərən"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "unvan"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "region",
        "Title" => "Region"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "telefon",
        "Title" => "Telefonu"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "shexsiyyet_vesiqesi_teqdim_edilmeyib",
        "Title" => "Şəxsiyyət vəsiqəsi"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "shexsiyyet_vesiqesi_seria",
        "Title" => "Seria"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "shexsiyyet_vesiqesi_pin_kod",
        "Title" => "Kod"
    ],
    [
        "IsRequired" => !$bindsToOutgoingDocument,
        "InputType" => "id",
        "ColumnName" => "hardan_daxil_olub",
        "Title" => "Haradan daxil olub"
    ],
    [
        "InputType" => "select",
        "ColumnName" => "movzu",
        "Title" => "Mövzu"
    ],
    [
        "IsRequired" => !is_null($tekrar_eyni_checked),
        "Title" => "Təkrar/Eyni sənəd",
        "InputType" => "id",
        "ColumnName" => "tekrar_eyni_sened_id"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "tekrar_eyni_checked"
    ]
];

$phones = [
    [
        "Title" => "Telefonu",
        "InputType" => "array",
        "ColumnName" => "ophone"
    ],
];

$fizikiForm = new Form($fizikiFields);
$fizikiForm->check();
$dataToBeInsertedFiziki = $fizikiForm->collectDataToBeInserted();

$daxilOlanSenedForm = new Form($daxilOlanSenedFields);
$daxilOlanSenedForm->check();
$dataToBeInsertedDaxilOlanSened = $daxilOlanSenedForm->collectDataToBeInserted();

$elave_shexslerForm = new Form($elave_shexsler);
$elave_shexslerForm->check();
$dataToBeInsertedElave_shexsler = $elave_shexslerForm->collectDataToBeInserted();

$phonesForm = new Form($phones);
$phonesForm->check();
$dataToBeInsertedElave_phones = $phonesForm->collectDataToBeInserted();

$editing = $id > 0;

if ($editing) {
    $incomingDocument = new Document($id);

}
try {
    DB::exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
	pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	pdo()->beginTransaction();

    $muracietEdenId = getInt('muraciet_eden_id', 0);
    $muraciet_eden_tip_id = getInt('muraciet_eden_tip_id', 0);

    if($muraciet_eden_tip_id!=4){
        if ($muracietEdenId > 0) {
            $dataToBeInsertedFiziki['muraciet_eden'] = $muracietEdenId;
        } else {
            $dataToBeInsertedFiziki['muraciet_eden'] = getSenderId($dataToBeInsertedFiziki['muraciet_eden']);
        }
    }else{
        $dataToBeInsertedFiziki['muraciet_eden']=NULL;
    }


    $icraMuddetinDeyishdirilmesidir = false;

	if ($editing) {

        $id_quote =$id;

        $dataToBeInsertedDaxilOlanSened['qeyd'] = $dataToBeInsertedDaxilOlanSened['mektubun_qisa_mezmunu'];
        $sened_fayl = $dataToBeInsertedDaxilOlanSened['sened_fayl'];
        $qoshma_fayl = $dataToBeInsertedDaxilOlanSened['qoshma_fayl'];
        unset($dataToBeInsertedDaxilOlanSened['sened_fayl']);
        unset($dataToBeInsertedDaxilOlanSened['qoshma_fayl']);
        $incomingDocument->edit($dataToBeInsertedDaxilOlanSened, $dataToBeInsertedFiziki);

        updateFileId($incomingDocument->getId(),$sened_fayl,'daxil_olan_senedler_sened_fayl');
        updateFileId($incomingDocument->getId(),$qoshma_fayl,'daxil_olan_senedler_qoshma_fayl');

        DB::query("DELETE FROM tb_daxil_olan_senedler_elave_shexsler
                                WHERE daxil_olan_sened_id=".$id_quote);

        if ($dataToBeInsertedDaxilOlanSened['sened_tip']==1){
            $melumatlar=$dataToBeInsertedElave_shexsler["melumat"];
            $dataToBeInsertedElave_shexsler=array();
            $dataToBeInsertedElave_shexsler["melumat"]=$melumatlar;

        }
        elseif ($dataToBeInsertedDaxilOlanSened['sened_tip']==2){
            unset($dataToBeInsertedElave_shexsler["melumat"]);
        }

        foreach ($dataToBeInsertedElave_shexsler as $key=> $shexsler){
            foreach ($shexsler as $shexs){
                if($shexs!="") {
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

        DB::query("DELETE FROM tb_daxil_olan_senedler_fiziki_ophone
                                WHERE sened_id=".$id_quote);
        setInsert($dataToBeInsertedElave_phones['ophone'], $incomingDocument->getId());


    } else {
		$dataToBeInsertedDaxilOlanSened['TenantId'] = $user->getActiveTenantId();
        $dataToBeInsertedDaxilOlanSened['tip']      = Document::TIP_FIZIKI;

        $dataToBeInsertedFiziki['tekrar_eyni'] = $dataToBeInsertedFiziki['tekrar_eyni_checked'];
        unset($dataToBeInsertedFiziki['tekrar_eyni_checked']);

        if(getMuddet('activ_son_tarix_fiziki') == 1 && $dataToBeInsertedDaxilOlanSened['sened_tip'] == 2)
        {
            $gunSonIcraTarixi   = getMuddet('son_icra_tarix_gun_fiziki');
            if($gunSonIcraTarixi != "") {
                $res = getDeadline($dataToBeInsertedDaxilOlanSened['senedin_daxil_olma_tarixi'], $gunSonIcraTarixi);
                $dataToBeInsertedDaxilOlanSened['son_icra_tarixi'] = convertValueToSQLFormat('text.datetime', $res);
            }
        }

        if($dataToBeInsertedDaxilOlanSened['sened_tip'] == 1)
        {
            $dataToBeInsertedDaxilOlanSened['icra_edilme_tarixi'] = NULL;
        }

        $dataToBeInsertedDaxilOlanSened['qeyd'] = $dataToBeInsertedDaxilOlanSened['mektubun_qisa_mezmunu'];

        $sened_fayl = $dataToBeInsertedDaxilOlanSened['sened_fayl'];
        $qoshma_fayl = $dataToBeInsertedDaxilOlanSened['qoshma_fayl'];
        unset($dataToBeInsertedDaxilOlanSened['sened_fayl']);
        unset($dataToBeInsertedDaxilOlanSened['qoshma_fayl']);
        $incomingDocument = Document::create($dataToBeInsertedDaxilOlanSened, $user, $dataToBeInsertedFiziki);

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

        setInsert($dataToBeInsertedElave_phones['ophone'], $incomingDocument->getId());

        $outgoingDocId = (int)$dataToBeInsertedDaxilOlanSened['outgoing_document_id'];
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

        foreach ($dataToBeInsertedElave_shexsler as $key=> $shexsler){
            foreach ($shexsler as $shexs){
              if($shexs!="") {
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
            $dataToBeInsertedDaxilOlanSened['rey_muellifi'],
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
            $dataToBeInsertedDaxilOlanSened['yoxlayan_shexs'],
            "icra_muddeti_cavab",
            "",
            "",
            "",
            $icraMuddetiDaxilOlanSenedNomre,
            "daxil_olan_sened",
            "icra_muddeti_cavab"
        );
    } else {
        if($dataToBeInsertedDaxilOlanSened['outgoing_document_id'] > 0){
            $derkenar_id= pdof()->query("Select derkenar_id from v_prodoc_outgoing_document_relation where outgoing_document_id=".$dataToBeInsertedDaxilOlanSened['outgoing_document_id'])->fetch();
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
                    $kurator=pdof()->query("Select user_id from tb_derkenar_elave_shexsler where tip='kurator' and derkenar_id=".$derkenar_id['derkenar_id'])->fetchColumn();
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
LEFT JOIN tb_prodoc_appeal_outgoing_document AS t2 ON t1.id = t2.appeal_id WHERE outgoing_document_id =".$dataToBeInsertedDaxilOlanSened['outgoing_document_id'].")")->fetch();
            $rey_veren = pdof()->query("SELECT DISTINCT rey_muellifi from tb_derkenar LEFT JOIN tb_daxil_olan_senedler on tb_derkenar.daxil_olan_sened_id= tb_daxil_olan_senedler.id WHERE daxil_olan_sened_id in(SELECT t1.daxil_olan_sened_id FROM
tb_prodoc_muraciet AS t1
LEFT JOIN tb_prodoc_appeal_outgoing_document AS t2 ON t1.id = t2.appeal_id WHERE outgoing_document_id =".$dataToBeInsertedDaxilOlanSened['outgoing_document_id'].")")->fetch();

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

        if ($dataToBeInsertedDaxilOlanSened['yoxlayan_shexs'] > 0)
        {
            $sessionUserInfo = $user->getUserInfo();
            $username = $user->tmzle($sessionUserInfo['user_name']);
            $user->sendNotifications( true, true,
                'prodoc_yoxlamaya_gonderilib',
                $username, "",
                $id,
                $dataToBeInsertedDaxilOlanSened['yoxlayan_shexs'],
                "prodoc_yoxlamaya_gonderilib",
                "",
                "",
                "",
                $document_number[0],
                "daxil_olan_sened",
                "yoxlama"
            );
        }
        else if ($dataToBeInsertedDaxilOlanSened['rey_muellifi'] > 0)
        {
            $sessionUserInfo = $user->getUserInfo();
            $username = $user->tmzle($sessionUserInfo['user_name']);

            $user->sendNotifications( true, true,
                'prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra',
                $username, "",
                $id,
                $dataToBeInsertedDaxilOlanSened['rey_muellifi'],
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

    pdo()->commit();
    $responseArr=array('sened_id'=>$id);
    $user->success_msg("Ok!",$responseArr);

} catch (RepeatOrSameDocumentException $e) {
    print json_encode([
        'status' => 'error',
        'errors' => [$e->getMessage()]
    ]);
} catch (Exception $e) {
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var!']
    ]);
} finally {
    pdo()->rollBack();
}

function getSenderId($muraciet_eden)
{

    global $user;

    if (trim($muraciet_eden) === "") {
        return NULL;
    }

    $arr = explode(' ', $muraciet_eden);

    return DB::insertAndReturnId('tb_Customers',
                    [
                        'Adi'      => $arr[0],
                        'Soyadi'   => (isset($arr[1]) ? $arr[1] : ''),
                        'AtaAdi'   => (isset($arr[2]) ? $arr[2] : ''),
                        'TenantId' => $user->getActiveTenantId()
                    ]
                );
}

function setInsert($phones, $id)
{
    foreach ($phones as $ophone){
        if($ophone!="") {
            SQL::insert('tb_daxil_olan_senedler_fiziki_ophone', [
                'sened_id' => $id,
                'ophone'   => $ophone
            ]);
        }
    }
}