<?php
if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
        SELECT
            poa.document_id,
            poa.id,
            principal.user_ad AS principal_name,
            proxy.user_ad AS proxy_name,
            poa.start_date,
            poa.end_date,
            poa.parallelism,
            poa.allowed_to_work_with_subordinate_users_docs,
            poa.note
        FROM tb_prodoc_power_of_attorney AS poa
        LEFT JOIN v_user_adlar AS principal
         ON principal.USERID = poa.from_user_id
        LEFT JOIN v_user_adlar AS proxy
         ON proxy.USERID = poa.to_user_id
        WHERE poa.id = $sid 
    ";

    $poa = DB::fetch($sql);

    $sql = "
        SELECT doc_type
        FROM tb_prodoc_power_of_attorney_allowed_doc
        WHERE power_of_attorney_id = {$poa['id']}
    ";
    $allowedDocs = DB::fetchColumnArray($sql);

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';

    require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';

    $elementler = getButtonPositionKeys('ds_power_of_attorney');
    $priv = new Privilegiya();
    $senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/power_of_attorney/poa_etrafli.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}