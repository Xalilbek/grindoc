<?php

use  View\Helper\Proxy;


include_once DIRNAME_INDEX  . '/class/class.grouparray.php';

$act_types=array(
  "muhafize"=>" Mina/PHS-lərin müvəqqəti mühafizə
                                          altında saxlanılmasına
                                          dair",
  "zerersizleshdirme"=>" Mina/PHS-lərin zərərsizləşdirilməsinə dair",
  "dashinma"=>" Mina/PHS-lərin daşınmasına dair",
  "tek_sursat_ashkar"=>'"Tək Sursat"
                                         əməliyyatı zamanı Mina/PHS-lərin
                                         aşkar olunmasına dair',
  "tesk_sursat_dashinma"=>'"Tək Sursat"
                                         əməliyyatı zamanı Mina/PHS-lərin
                                         aşkar olunmasına və daşınmasına dair',
    "ashkar_dashinma"=>'"Aşkar olunan oldu/soyuq
                                            silah, onların qurğuları və
                                            ehtiyyat hissələri, tərkibi və təyinatı məlum olmayan
                                            Mina/PHS və qablaşdırılması pozulmayan və təkrar istifadəsi mümkün olan
                                            Mina/PHS-lərin Azərbaycan
                                            Respubilklası Müdafiə Nazirliyinı verilməsi üçün təhvil-təslim"',
    "erize"=>"Ərizə"
);



if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       SELECT act.*,
               city.ad as sheher,
               region.ad as kend,
               CONCAT([user].Adi,' ',[user].Soyadi) as tehvil_veren_ad
        FROM tb_prodoc_aktlar act
               LEFT JOIN tb_general_cities city ON city.id = act.sheher_id
               LEFT JOIN tb_general_regions region ON region.id = act.kend_id
               LEFT JOIN v_users [user] ON [user].USERID = act.tehvil_veren
       WHERE act.id= $id
        ";


    $poa = DB::fetch($sql);
    $checkPetition=false;
    is_null($poa['act_type'])?$checkPetition=true:$checkPetition=false;
    $poa['act_type']=isset($act_types[$poa['act_type']])?$act_types[$poa['act_type']]:$act_types['erize'];
    $sql="
        SELECT imzalayan_shexsler.*, CONCAT(Adi, ' ', Soyadi) as user_name
        FROM tb_prodoc_akt_imzalayan_shexsler imzalayan_shexsler
               LEFT JOIN v_users ON v_users.USERID = imzalayan_shexsler.user_id
        WHERE internal_document_id = ".$poa['id'];

    $imzalayan_shexsler = DB::fetchAll($sql);

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
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/act/act.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}