<?php
include_once '../../../class/class.functions.php';
session_start();

$user = new User();
if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}
use Gears\Pdf;
//use PhpOffice\PhpWord\PhpWord;

if(isset($_GET['file_id'])&& (int)$_GET['file_id']>0){
    $inserted_files = DB::fetch('SELECT id, file_actual_name FROM tb_files WHERE id = '.(int)$_GET['file_id']);
    $pdfPhp = new Pdf(UPLOADS_DIR_PATH. 'prodoc/'.$inserted_files['file_actual_name']);
    $pdfPhp->stream();
}
