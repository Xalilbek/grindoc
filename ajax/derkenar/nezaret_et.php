<?php
session_start();
include_once '../../../class/class.functions.php';
$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$Id = getRequiredPositiveInt('id');

$nezaret = DB::fetchColumn('SELECT mektub_nezaretdedir FROM tb_daxil_olan_senedler WHERE id = '.$Id);
$key = is_null($nezaret) ? 1 : NULL;

DB::update('tb_daxil_olan_senedler', [
    'mektub_nezaretdedir' => $key
], $Id);

print json_encode(['error' => $key]);

