<?php
if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
        SELECT
            tb2.document_id,
            principal.USERID AS principal_id,
            proxy.USERID AS proxy_id,
            principal.user_ad AS principal_name,
            proxy.user_ad AS proxy_name,
            ( SELECT name FROM tb_prodoc_nazalogiya WHERE id = tb2.qisa_mezmun ) AS qisa_mezmun,
            tb2.melumat_metni,
            tb2.id,
            tb3.state
        FROM tb_prodoc_teqdimat as tb2
        LEFT JOIN v_user_adlar AS principal
        ON principal.USERID = tb2.kim
        LEFT JOIN v_user_adlar AS proxy
        ON proxy.USERID = tb2.kime
        LEFT JOIN v_daxil_olan_senedler tb3
        ON tb3.id = tb2.document_id
        WHERE tb2.id = $sid
    ";

    $poa = DB::fetch($sql);

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';

    require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';


    $elementler = getButtonPositionKeys('ds_teqdimat');
    $priv = new Privilegiya();
    $senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/teqdimat/teqdimat.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}