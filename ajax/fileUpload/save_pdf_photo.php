<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';

$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}
$userId = (int)$_SESSION['erpuserid'];

$fields= [
    [
        "InputType" => "id",
        "ColumnName" => "id"
    ],
    [
        "Title" => "Sənəd",
        "InputType" => "file",
        "ColumnName" => "sened_fayl"
    ]
];


try{
    $form = new Form($fields);

    $form->check();
    $dataToBeInserted = $form->collectDataToBeInserted();

    $form = saveFiles('sened_fayl', PRODOC_FILES_SAVE_PATH.'/pdf_background/',true,'template.png');

    print json_encode([
        'status' => 'success',
        'id' => $dataToBeInserted['id']
    ]);
    exit();
}
catch (Exception $e){
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var' . $e->getMessage()]
    ]);
}
