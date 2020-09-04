<?php
session_start();
include_once '../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';

$user = new User();

$userId = $_SESSION['erpuserid'];

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$fields = [
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "muraciet_tip_id"
    ],
];

$f = new Form($fields);
$f->check();
$formData = $f->collectDataToBeInserted();

require_once DIRNAME_INDEX . 'prodoc/model/AppealType.php';
$resultStatus = AppealType::hasSelectableStatusType($formData['muraciet_tip_id']);
$resultConnecting = AppealType::hasSelectableConnectingType($formData['muraciet_tip_id']);
$result = [ 'status' => $resultStatus ? 'show' : 'hide' , 'elaqelendirme' => $resultConnecting ? 'show' : 'hide' ];
$user->success_msg($result);