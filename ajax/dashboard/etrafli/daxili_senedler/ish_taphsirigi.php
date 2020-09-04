<?php
include_once DIRNAME_INDEX.'/class/class.functions.php';

use  View\Helper\Proxy;

$user = new User();
$userTenantId = $user->getActiveTenantId();

include_once DIRNAME_INDEX  . '/class/class.grouparray.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       SELECT
            ( SELECT CONCAT ( Adi, ' ', Soyadi, ' ', AtaAdi ) FROM tb_users WHERE USERID = tb_gorushler.user_id ) AS kim,
            ( SELECT CONCAT ( Adi, ' ', Soyadi, ' ', AtaAdi ) FROM tb_Customers WHERE id = tb_gorushler.customer ) AS elaqeli_shexs,
            ( SELECT CONCAT ( Adi, ' ', Soyadi, ' ', AtaAdi ) FROM tb_users WHERE USERID = tb_gorushler.gorushu_teyin_eden_user ) AS gorushu_teyin_eden_user,
            ( SELECT Adi FROM tb_CustomersCompany WHERE id = tb_gorushler.company ) AS company,
            teyinat_gorush AS teyinat,
            teyinat_tipi_gorush AS teyinat_tipi,
            start_date,
            end_date,
            about AS melumat,
            ofisde,
            document_id 
        FROM
            tb_gorushler 
        WHERE
            id = $id 
            AND TenantId = $userTenantId 
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




    require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';


    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/ish_tapshirigi/ish_tapshirigi.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}