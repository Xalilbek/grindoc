<?php
include_once '../../../class/class.functions.php';
session_start();

$user = new User();
if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

if(isset($_GET['file_id'])&& (int)$_GET['file_id']>0){
    $inserted_files = DB::fetch('SELECT id, file_actual_name FROM tb_files WHERE id = '.(int)$_GET['file_id']);
    $mime = mime_content_type(UPLOADS_DIR_PATH. 'prodoc/'.$inserted_files['file_actual_name']);
    header('Content-type: ' . $mime);
    readfile(UPLOADS_DIR_PATH. 'prodoc/'.$inserted_files['file_actual_name']);
}
