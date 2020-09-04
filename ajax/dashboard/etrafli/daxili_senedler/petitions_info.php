<?php
defined('DIRNAME_INDEX') or die("hara?");

if(isset($parametrler['sid'])  && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $pid = (int)$parametrler['sid'];
    $userId = (int)$_SESSION['erpuserid'];
    $shablon_sal = 0;
    $bashliq = "";

    $mInf = pdof()->query(
        "
        SELECT 
        tb1.*,
        (SELECT user_ad FROM v_user_adlar WHERE USERID=who_registered) AS who_registered_name,
        (SELECT user_ad FROM v_user_adlar WHERE USERID=employe) AS employe_name 
        FROM tb_proid_employe_petition tb1
        WHERE tb1.id='$pid'"
    )
        ->fetch();

    $type = (int)$mInf['type'];
    if($type==3)
    {
        $fetch_position = pdof()->query("SELECT ( SELECT struktur_bolmesi FROM tb_Struktur WHERE sebebi = struktur_id ) AS yeni_shobe, ( SELECT vezife FROM tb_vezifeler WHERE sebebi2 = id ) AS yeni_vezife ,(SELECT vezife  FROM tb_vezifeler tb3 WHERE tb2.vezife_id=tb3.id) AS cari_vezife, (SELECT struktur_bolmesi  FROM tb_Struktur tb4 WHERE tb2.struktur_id=tb4.struktur_id) AS cari_shobe FROM tb_proid_employe_petition tb1 LEFT JOIN tb_users tb2 ON tb1.who_registered=tb2.USERID  WHERE id='$pid'")->fetch();
    }

    if(!$mInf)
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red;'>".dil::soz("47sehv_bele_melumat_yoxdur")."</div>",ENT_QUOTES)));
        exit();
    }

    switch ($mInf['type']) {
        case 1 :
            $mInf['type_name'] = dil::soz("47ishe_qebul");
            break;
        case 2 :
            $mInf['type_name'] = dil::soz("47ishe_xitam");
            break;
        case 3 :
            $mInf['type_name'] = dil::soz("47bashqa_ishe_kechirtme");
            break;
        case 6 :
            $mInf['type_name'] = dil::soz("47tibbi_sigorta");
            break;
        default :
            $mInf['type_name'] = dil::soz("73maddi_yardim");
            break;

    }
    $date = $type==(6||7) ? $user->tarix($mInf['petition_date']) :
        $user->tarix($mInf['petition_date'])." , ".date("H:i:s",strtotime($mInf['petition_date']));

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($mInf['document_id']);

    $elementler = array(
        "type" =>$type,
        "detailed_information" => $intDoc->getDetailedInformationHTML(),
        "detailed_information_tree" => $intDoc->getRelatedInternalDocumentsHTMLTree(),
        "pid"=>$pid,
        "MN"=>time().rand(1,1000),
        "status"=>(int)$mInf['status'],

        "who_registered"=>htmlspecialchars($mInf['who_registered_name']),

        "cari_shobe"=> $type==3 ? htmlspecialchars($fetch_position['cari_shobe']) : '',
        "cari_vezife"=> $type==3 ? htmlspecialchars($fetch_position['cari_vezife']) : '',
        "yeni_shobe"=> $type==3 ? htmlspecialchars($fetch_position['yeni_shobe']) : '',
        "yeni_vezife"=> $type==3 ? htmlspecialchars($fetch_position['yeni_vezife']) : '',
        "employe"=>htmlspecialchars($mInf['employe_name']),
        "petition_type"=>htmlspecialchars($mInf['type_name']),
        "note"=>htmlspecialchars($mInf['note']),
        "date"=>$date,


        "47sebebi_daxil_edin"=>dil::soz("47sebebi_daxil_edin"),
        "47erizenin_tipi"=>dil::soz("47erizenin_tipi"),
        "47elave_etdi"=>dil::soz("47elave_etdi"),
        "47imtina_et"=>dil::soz("47imtina_et"),
        "47tarixce"=>dil::soz("47tarixce"),
        "47cari_shobe"=>dil::soz("47cari_shobe"),
        "47cari_vezife"=>dil::soz("47cari_vezife"),
        "47yeni_shobe"=>dil::soz("47yeni_shobe"),
        "47yeni_vezife"=>dil::soz("47yeni_vezife"),
        "47erizenin_nomresi"=>dil::soz("47erizenin_nomresi"),
        "47emekdash"=>dil::soz("47emekdash"),
        "47tarix"=>dil::soz("47tarix"),
        "47bagla"=>dil::soz("47bagla"),
        "47sebeb"=>dil::soz("47sebeb"),
        "47fayl"=>dil::soz("47fayl"),
        "47qeyd"=>dil::soz("47qeyd"),
    );


    print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/employee_petition',$elementler, 'prodoc'))));

}
