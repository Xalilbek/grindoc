<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$chixan_sened_id = getRequiredPositiveInt('id');

try {
    $chixanSened = new OutgoingDocument($chixan_sened_id);
    $chixanSened->setSend();

    $user->success_msg();
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

