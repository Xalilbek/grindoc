<?php
session_start();
include_once '../../../../class/class.functions.php';
include_once DIRNAME_INDEX . '/prodoc/model/Task/Task.php';
$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

if (isset($_POST['outgoingDocumentId'])) {
    require_once DIRNAME_INDEX . 'prodoc/includes/outgoing_document.php';
    $_POST['sened_id'] = xosToDos((int)$_POST['outgoingDocumentId'], true);
}

$fromParent = false;

if(!isset($_POST['muracietId'])) {
    $daxil_olan_sened_id = getRequiredPositiveInt('sened_id');

    $daxil_olan_sened = DB::fetch("SELECT rey_muellifi_ad, icra_edilme_tarixi, sened_tip  FROM v_daxil_olan_senedler WHERE id = '$daxil_olan_sened_id'");

    $sql = "
        SELECT 
        STUFF((SELECT CONCAT(', ', v_user_adlar.user_ad) 
          FROM tb_derkenar_elave_shexsler as tb4
	      LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb4.user_id
          WHERE tb4.derkenar_id = tb1.id  AND tip = 'kurator' FOR XML PATH('')),1,1,'') as icraya_nezaret_eden_shexs,
          STUFF((SELECT CONCAT(', ', v_user_adlar.user_ad) 
          FROM tb_derkenar_elave_shexsler as tb4
	      LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb4.user_id
          WHERE tb4.derkenar_id = tb1.id  AND tip = 'ishtrakchi' FOR XML PATH('')),1,1,'') as hemIcraci,
          STUFF((SELECT CONCAT(', ', v_user_adlar.user_ad) 
          FROM tb_derkenar_elave_shexsler as tb4
	      LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb4.user_id
          WHERE tb4.derkenar_id = tb1.id  AND tip = 'melumat' FOR XML PATH('')),1,1,'') as melumatlandiran_shexs,
        tb1.*,
        tb3.tip,
        tb3.related_document_id,
        tb1.id AS muraciet_id, 
        (SELECT mesul_shexs_ad FROM v_derkenar WHERE id=tb1.parentTaskId ) as mesul_icraci,
        (SELECT id FROM v_derkenar WHERE parentTaskId=tb1.id ) as parentTaskID
        FROM v_derkenar tb1
        OUTER APPLY (
          SELECT TOP 1 tip, id, related_document_id  FROM tb_prodoc_muraciet WHERE derkenar_id = tb1.id
          ORDER BY created_at DESC
        ) AS tb3
        WHERE tb1.daxil_olan_sened_id = $daxil_olan_sened_id AND (tb1.parentTaskId = 0 OR tb1.parentTaskId IS NULL)
    ";


    $derkenarlar = DB::fetchAll($sql);

    ob_start();
    require_once DIRNAME_INDEX . 'prodoc/templates/default/dashboard/derkenarlar.php';
    $html = ob_get_clean();

    print json_encode(array("status" => "success", "html" => $html));
}
if (isset($_POST['muracietId']) && (int)$_POST['muracietId'] > 0)
{
    $fromParent = true;
    $derkenar_id = (int)$_POST['muracietId'];


    $outgoingDocuments = DB::fetchAll("SELECT
                                              M.id,
                                              M.tip,
                                              CASE WHEN M.tip = 'ishe_tik' THEN DOS.document_number ELSE C.document_number END AS document_number,
                                              CASE WHEN M.tip = 'ishe_tik' THEN DOS.created_at ELSE C.created_at END AS created_at,
                                              C.teyinat_ad
                                            FROM tb_prodoc_muraciet AS M
                                            LEFT JOIN tb_prodoc_appeal_outgoing_document AS OD
                                              ON M.tip = 'sened_hazirla' AND M.id = OD.appeal_id
                                            LEFT JOIN v_chixan_senedler AS C
                                              ON C.id = OD.outgoing_document_id
                                            LEFT JOIN tb_daxil_olan_senedler AS DOS
                                              ON DOS.id = M.related_document_id AND M.tip = 'ishe_tik' AND M.related_document_id > 0
                                            WHERE M.derkenar_id = '$derkenar_id'");

    ob_start();
    require DIRNAME_INDEX . 'prodoc/templates/default/dashboard/xos_hazirlanib.php';
    $html1 = ob_get_clean();


    $sql = "
        SELECT
          STUFF((SELECT CONCAT(', ', v_user_adlar.user_ad)
          FROM tb_derkenar_elave_shexsler as tb4
	      LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb4.user_id
          WHERE tb4.derkenar_id = tb1.id  AND tip = 'ishtrakchi' FOR XML PATH('')),1,1,'') as hemIcraci,
        tb1.*,
        tb3.tip,
        tb1.id AS muraciet_id,
        (SELECT mesul_shexs_ad FROM v_derkenar WHERE id=tb1.parentTaskId ) as mesul_icraci
        FROM v_derkenar tb1
        OUTER APPLY (
          SELECT TOP 1 tip, id  FROM tb_prodoc_muraciet WHERE derkenar_id = tb1.id
          ORDER BY created_at DESC
        ) AS tb3
        WHERE tb1.parentTaskId = $derkenar_id AND tb1.parentTaskId > 0
    ";

    $alt_derkenarlar = DB::fetchAll($sql);

    ob_start();
    require_once DIRNAME_INDEX . 'prodoc/templates/default/dashboard/alt_derkenar.php';
    $html2 = ob_get_clean();

    print json_encode(array("status" => "success", "html" => $html1 . $html2));


}