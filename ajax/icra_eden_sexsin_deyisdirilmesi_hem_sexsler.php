<?php
session_start();
include_once '../../class/class.functions.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$tasks_id = getRequiredPositiveInt('tasks_id');

try {

    $mesul_shexs = DB::fetchColumn("SELECT  mesul_shexs_ad FROM v_derkenar WHERE id = '$tasks_id'");

    $sql = "SELECT
					v_user_adlar.user_ad
				FROM
					v_derkenar_elave_shexsler as tb2
				LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.user_id
				WHERE
					tb2.derkenar_id = {$tasks_id} AND tb2.tip = 'ishtrakchi'
			";

    $istirakchi_shexs = DB::fetchAll(sprintf($sql));

    print json_encode(array("istirakchi" => $istirakchi_shexs, "mesul_shexs" => $mesul_shexs));

} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

