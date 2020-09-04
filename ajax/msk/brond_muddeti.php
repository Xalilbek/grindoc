<?php
session_start();
include_once '../../../class/class.functions.php';

$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

$deqiqe    = get('deqiqe');

if(isset($deqiqe)) {
    DB::query("UPDATE tb_options SET value = '$deqiqe' WHERE option_name = 'brond_muddet'");
    $user->success_msg();
}