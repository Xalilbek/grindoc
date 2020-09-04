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
            tb2.id as sened_id,
            ( SELECT struktur_bolmesi FROM v_users WHERE USERID = tb2.sifarisci ) AS sifarisci_shobesi,
            ( SELECT name FROM tb_prodoc_sifaris_tipi WHERE id = tb2.sifaris_tipi ) AS sifarisci_tipi,
            tb2.document_id,
            tb2.sifaris_tarixi,
            tb2.sifarisci,
            tb2.sifaris_tipi,
            tb1.id as senedID,
            tb1.state,
            tb2.senedin_tarixi,
            tb2.qeyd
        FROM
            v_daxil_olan_senedler as tb1
            LEFT JOIN tb_prodoc_satinalma_sifaris as tb2 ON tb1.id = tb2.document_id 
        WHERE tb2.id = $sid
    ";

    $poa = DB::fetch($sql);

    $sifaris_netice = DB::fetchColumn("SELECT status FROM tb_prodoc_formlar_tesdiqleme WHERE daxil_olan_sened_id = {$poa['senedID']} AND qrup = (SELECT MAX(qrup)-1 from tb_prodoc_formlar_tesdiqleme WHERE daxil_olan_sened_id = {$poa['senedID']})");

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';
    require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';


    $elementler = getButtonPositionKeys('ds_satin_alma');
    $priv = new Privilegiya();
    $senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/satin_alma/satin_alma.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}