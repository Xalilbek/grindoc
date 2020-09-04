<?php
include_once DIRNAME_INDEX  . '/class/class.grouparray.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       SELECT
           v_daxil_olan_senedler.id as sened_id,
            ttt.user_name,
            ttt.vezife,
            tb2.*
        FROM
            v_daxil_olan_senedler
            LEFT JOIN tb_proid_employe_petition as tb2 ON v_daxil_olan_senedler.id= tb2.document_id 
            LEFT JOIN v_users AS ttt ON
             ttt.USERID = tb2.employe
        WHERE tb2.id = $sid
    ";

    $poa = DB::fetch($sql);

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/ishe_qebul_erizesi/ishe_qebul_erizesi.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}