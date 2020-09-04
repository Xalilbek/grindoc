<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . '/modules/module_builder/model.php';
require_once DIRNAME_INDEX . '/prodoc/component/Form.php';

$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];

$testiqleme_id = get('id');

try {

    DB::beginTransaction();
    $testiqleme = DB::fetchById('tb_prodoc_testiqleyecek_shexs', $testiqleme_id);

    $nextOrder = null;
    $outgoingDocId = 0;
    $currentOrder = null;
    if ('OutgoingDocument' === $testiqleme['related_class'] || 'Appeal' === $testiqleme['related_class']) {
        $outgoingDocId = $testiqleme['related_record_id'];
        $outgoingDoc = new $testiqleme['related_class']($outgoingDocId);
        $confirmation = new \Service\Confirmation\Confirmation($outgoingDoc);
        $currentOrder = $confirmation->getCurrentOrder();
    }


    $testiqleyecekShexs = new TestiqleyecekShexs($testiqleme_id);
    $additionalFileForType = 'testiq_' . $testiqleme['tip'] . '.php';
    if (file_exists($additionalFileForType)) {
        require_once $additionalFileForType;
    }

    $testiqleyecekShexs->testiqle(['note' => get('note')]);

    if (isset($confirmation)) {
        $nextOrder = $confirmation->getCurrentOrder();
    }

    $mesul_shexsler = pdof()->query("SELECT t1.*, t2.outgoing_document_id, (Select mesul_shexs from tb_derkenar
             WHERE id=(SELECT parentTaskId FROM tb_derkenar WHERE id=derkenar_id and parentTaskId>0 ) ) as mesul_shexs,
            (
                SELECT v_daxil_olan_senedler.created_by
                FROM v_daxil_olan_senedler
                WHERE id = daxil_olan_sened_id
            ) as qeydiyyatci,
            	( SELECT rey_muellifi FROM v_daxil_olan_senedler WHERE id = daxil_olan_sened_id ) AS rey_muellifi,
            	   	( SELECT rey_muellifi_ad FROM v_daxil_olan_senedler WHERE id = daxil_olan_sened_id ) AS rey_muellifi_ad,
            	   	(SELECT document_number from v_daxil_olan_senedler WHERE id= t1.daxil_olan_sened_id) as daxil_olan_number,
            	   	(SELECT teyinat FROM v_chixan_senedler  WHERE id=outgoing_document_id) as teyinat,
            (SELECT CONCAT(Adi,' ' ,Soyadi) from tb_users where USERID=(Select mesul_shexs from tb_derkenar
            WHERE id=(SELECT parentTaskId FROM tb_derkenar WHERE id=derkenar_id and parentTaskId>0 ) ))
             as mesul_shexs_ad  FROM
            tb_prodoc_muraciet AS t1 LEFT JOIN tb_prodoc_appeal_outgoing_document AS t2 ON t1.id = t2.appeal_id
            WHERE outgoing_document_id='".$outgoingDocId."' ORDER BY outgoing_document_id desc  ")->fetch();
    $tip="";

    if ('OutgoingDocument' === $testiqleme['related_class']||'Appeal' === $testiqleme['related_class']) {

        if ($nextOrder !== $currentOrder && null !== $nextOrder) {
                ///$confirmation->getCurrentApprovingUsers();

            $sql = "
                SELECT 
                    user_id,
                    tip,
                    document_number,
                    (
                        SELECT CONCAT(Adi,' ' ,Soyadi) as user_name FROM tb_users WHERE USERID = v_chixan_senedler.created_by
                    ) as user_name,
                related_record_id, teyinat 
                FROM tb_prodoc_testiqleyecek_shexs 
                LEFT JOIN v_chixan_senedler on
                v_chixan_senedler.id=related_record_id  WHERE [order] = {$nextOrder} AND related_class = '".$testiqleme['related_class']."' AND related_record_id = '$outgoingDocId'
            ";

            $users = DB::fetchAll($sql);

            foreach ($users as $tUser) {

                if (($tUser['tip']=='mesul_shexs'&&$mesul_shexsler['mesul_shexs']!=0&&$mesul_shexsler['mesul_shexs']!=''&&$mesul_shexsler['mesul_shexs']!=null)||($tUser['tip']==TestiqleyecekShexs::TIP_REY_MUELIFI)){
                    $gonderilecek_shexs=0;
                    switch ($tUser['tip']){
                        case TestiqleyecekShexs::TIP_MESUL_SHEXS:$gonderilecek_shexs=$mesul_shexsler['mesul_shexs'];
                            break;
                        case TestiqleyecekShexs::TIP_REY_MUELIFI:$gonderilecek_shexs=$mesul_shexsler['rey_muellifi'];
                            break;
                    }



                }

                else if($tUser['tip']!="rey_muelifi"){


                }

                if ($tUser['tip']==TestiqleyecekShexs::TIP_MESUL_SHEXS&&$mesul_shexsler['mesul_shexs']!=0&&$mesul_shexsler['mesul_shexs']!=''&&$mesul_shexsler['mesul_shexs']!=null){

                        $user->sendNotifications( true, true,
                            "ishe_tik",
                            $tUser['user_name'], "",
                            $tUser['related_record_id'],
                            $mesul_shexsler['mesul_shexs'],
                            "ishe_tik",
                            "",
                            "",
                            "",
                            $tUser['document_number'],
                            "daxil_olan_sened",
                            "ishe_tik"
                        );


                    $user->sendNotifications( true, true,
                        "ishe_tik_qeydiyyatci",
                        $tUser['user_name'], $mesul_shexsler['mesul_shexs_ad'],
                        $tUser['related_record_id'],
                        $mesul_shexsler['qeydiyyatci'],
                        "ishe_tik_qeydiyyatci",
                        "",
                        "",
                        "",
                        $tUser['document_number'],
                        "daxil_olan_sened",
                        "ishe_tik"
                    );
                }if ((($tUser['tip']==TestiqleyecekShexs::TIP_MESUL_SHEXS&&$mesul_shexsler['mesul_shexs']!=0&&$mesul_shexsler['mesul_shexs']!=''&&$mesul_shexsler['mesul_shexs']!=null)||
                    ($tUser['tip']==TestiqleyecekShexs::TIP_REY_MUELIFI))&&'Appeal' === $testiqleme['related_class']){
                    $gonderilecek_shexs=0;
                    switch ($tUser['tip']){
                        case TestiqleyecekShexs::TIP_MESUL_SHEXS:$gonderilecek_shexs=$mesul_shexsler['mesul_shexs'];
                        break;
                        case TestiqleyecekShexs::TIP_REY_MUELIFI:$gonderilecek_shexs=$mesul_shexsler['rey_muellifi'];
                        break;
                            }
                        $user->sendNotifications( true, true,
                            "ishe_tik",
                            $tUser['user_name'], "",
                            $tUser['related_record_id'],
                            $gonderilecek_shexs,
                            "ishe_tik",
                            "",
                            "",
                            "",
                            $tUser['document_number'],
                            "daxil_olan_sened",
                            "derkenar"
                        );


                    $user->sendNotifications( true, true,
                        "ishe_tik_qeydiyyatci",
                        $tUser['user_name'], $mesul_shexsler['mesul_shexs_ad'],
                        $tUser['related_record_id'],
                        $mesul_shexsler['qeydiyyatci'],
                        "ishe_tik_qeydiyyatci",
                        "",
                        "",
                        "",
                        $tUser['document_number'],
                        "daxil_olan_sened",
                        "derkenar"
                    );
                }


            }

        }
    }
    elseif ('Appeal' === $testiqleme['related_class']){

    }

    notify();

    DB::commit();
    $user->success_msg();
} catch (Exception $e) {
    DB::rollBack();
    $user->error_msg($e->getMessage());
}


function notify()
{
    global $user, $testiqleme, $mesul_shexsler, $tip;

    if($testiqleme['tip']=='rey_muelifi'){
        switch ($mesul_shexsler['teyinat'])
        {
            case '3': $tip.="aidiyyati_orqan_rey";break;
            case '4': $tip.="fiziki_shexs_rey";break;
            case '5': $tip.="tabeli_qurum_rey";break;
            default: return; break;
        }
//        $user->sendNotifications( true, true,
//            $tip,
//            $mesul_shexsler['rey_muellifi_ad'], "",
//            $mesul_shexsler['daxil_olan_sened_id'],
//            $mesul_shexsler['rey_muellifi'],
//            $tip,
//            "",
//            "",
//            "",
//            $mesul_shexsler['daxil_olan_number'],
//            "daxil_olan_sened",
//            "derkenar"
//        );
//
//        $user->sendNotifications( true, true,
//            $tip,
//            $mesul_shexsler['rey_muellifi_ad'], $mesul_shexsler['mesul_shexs_ad'],
//            $mesul_shexsler['daxil_olan_sened_id'],
//            $mesul_shexsler['qeydiyyatci'],
//            $tip,
//            "",
//            "",
//            "",
//            $mesul_shexsler['daxil_olan_number'],
//            "daxil_olan_sened",
//            "derkenar"
//        );
    }
}
