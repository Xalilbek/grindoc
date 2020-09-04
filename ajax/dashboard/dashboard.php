<?php
session_start();
include_once '../../../class/class.functions.php';
$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit;
}

if( isset($_POST['tab']) && is_string($_POST['tab']) && $_POST['tab']!="" )
{
    $tab = $user->tmzle($_POST['tab']);
    $senedler = array();
    $html = '';

    if (isset($_POST['sened_id']) && is_numeric($_POST['sened_id']) && (int)$_POST['sened_id'] > 0)
    {
        $sened_id = (int)$_POST['sened_id'];

        $rey_mellifi_id = DB::fetchColumn("SELECT rey_muellifi FROM tb_daxil_olan_senedler2 WHERE id = '$sened_id'");
        $html = DB::fetchColumn("SELECT CONCAT(Adi, ' ', Soyadi, ' ', AtaAdi) AS user_ad FROM tb_users WHERE USERID='$rey_mellifi_id'");
        $html = $html === false ? '' : $html;
    }
    else {
        if ($tab === 'butun_senedler') {
            $senedler = DB::fetchAll("SELECT *, N'Fiziki şəxs' AS nov FROM tb_daxil_olan_senedler2 ORDER BY qeydiyyat_tarixi DESC");
        } else if ($tab === 'yoxlama') {
            $senedler = DB::fetchAll("SELECT *, N'Fiziki şəxs' AS nov FROM tb_daxil_olan_senedler2 WHERE son_emeliyyat = '".YOXLAMADA."' ORDER BY qeydiyyat_tarixi DESC");
        } else if ($tab === 'yeni') {
            $senedler = DB::fetchAll("SELECT *, N'Fiziki şəxs' AS nov FROM tb_daxil_olan_senedler2 WHERE son_emeliyyat = '".SENED_QEYDIYYATDAN_KECIB."' ORDER BY qeydiyyat_tarixi DESC");
        }

        foreach ($senedler as $sened) {
            $html .= "<tr class='clickable-row' sened-tab='" . escape($tab) . "' sened-id='" . escape($sened['id']) . "'>";
            $html .= "    <td>" . escape($sened['id']) . "</td>";
            $html .= "    <td><input type='checkbox'></td>";
            $html .= "    <td>" . escape($sened['gonderme_indeksi']) . "</td>";
            $html .= "    <td>" . escape($sened['qeydiyyat_tarixi']) . "</td>";
            $html .= "    <td>" . escape($sened['nov']) . "</td>";
            $html .= "</tr>";
        }
    }

    print json_encode(array("status" => "success", "html" => $html ));
}
else {
    print json_encode(array("status" => "failed", "message" => "Bu adda əməliyyat yoxdur"));
}