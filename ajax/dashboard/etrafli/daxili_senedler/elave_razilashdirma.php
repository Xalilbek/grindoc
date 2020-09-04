<?php
include_once DIRNAME_INDEX  . '/class/class.grouparray.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       SELECT
           v_daxil_olan_senedler.id as sened_id,
            ( CASE tb2.sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' ELSE N'Qeyd olunmayıb' END ) AS senedin_tipi,
            ( SELECT name FROM tb_prodoc_nazalogiya WHERE id = tb2.qisa_mezmun ) AS qisa_mezmun,
            rey_muellifi_ad,
            ttt.user_name,
            ttt.vezife,
            v.ad AS emeqhaqqina_elave_valyuta_ad,
            tb2.*
        FROM
            v_daxil_olan_senedler
            LEFT JOIN tb_prodoc_elave_razilashdirma as tb2 ON v_daxil_olan_senedler.id= tb2.document_id 
            LEFT JOIN v_users AS ttt ON
             ttt.USERID = tb2.emekdash
             LEFT JOIN tb_valyuta As v
             ON v.id = tb2.emeqhaqqina_elave_valyuta
        WHERE tb2.id = $sid
    ";

    $poa = DB::fetch($sql);

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/elave_razilashdirma/elave_razilashdirma.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}