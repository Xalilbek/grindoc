<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 28.08.2018
 * Time: 17:17
 */

$form = [
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "netice",
        "Title" => "Nəticə"
    ]
];

$daxilOlanSenedForm = new Form($form);
$daxilOlanSenedForm->check();
$dataToBeInsertedDaxilOlanSened = $daxilOlanSenedForm->collectDataToBeInserted();

$id = $testiqleme['related_record_id'];
DB::update('tb_daxil_olan_senedler', [
    'netice' => $dataToBeInsertedDaxilOlanSened['netice']
], $id);