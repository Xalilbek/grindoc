<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 28.08.2018
 * Time: 17:17
 */

use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;

$f = [
    [
        "IsRequired" => true,
        "InputType" => "text",
        "ColumnName" => "document_number",
        "Title" => "Sənədin №-si"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "editable_with_select"
    ],
];

$daxilOlanSenedForm = new Form($f);
$daxilOlanSenedForm->check();
$dataToBeInsertedDaxilOlanSened = $daxilOlanSenedForm->collectDataToBeInserted();

require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
$class = $testiqleme['related_class'];

$i = new $class($testiqleme['related_record_id']);
if ($i instanceof Document) {
    $i->setCustomQuery('v_incoming_document_all', true);
}

$documentNumberGeneral = new DocumentNumberGeneral($i, [
    'manualDocumentNumber' => $dataToBeInsertedDaxilOlanSened['document_number'],
    'editable_with_select' => $dataToBeInsertedDaxilOlanSened['editable_with_select']
]);
$documentNumberGeneral->assignNumber();