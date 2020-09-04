<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/Setting.php';

use Model\DocumentNumber\DocumentNumberGeneral\Setting;

$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

$pr = (int)$user->checkPrivilegia("msk:msk_prodoc_nomrelenme");
if ($pr !== 2)
{
    print json_encode(array("status"=>"hazir","template"=>htmlspecialchars('<div>Olmaz!</div>',ENT_QUOTES)));
    exit();
}


$id = getRequiredPositiveInt('id');

$formFields = [
    [
        "InputType" => "text",
        "ColumnName" => "initial_number"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "initial_number_for_next_years"
    ],
    [
        "InputType" => "text.date",
        "ColumnName" => "active_from"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "editable"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "editable_with_select"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "repeat_appeal"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "set_number_after_approval"
    ],
];

$form = new Form($formFields);
$form->check();
$formData = $form->collectDataToBeInserted();

if (is_null($formData['active_from'])) {
    $formData['active_from'] = date('Y-m-d');
}

$documentNumberGeneral = new Setting($id);
$documentNumberGeneral->editAdditionalData($formData);

$user->success_msg();