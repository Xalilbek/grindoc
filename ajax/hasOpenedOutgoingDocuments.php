<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 02.06.2018
 * Time: 12:17
 */
require_once '../../class/class.functions.php';
require_once DIRNAME_INDEX . '/prodoc/model/Document.php';

session_start();

$user = new User();
if(!$user->get_session())
{
    print json_encode(array("daxil_olmayib"));
    exit;
}

$daxil_olan_sened_id = getRequiredPositiveInt('daxil_olan_sened_id');

$iDoc = new Document($daxil_olan_sened_id);

print (int)Appeal::hasOpenedOutgoingDocuments($iDoc);