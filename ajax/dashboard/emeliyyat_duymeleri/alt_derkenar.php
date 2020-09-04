<?php
session_start();
include_once '../../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

try {

    if(isset($_POST['id']))
    {

        $id = (int)$_POST['id'];
        $executor = isset($_POST['executor'])?(int) $_POST['executor']:0;

        Task::isTaskAlreadyCreated($id,$executor);
    }

    $user->success_msg();
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}