<?php

use  View\Helper\Proxy;


include_once DIRNAME_INDEX  . '/class/class.grouparray.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       Select tb_prodoc_partlamamish_tek_sursat.*,
        tb_general_cities.ad as sheher_ad, 
        tb_general_regions.ad as rayon_ad,
		document_number 
        from tb_prodoc_partlamamish_tek_sursat 
        LEFT JOIN tb_general_cities on tb_general_cities.id=tb_prodoc_partlamamish_tek_sursat.sheher_id  
        left JOIN tb_general_regions on rayon_id=tb_general_regions.id 
		LEFT JOIN tb_prodoc_document_number on tb_prodoc_document_number.id=document_id
        where  tb_prodoc_partlamamish_tek_sursat.id = $id
        ";

    $poa = DB::fetch($sql);


    $sql="
        Select * from tb_daxil_olan_senedler
        where id=".$poa['document_id'];
    $docInfo= DB::fetch($sql);




    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);
    $intDoc->setCustomQuery('v_daxil_olan_senedler_corrected', true);
    $doc_numb=$intDoc->getData()['document_number'];
    $created_by=$intDoc->getData()['created_by'];
    $created_by_info= DB::fetch("SELECT Concat(Adi,' ',Soyadi) as full_name, struktur_bolmesi, vezife from v_users where USERID=".$created_by);


    $relatedDoc = $intDoc->getRelatedDocuments()[0];
    $tapshiriq_number= $relatedDoc['document_number'];

    require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
    $RM = tapsiriqEmrinReyMuelifi($relatedDoc['id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/hesabat_yarat/hesabat_yarat.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}