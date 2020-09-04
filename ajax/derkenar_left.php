<?php
session_start();
include_once '../../class/class.functions.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];

try {

    $derkenarYoxla = DB::fetchColumn("SELECT config FROM tb_prodoc_user_config WHERE user_id = {$userId}");
    $derkenarUnser = unserialize($derkenarYoxla);

    $derkenarUnser['derkenar_left'] =  isset($_POST['tab']) ? $_POST['tab'] : 'none';
    if($derkenarYoxla)
    {
        DB::update('tb_prodoc_user_config', [
            'config' => serialize($derkenarUnser)
        ], $userId, 'user_id');
    }
    else
    {
        DB::insert('tb_prodoc_user_config', [
            'user_id' => $userId,
            'config' => serialize( $derkenarUnser)
        ]);
    }

    $user->success_msg();
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}
