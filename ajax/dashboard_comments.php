<?php
include_once '../../class/class.functions.php';


$Id = isset($parametrler['id'])?$parametrler['id']:'';
$senedTipi = isset($parametrler['senedTipi'])?$parametrler['senedTipi']:'';
$sessionUserId             = $_SESSION['erpuserid'];
$elementler = array(
    "Id"        =>$Id,
    "senedTipi" =>$senedTipi,
    "sId"       =>$sessionUserId
);

print json_encode(array(
    "status" => "hazir",
    "template" => render('dashboard_comments', $elementler, 'prodoc')
));



