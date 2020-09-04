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
$id     = getInt('fileID');

try {

    DB::query("Update tb_files SET is_deleted = 1 WHERE id = '$id' AND created_by = '$userId'");

    $user->success_msg();
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

