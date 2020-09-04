<?php

use  View\Helper\Proxy;

include_once DIRNAME_INDEX  . '/class/class.grouparray.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       SELECT
            v_daxil_olan_senedler.id as docId,
            tb_prodoc_task_command.id AS task_command_id,
            v_daxil_olan_senedler.id as sened_id,
            document_id,
            ( CASE sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' ELSE N'Qeyd olunmayıb' END ) AS senedin_tipi,
             status,
            ( SELECT document_number FROM v_prodoc_document_number WHERE id = document_number_id ) AS document_number,
            document_date,
            movzu,
            girish,
            meqsed,
            xususi_qeydler ,
            tb_prodoc_task_command.rey_muellifi as rey_muellifi,
            v_users.user_name AS rey_muellifi_ad
        FROM
            v_daxil_olan_senedler
            LEFT JOIN tb_prodoc_task_command ON v_daxil_olan_senedler.id= document_id 
            LEFT JOIN v_users
             ON tb_prodoc_task_command.rey_muellifi = v_users.USERID
        WHERE tb_prodoc_task_command.id= $sid 
    ";
    $poa = DB::fetch($sql);

    $sql = "
        SELECT
            id,
            ( SELECT Concat ( Adi, ' ', Soyadi ) FROM tb_users WHERE USERID = kime ) AS name ,
            kime,
            son_icra_tarixi,
            derkenar_metn AS derkenar_metn_ad
        FROM
        tb_prodoc_task_command_hesabat_verenler
        WHERE task_command_id=".$poa['task_command_id']. "
        ORDER BY kime
    ";

    $kime = DB::fetchAll($sql, true, PDO::FETCH_ASSOC);

    $melumat = DB::fetchAll("SELECT
                        ( SELECT Concat ( Adi, ' ', Soyadi ) FROM tb_users WHERE USERID = user_id ) AS name , status, user_id
                    FROM
                        tb_prodoc_testiqleyecek_shexs 
                    WHERE
                        tip = 'tanish_ol' AND
                        user_id NOT IN ( SELECT mesul_shexs FROM [dbo].[v_derkenar] WHERE daxil_olan_sened_id = ".$poa['sened_id']." ) 
                        AND related_record_id =".$poa['sened_id']);


    $melumat_shexsler="";
    $countOfquery=count($kime);
    $listOfPrincipals= [];



    foreach ($melumat as $kim){
        $listOfPrincipals[]=$kim['user_id'];
    }
    foreach ($kime as $kim){
        $listOfPrincipals[]=$kim['kime'];
    }
    $listOfPrincipals[] = $poa['rey_muellifi'];



    $proxy = new Proxy( new Document($poa['docId']));
    $proxy->setListOfPrincipals($listOfPrincipals);

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
    $doc = new Document($poa['document_id']);
    $status = $doc->getStatusTitle($poa['status']);
    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/task_command/task_command.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}