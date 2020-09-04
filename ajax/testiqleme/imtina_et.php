<?php

use History\History;

session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];

$testiqleme_id = get('id');
$sened_id = get('sened_id');
$sebeb = get('sebeb');

try {
    $testiqleyecekShexs = new TestiqleyecekShexs($testiqleme_id);
    $testiqleyecekShexs->imtinaEt(['note' => get('sebeb')]);
    $userType = $testiqleyecekShexs->getInfo()['tip'];

    $lastHistoryId = History::getLastInsertedId();

    if (isset($_FILES['sened'])) {
        $files = saveFiles('sened', PRODOC_FILES_SAVE_PATH, false);

        for ($j = 0, $lenj = count($files); $j < $lenj; ++$j) {

            SQL::insert('tb_files', [
                'module_name' => 'history_sened',
                'module_entry_id' => $lastHistoryId,
                'file_original_name' => $files[$j]['file_original_name'],
                'file_actual_name' => $files[$j]['file_actual_name'],
            ]);
        }
    }

    $tUser= pdof()->query("Select tb_prodoc_testiqleyecek_shexs.*, document_number, (SELECT CONCAT(Adi,' ' ,Soyadi) as user_name FROM tb_users WHERE USERID=user_id) as user_name , rey_muellifi, tb_derkenar.mesul_shexs  FROM tb_prodoc_testiqleyecek_shexs LEFT JOIN (SELECT t1.derkenar_id, t1.daxil_olan_sened_id, t2.outgoing_document_id  FROM
tb_prodoc_muraciet AS t1
LEFT JOIN tb_prodoc_appeal_outgoing_document AS t2 ON t1.id = t2.appeal_id)dd on tb_prodoc_testiqleyecek_shexs.related_record_id = outgoing_document_id
LEFT JOIN v_daxil_olan_senedler on v_daxil_olan_senedler.id=daxil_olan_sened_id LEFT JOIN tb_derkenar  on derkenar_id=tb_derkenar.id WHERE tb_prodoc_testiqleyecek_shexs.id=".$testiqleme_id)->fetch();

    if ($tUser['rey_muellifi']!=''&&$tUser['rey_muellifi']!=null&& $tUser['rey_muellifi']!=0 &&$tUser['rey_muellifi']>0){

//        $user->sendNotifications( true, true,
//            $tUser['tip']."_imtina",
//            $tUser['user_name'], "",
//            $tUser['related_record_id'],
//            $tUser['rey_muellifi'],
//            $tUser['tip']."_imtina",
//            "",
//            "",
//            "",
//            $tUser['document_number'],
//            "xaric_olan_sened",
//            "imtina"
//        );
    }

    if ($tUser['mesul_shexs']!=''&&$tUser['mesul_shexs']!=null&& $tUser['mesul_shexs']!=0 &&$tUser['mesul_shexs']>0){

//        $user->sendNotifications( true, true,
//            $tUser['tip']."_imtina",
//            $tUser['user_name'], "",
//            $tUser['related_record_id'],
//            $tUser['mesul_shexs'],
//            $tUser['tip']."_imtina",
//            "",
//            "",
//            "",
//            $tUser['document_number'],
//            "xaric_olan_sened",
//            "imtina"
//        );
    }
    $user->success_msg();
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

