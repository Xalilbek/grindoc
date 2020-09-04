<?php
session_start();
include_once '../../class/class.functions.php';
$user = new User();

if(!$user->get_session())
{
    header("Location: login.php");
    exit;
}

if(isset($_POST['qrkod'])){

    $qrkod = DB::quote($_POST['qrkod']);

    $qr_update_query = pdof()->query("UPDATE tb_options SET value=N$qrkod WHERE option_name='qr_code_shablon'");

    $qr_select_query = DB::fetch("SELECT * FROM tb_options WHERE option_name='qr_code_shablon'");


    if($qr_update_query){
        $result = array("qrkod"=> $qr_select_query['value']);
    }
    else{
        $result = array("qrkod"=>"Kod tapilmadi");
    }


    echo json_encode($result);

}


?>