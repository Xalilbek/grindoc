<?php

if(isset($parametrler['sid']) && $parametrler['sid']>0)
{
    $MN = 0;
    $userId = (int)$_SESSION['erpuserid'];
    $getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
    $userGroup = (int)$getUserInfo['Groupp'];

    $pri_check = pdof()->query("SELECT privs FROM tb_group_privs_new tb1 WHERE group_id='$userGroup' AND tb1.modul_id = (SELECT id FROM tb_modules tb2 WHERE tb2.module='xestelik_vereqi')")->fetch();
    $pri = (int)$pri_check[0];

    $sid = (int)$parametrler['sid'];
    $mtInf = pdof()->query("SELECT * FROM v_xestelik_vereqleri WHERE id='$sid'")->fetch();
    $goster = false;
    $legal_illegal      = $user->getActiveSystemType();

    if($mtInf['user_id']==$userId)
    {
        $goster = true;
    }

    $imtinaSebeb="";
    $imtinaEden="";

    $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_xestelik_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.xestelik_vereqi_id='$sid' AND tb1.emeliyyat_tip='tesdiqleme'");
    $tr1 = '';
    $tr2 = '';

    $testiqBtn = false;
    $vekaletnameUserYoxla = 0;

    if($mtInf['status']==3)
    {
        $imtinaEden = (int)$mtInf['imtinaEden'];


        $imtinaEdenInf = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='$imtinaEden'")->fetch();

        $sebeb = $mtInf['imtinaSebebi'];
        $getImtinaDate = pdof()->query("SELECT TOP 1 date FROM tb_xestelik_vereqi_logs WHERE xv_id='$sid' AND ne='4' ORDER BY date DESC")->fetch();
        $imtinaSebeb = htmlspecialchars($sebeb);
        $imtinaEden = htmlspecialchars($imtinaEdenInf[0]) . " - " . date("d-m-Y H:i:s", strtotime($getImtinaDate[0]));
    }


    while($trInfo = $tesdiqleyenler->fetch())
    {
        if((int)$trInfo['status']===1)
        {
            $tr1 .= "<div visit_uid=".(int)$trInfo['user_id']." >".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("xv_etr_ev_etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("xv_etr_ev")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
        }
        else
        {
            if((int)$trInfo['vekaletname_user']===$userId)
            {
                $vekaletnameUserYoxla = (int)$trInfo['user_id'];
                $testiqBtn = true;
            }
            if((int)$trInfo['user_id']===$userId)
            {
                $testiqBtn = true;
            }
            $tr2 .= "<div visit_uid=".(int)$trInfo['user_id'].">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("xv_etr_ev_etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("xv_etr_ev")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
        }
    }

    $changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='xestelik_vereqi' AND kid='$sid' AND user_id='$userId'");
    $melumat = array();
    $melumat['status'] = "hazir";
    $melumat['testiqBtn'] = (int)$testiqBtn;
    $melumat['editBtn'] = ((int)$mtInf['elave_eden_user']===$userId ) ? 1 : 0;
    $melumat['ImtinaBtn'] = ((int)$mtInf['elave_eden_user']===$userId ) ? 1 : 0;
    $melumat['emekdash'] = htmlspecialchars($mtInf['user_ad']);
    $melumat['emekdash_id'] = (int)$mtInf['user_id'];
    $melumat['about'] = htmlspecialchars($mtInf['about']);
    $melumat['elave_eden_user'] = htmlspecialchars($mtInf['elave_eden_user_ad']);
    $melumat['ilk_tarix'] = date("d-m-Y", strtotime($mtInf['ilk_tarix']));
    $melumat['son_tarix'] = date("d-m-Y", strtotime($mtInf['son_tarix']));
    $melumat['gunluk_emek_haqqi'] = $user->r_round(((float)$mtInf['gundelik_emekhaqqi']*(float)$mtInf['kompensasia'])/100.00,3).' AZN <i class="fa fa-info-circle" style="cursor: pointer;" onclick=\'templateYukle("gunlukEmekhaqqiEtrafliXestelik","'.dil::soz("xv_etr_etr_melumat").'",{"xid":'.$sid.'});\'></i>';
    $melumat['kompensasia'] = (float)$mtInf['kompensasia']."%";

    $edits = '';
    $getLogsEdit = pdof()->query("SELECT * FROM tb_xestelik_vereqi_logs WHERE xv_id='" . $sid . "' AND ne='1'");
    while($logEdit = $getLogsEdit->fetch())
    {
        $getuserInff = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$logEdit['user_id'] . "'")->fetch();
        $edits .= "<div visit_uid=".(int)$logEdit['user_id'].">".htmlspecialchars($getuserInff[0],ENT_QUOTES)." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s", strtotime($logEdit['date']))."<br>".dil::soz("xv_etr_sebeb").": ".htmlspecialchars($logEdit['qeyd'])."</div>";
    }
    if($testiqBtn && (in_array($userId,explode(",",$mtInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mtInf['tesdiqleme_geden_userler']))))===false)
    {
        $testiqBtn = false;
    }
    $imtinaBtn = false;
    if((int)$mtInf['status']!==3 && (in_array($userId,explode(",",$mtInf['tesdiqleme_geden_userler'])) || $userId==$mtInf['user_id'] || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mtInf['tesdiqleme_geden_userler'])))))
    {
        $imtinaBtn = true;
    }

    $attachment = "";
    foreach(explode(",",$mtInf['attachment']) AS $sened)
    {
        if(trim($sened)!="")
        {
            $attachment .= '<div style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/proid/xestelik_vereqi/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/proid/xestelik_vereqi/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
        }
    }
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($mtInf['document_id']);
    //print json_encode($melumat);
    $elementler = array(
        "sid"=>$sid,
        "detailed_information" => $intDoc->getDetailedInformationHTML(),

        "MN"=>$MN,
        "editBtn"=>(int)($userId==$mtInf['user_id'] || $userId==$mtInf['elave_eden_user']),
        'tesdiqBtn'=>(int)$testiqBtn,
        'imtinaBtn'=>(int)$imtinaBtn,
        "emekdash_id"=>(int)$mtInf['user_id'],
        "kim"=>htmlspecialchars($mtInf['user_ad']),
        "tarix1"=>date("d-m-Y", strtotime($mtInf['ilk_tarix'])),
        "tarix2"=>date("d-m-Y", strtotime($mtInf['son_tarix'])),
        "melumat"=>htmlspecialchars($mtInf['about']),
        "elave_eden"=>htmlspecialchars($mtInf['elave_eden_user_ad']),
        "kompensasia"=> (float)$mtInf['kompensasia']."%",
        "gunlukemek"=>$melumat['gunluk_emek_haqqi'],
        'tr1'=>($tr1===""?" - ".dil::soz("xv_etr_yoxdur")."":$tr1),
        'tr2'=>($tr2===''?" - ".dil::soz("xv_etr_yoxdur")."":$tr2),
        "edits"=>$edits,
        "imtinaSebeb" => $imtinaSebeb,
        "imtinaEden"=> $imtinaEden,
        "attachment"=>$attachment,
        "xv_nomresi" => htmlspecialchars($mtInf['document_number']),
        "xv_seriasi" => htmlspecialchars($mtInf['seria_number']),

        "xv_xestelik_vereqesinin_seriasi" => dil::soz("xv_xestelik_vereqesinin_seriasi"),
        "xv_xestelik_vereqesinin_nomresi" => dil::soz("xv_xestelik_vereqesinin_nomresi"),
        "xv_etr_emekdash" => dil::soz("xv_etr_emekdash"),
        "xv_etr_elave_eden_emekdash" => dil::soz("xv_etr_elave_eden_emekdash"),
        "xv_etr_ilk_tarix" => dil::soz("xv_etr_ilk_tarix"),
        "xv_etr_son_tarix" => dil::soz("xv_etr_son_tarix"),
        "xv_etr_kompensasia" => dil::soz("xv_etr_kompensasia"),
        "xv_etr_1_gunluk_emek_haqqi" => dil::soz("xv_etr_1_gunluk_emek_haqqi"),
        "xv_etr_qeyd" => dil::soz("xv_etr_qeyd"),
        "xv_etr_fayl" => dil::soz("xv_etr_fayl"),
        "xv_etr_testiqleyen_rehberler" => dil::soz("xv_etr_testiqleyen_rehberler"),
        "xv_etr_testiqlemeyen_rehberler" => dil::soz("xv_etr_testiqlemeyen_rehberler"),
        "xv_etr_deyishdirilib" => dil::soz("xv_etr_deyishdirilib"),
        "xv_etr_imtina_edib" => dil::soz("xv_etr_imtina_edib"),
        "xv_etr_sebeb" => dil::soz("xv_etr_sebeb"),
        "xv_etr_duzelish_et" => dil::soz("xv_etr_duzelish_et"),
        "xv_etr_testiqle" => dil::soz("xv_etr_testiqle"),
        "xv_etr_imtina_et" => dil::soz("xv_etr_imtina_et"),
        "xv_etr_bagla" => dil::soz("xv_etr_bagla"),
        "xv_etr_ugurla_testiqlendi" => dil::soz("xv_etr_ugurla_testiqlendi"),
        "xv_etr_imtina_et" => dil::soz("xv_etr_imtina_et"),
        "xv_etr_sebebi_daxil_edin" => dil::soz("xv_etr_sebebi_daxil_edin"),
        "xv_etr_ugurla_imtina_edildi" => dil::soz("xv_etr_ugurla_imtina_edildi"),
        "xv_etr_testiqlenmeyib" => dil::soz("xv_etr_testiqlenmeyib"),
        "xv_etr_testiqlenib" => dil::soz("xv_etr_testiqlenib"),
        "xv_etr_imtina_edilib" => dil::soz("xv_etr_imtina_edilib"),
        "xv_tarixche2" => dil::soz("xv_tarixche2"),
        "xv_tarixche" => dil::soz("xv_tarixche"),
        "legal_illegal" => $legal_illegal
    );

    print json_encode(array("status" => "success", "html" => $user->template_yukle('daxili_senedler/xestelik_vereqi',$elementler, 'prodoc') ));

}
else
{
    //$melumat['status'] = "sehf";
    //$melumat['error_mesaj'] = "Olmaz!";
    //print json_encode($melumat);
}
