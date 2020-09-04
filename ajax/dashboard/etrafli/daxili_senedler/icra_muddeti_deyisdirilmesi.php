<?php

use View\Helper\Proxy;

include_once DIRNAME_INDEX  . '/class/class.grouparray.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       SELECT
               v_daxil_olan_senedler.id as sened_id, tb2.document_id,
            ( SELECT name FROM tb_prodoc_nazalogiya WHERE id = tb2.qisa_mezmun ) AS qisa_mezmun,
            (SELECT related_document_id FROM tb_internal_document_relation WHERE internal_document_id = tb2.document_id) as senede_bagli_nomre,
            tb2.qeyd,
            tb2.icra_muddeti_muraciet_olunan_tarix,
            tb2.senedin_tarixi,
            v_daxil_olan_senedler.state
        FROM
            v_daxil_olan_senedler
            LEFT JOIN tb_icra_muddeti_deyisdirilmesi as tb2 ON v_daxil_olan_senedler.id = tb2.document_id 
        WHERE tb2.id = $sid
    ";

    $poa = DB::fetch($sql);
    $sql = "
        SELECT
                v_user_adlar.user_ad,
                v_user_adlar.USERID
            FROM
                tb_prodoc_formlar_tesdiqleme as tb2
            LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.user_id
            WHERE
                emeliyyat_tip = 'viza'
                AND document_id =  $sid
    ";




    $getInspectorsOfDocument = DB::fetchAll($sql);


    $listOfPrincipals =[];
    foreach ($getInspectorsOfDocument as $inspector) {
        $listOfPrincipals[] = $inspector['USERID'];
    }

    $proxy = new Proxy($incomingDocument);
    $proxy->setListOfPrincipals($listOfPrincipals);


    $senede_bagli_nomre = DB::fetch("SELECT document_number, icra_edilme_tarixi FROM v_daxil_olan_senedler WHERE id = {$poa['senede_bagli_nomre']}");

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';
    require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';


    $elementler = getButtonPositionKeys('ds_icra_muddeti_deyisdirilmesi');
    $priv = new Privilegiya();
    $senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/icra_muddeti_deyisdirilmesi/icra_muddeti_deyisdirilmesi.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}