<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 17.05.2018
 * Time: 16:42
 */
session_start();

require_once '../../class/class.functions.php';
require_once DIRNAME_INDEX . 'modules/module_builder/model.php';
require_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';

use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;

$user = new User();

if(!$user->get_session())
{
    header("Location: login.php");
    exit;
}

$id = getRequiredPositiveInt('id');

$document = new Document($id);
$document->setCustomQuery('v_incoming_document_all', true);

if (is_null($document)) {
    $user->error_msg();
}

$documentNumberGeneral = new DocumentNumberGeneral($document);

try {
    print json_encode([
        'number'  => $documentNumberGeneral->getCurrentDocumentNumber(),
        'setting' => $documentNumberGeneral->getSetting()->getInfo()
    ]);
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}