<?php

use View\Helper\Proxy;

include_once DIRNAME_INDEX  . '/class/class.grouparray.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/etrafli/son_emeliyyat.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       SELECT
               v_daxil_olan_senedler.id as sened_id, tb2.document_id,
            ( SELECT name FROM tb_prodoc_nazalogiya WHERE id = tb2.qisa_mezmun ) AS qisa_mezmun,
            (SELECT daxil_olan_sened_id FROM tb_derkenar WHERE id = tb2.related_document_id) as deyisdirilme_sened,
            (SELECT user_ad FROM v_user_adlar WHERE USERID = tb2.yeni_icra_eden_sexs ) AS icra_eden_shexs_adi,
            tb2.qeyd,
            tb2.senedin_tarixi,
            tb_prodoc_neticeler.name as neticesi
            v_daxil_olan_senedler.state,
        FROM
            v_daxil_olan_senedler
            LEFT JOIN tb_icra_sexsin_deyisdirilmesi as tb2 ON v_daxil_olan_senedler.id = tb2.document_id 
            LEFT JOIN  tb_prodoc_neticeler on v_daxil_olan_senedler.netice=tb_prodoc_neticeler.id
        WHERE tb2.id = $sid
    ";

    $poa = DB::fetch($sql);

    $deyisdirilmesi_sened = DB::fetchColumn("SELECT document_number FROM v_daxil_olan_senedler WHERE id = {$poa['deyisdirilme_sened']}");

    $hemIcraciShex = [];

    $sql = "SELECT
                v_user_adlar.user_ad,
                v_user_adlar.USERID
            FROM
                tb_icra_sexsin_deyisdirilmesi_hem_icraci_sexsler as tb2
            LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.hemIcraciShexs
            WHERE
                tb2.parent_id = '$sid'";

    $hemIcraciShex = DB::fetchAll(sprintf($sql));


    $sql = "
        SELECT
                v_user_adlar.user_ad,
                v_user_adlar.USERID
            FROM
                tb_prodoc_formlar_tesdiqleme as tb2
            LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.user_id
            WHERE
                emeliyyat_tip = 'viza'
                AND document_id = $sid
    ";

    $getInspectorsOfDocument = DB::fetchAll($sql);

    $incomingDocument = new Document($poa['document_id']);
    $listOfPrincipals = [];

    foreach ($hemIcraciShex as $curator) {
        $listOfPrincipals[] = $curator['USERID'];
    }

    foreach ($getInspectorsOfDocument as $inspector) {
        $listOfPrincipals[] = $inspector['USERID'];
    }

    $proxy = new Proxy($incomingDocument);
    $proxy->setListOfPrincipals($listOfPrincipals);

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';
    require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';


    $elementler = getButtonPositionKeys('ds_icra_sexsin_deyisdirilmesi');
    $priv = new Privilegiya();
    $senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/icra_sexsin_deyisdirilmesi/icra_sexsin_deyisdirilmesi.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}