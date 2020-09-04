<?php
include_once '../../../class/class.functions.php';

if(isset($_POST['userId'])&&(int)$_POST['userId']>0){
    $userId = $_POST['userId'];
    $poas = DB::fetchAll("SELECT tb_prodoc_power_of_attorney.*, v_users.user_name , v_daxil_olan_senedler.document_number
                                FROM tb_prodoc_power_of_attorney 
                                LEFT JOIN v_users ON  from_user_id = USERID   
                                LEFT  JOIN v_daxil_olan_senedler ON tb_prodoc_power_of_attorney.document_id = v_daxil_olan_senedler.id
                                WHERE to_user_id  = ".DB::quote($userId));

    if(count($poas)>0)
        print json_encode(array("status" => "ok",'poas'=>$poas));
    else
        print json_encode(array("status" => "failed"));
}
else
    print json_encode(array("status" => "failed"));