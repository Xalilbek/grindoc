<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 29.06.2018
 * Time: 10:43
 */
session_start();
include_once '../../../class/class.functions.php';

$user = new User();
if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$id = getRequiredPositiveInt('id');

$incomingDocumentInfo = null;

$sql = "
    SELECT
    tb1.*,
    
    tb3.muraciet_eden,
    tb3.muraciet_eden_ad,
    tb3.unvan,
    tb3.region,
    tb3.region_ad,
    tb3.telefon,
    tb3.shexsiyyet_vesiqesi_teqdim_edilmeyib,
    tb3.shexsiyyet_vesiqesi_seria,
    tb3.shexsiyyet_vesiqesi_pin_kod,
    tb3.hardan_daxil_olub,
    tb3.hardan_daxil_olub_ad,
    tb3.movzu,
    tb3.movzu_ad,

    tb2.muraciet_tip_ad,
    tb2.muraciet_tip_id
    FROM v_daxil_olan_senedler  AS tb1
    LEFT JOIN v_chixan_senedler AS tb2 ON tb2.id = tb1.outgoing_document_id
    LEFT JOIN v_daxil_olan_senedler_fiziki AS tb3 ON tb3.daxil_olan_sened_id = tb1.id
";

$incomingDocument = new Document($id, $sql);
$incomingDocumentInfo = $incomingDocument->getInfo();

$incomingDocumentInfo["senedin_daxil_olma_tarixi"] = is_null($incomingDocumentInfo["senedin_daxil_olma_tarixi"]) ? '' : date('d-m-Y H:i', strtotime($incomingDocumentInfo["senedin_daxil_olma_tarixi"]));
$incomingDocumentInfo["senedin_tarixi"] = is_null($incomingDocumentInfo["senedin_tarixi"]) ? '' : date('d-m-Y H:i', strtotime($incomingDocumentInfo["senedin_tarixi"]));

$incomingDocumentInfo["gonderen_teshkilat"] = [
    "id" => $incomingDocumentInfo["gonderen_teshkilat"],
    "text" => $incomingDocumentInfo["gonderen_teshkilat_ad"]
];

$incomingDocumentInfo["gonderen_shexs"] = [
    "id" => $incomingDocumentInfo["gonderen_shexs"],
    "text" => $incomingDocumentInfo["gonderen_shexs_ad"]
];

$incomingDocumentInfo["mektubun_tipi"] = [
    "id" => $incomingDocumentInfo["mektubun_tipi"],
    "text" => $incomingDocumentInfo["mektubun_tipi_ad"]
];

$incomingDocumentInfo["mektubun_alt_tipi"] = [
    "id" => $incomingDocumentInfo["mektubun_alt_tipi"],
    "text" => $incomingDocumentInfo["mektubun_alt_tipi_ad"]
];

$incomingDocumentInfo["muraciet_eden"] = [
    "id" => $incomingDocumentInfo["muraciet_eden"],
    "text" => $incomingDocumentInfo["muraciet_eden_ad"]
];

$incomingDocumentInfo["hardan_daxil_olub"] = [
    "id" => $incomingDocumentInfo["hardan_daxil_olub"],
    "text" => $incomingDocumentInfo["hardan_daxil_olub_ad"]
];

$incomingDocumentInfo["movzu"] = [
    "id" => $incomingDocumentInfo["movzu"],
    "text" => $incomingDocumentInfo["movzu_ad"]
];

$incomingDocumentInfo["region"] = [
    "id" => $incomingDocumentInfo["region"],
    "text" => $incomingDocumentInfo["region_ad"]
];

$incomingDocumentInfo["muraciet_tip_id"] = [
    "id" => $incomingDocumentInfo["muraciet_tip_id"],
    "text" => $incomingDocumentInfo["muraciet_tip_ad"]
];

$incomingDocumentInfo["icra_edilme_tarixi"] = is_null($incomingDocumentInfo["icra_edilme_tarixi"]) ? '' : date('d-m-Y H:i', strtotime($incomingDocumentInfo["icra_edilme_tarixi"]));

print json_encode($incomingDocumentInfo);