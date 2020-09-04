<?php
session_start();
include_once '../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$sened_id = getRequiredPositiveInt('id');


try {
    $Sened = new Document($sened_id);

    $Sened->delete();




    $user->success_msg();
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

