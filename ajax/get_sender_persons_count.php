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

$fullName = get('fullName');

$response = [
    "count" => 0
];

if ($fullName === "") {
    print json_encode($response);
    exit();
}

$sql = "
    SELECT
        COUNT(*)
    FROM
        tb_Customers
    WHERE
        RTRIM(
            CONCAT(Adi, ' ', Soyadi, ' ', AtaAdi)
        ) = N'$fullName'
";

$response['count'] = DB::fetchColumn($sql);
print json_encode($response);