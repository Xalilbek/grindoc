<?php
session_start();
include_once '../../../../class/class.functions.php';
$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

if(isset($_POST['fileId'])&&$_POST['fileId']>0 ){
    try{
        $fileId=(int)$_POST['fileId'];
        $file = DB::query("DELETE FROM tb_files where id=".$fileId);

        print json_encode(array("status" => "success"));
    }catch (Exception $e){
        print json_encode(array("status" => "fail"));
    }


}
else{
    print json_encode(array("status" => "fail"));
}