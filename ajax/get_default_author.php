<?php
session_start();
include_once '../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';

$user = new User();

$type = getInt('type');
$documentKey = get('documentKey');
$userId = $_SESSION['erpuserid'];

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$documentKey = DB::quote($documentKey);

$sql = sprintf("
    SELECT
        tb_default_prodoc_executor.executor_id AS executor_id,
        v_user_adlar.user_ad AS executor_name
    FROM tb_default_prodoc_executor
    LEFT JOIN v_user_adlar
     ON tb_default_prodoc_executor.executor_id = v_user_adlar.USERID 
    WHERE doc_type_id = (
        SELECT TOP 1 id FROM tb_sened_novleri
        WHERE forma_ad = $documentKey AND TenantId = %s
    ) AND tb_default_prodoc_executor.executor_type = $type
", $user->getActiveTenantId());

$executor = DB::fetch($sql);

$data = [];

if (FALSE !== $executor) {
    $data['author'] = [
        'id' => $executor['executor_id'],
        'text' => $executor['executor_name']
    ];
}

print json_encode($data);