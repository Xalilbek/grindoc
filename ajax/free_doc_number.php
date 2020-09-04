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

$id = getInt('id');

$doc_number = DB::fetchById('tb_prodoc_document_number', $id);

$canFreeNumber =
    (int)$doc_number['reserved_by'] === (int)$_SESSION['erpuserid'] &&
    (int)$doc_number['reservation_status'] === DocumentNumberGeneral::RESERVATION_STATUS_RESERVED
;

if (
    false === $canFreeNumber
) {
    throw new Exception('Access error');
}

DB::update('tb_prodoc_document_number', [
    'reservation_status' => DocumentNumberGeneral::RESERVATION_STATUS_FREE
], $id);

$user->success_msg();