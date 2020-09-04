<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';

$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}
$userId = (int)$_SESSION['erpuserid'];

if (isset($_POST['sil']) && $_POST['sil'] == 1){
    $path =  PRODOC_FILES_SAVE_PATH.'/pdf_background/';
    unlink($path . 'template.png');
    exit;
}