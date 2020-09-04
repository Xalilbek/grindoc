<?php
defined('DIRNAME_INDEX') or die("hara?");

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $sid = (int)$parametrler['sid'];
    $userId = (int)$_SESSION['erpuserid'];
    $language = $user->getLang();
    $dill = new dilStclass($language);

    $getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
    $userGroup = (int)$getUserInfo['Groupp'];

    $emrInf = pdof()->query("SELECT *,(SELECT name FROM tb_proid_emr_category_msk WHERE id=v_emrler.isden_cixma_category) AS ick_ad,(SELECT ad FROM tb_emr_esaslar WHERE id=v_emrler.esas) AS esas_ad FROM v_emrler WHERE id='$sid' AND emr_tip='emek_muqavilesine_xitam'")->fetch();

    if((int)$emrInf['user_id']===$userId || (int)$emrInf['elave_edib']===$userId || in_array($userId,explode(",",$emrInf['rehberler'])) || in_array($userId,explode(",",$emrInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_emrler_tesdiqleme WHERE emr_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch() || true)
    {
        $changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='emrler' AND kid='$sid' AND user_id='$userId'");
        $getUserInf2 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$emrInf['elave_edib'] . "'")->fetch();
        $imtinaSebeb = "";
        $imtinaEden = "";
        if($emrInf['status']==3)
        {
            $imtinaEden = (int)$emrInf['imtinaEden'];
            $imtinaEdenInf = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='$imtinaEden'")->fetch();
            $sebeb = $emrInf['imtinaSebebi'];
            $getImtinaDate = pdof()->query("SELECT TOP 1 date FROM tb_emrler_logs WHERE emr_id='$sid' AND ne='4' ORDER BY date DESC")->fetch();
            $imtinaSebeb = htmlspecialchars($sebeb);
            $imtinaEden = htmlspecialchars($imtinaEdenInf[0]) . " - " . date("d-m-Y H:i:s", strtotime($getImtinaDate[0]));
        }

        $edits = '';
        $getLogsEdit = pdof()->query("SELECT * FROM tb_emrler_logs WHERE emr_id='$sid' AND ne='1'");
        while($logEdit = $getLogsEdit->fetch())
        {
            $getuserInff = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$logEdit['user_id'] . "'")->fetch();
            $edits .= "<div>".htmlspecialchars($getuserInff[0],ENT_QUOTES)." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s", strtotime($logEdit['date']))."<br>".dil::soz("47sebeb").": ".htmlspecialchars($logEdit['qeyd'])."</div>";
        }

        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_emrler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.emr_id='$sid' AND tb1.emeliyyat_tip='tesdiqleme'");
        //$tr1 = '';
        //$tr2 = '';

        $tesdiqBtn = false;
        $vekaletnameUserYoxla = 0;

        while($trInfo = $tesdiqleyenler->fetch())
        {
            if((int)$trInfo['status']===1)
            {
                if((int)$trInfo['user_id']===$userId)
                {
                    $tesdiqBtn = false;
                }
                //$tr1 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("47evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
            }
            else
            {
                if((int)$trInfo['vekaletname_user']===$userId)
                {
                    $vekaletnameUserYoxla = (int)$trInfo['user_id'];
                    $tesdiqBtn = true;
                }
                if((int)$trInfo['user_id']===$userId)
                {
                    $tesdiqBtn = true;
                }
                //$tr2 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("47evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }

        if($tesdiqBtn && (in_array($userId,explode(",",$emrInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$emrInf['tesdiqleme_geden_userler']))))===false)
        {
            $tesdiqBtn = false;
        }
        $imtinaBtn = false;
        if((int)$emrInf['status']===0 && (in_array($userId,explode(",",$emrInf['tesdiqleme_geden_userler'])) || $userId==$emrInf['elave_edib'] || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$emrInf['tesdiqleme_geden_userler'])))))
        {
            $imtinaBtn = true;
        }
        $senedler = array();
        foreach(explode(",", $emrInf['sened']) AS $senedEl)
        {
            if(empty($senedEl)) continue;
            $senedler[] = "<a href='uploads/emr_documents/emek_muqavilesine_xitam/".addslashes(htmlspecialchars($senedEl))."' target='_blank'><i class='fa fa-file'></i> ".htmlspecialchars($senedEl)."</a>";
        }
        $qoshma = array();
        foreach(explode(",", $emrInf['qoshma']) AS $senedEl)
        {
            if(empty($senedEl)) continue;
            $qoshma[] = "<a href='uploads/emr_documents/emek_muqavilesine_xitam/".addslashes(htmlspecialchars($senedEl))."' target='_blank'><i class='fa fa-file'></i> ".htmlspecialchars($senedEl)."</a>";
        }
        $senedSeria = pdof()->query("SELECT seria FROM tb_sened_novleri WHERE forma_ad='emr_emek_muqavilesine_xitam'")->fetch();
        $petitionId = (int)$emrInf['employe_petition'];
        $petitionInf = pdof()->query("SELECT * FROM tb_proid_employe_petition WHERE id='$petitionId'")->fetch();

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($emrInf['document_id']);
        $elementler = array(
            "sid"=>$sid,
            "detailed_information" => $intDoc->getDetailedInformationHTML(),
            "MN"=>time().rand(1,1000),
            "userId"=>$userId,
            "edits"=>$edits,
            'imtinaSebeb'=>$imtinaSebeb,
            "employe_petition"=>$petitionId==0?' <i>yoxdur</i> ':'<a href="javascript:templateYukle(\'proid_employe_petition_info\',\''.dil::soz("47ishchinin_erizesi__etrafli").'\',{\'pid\':\''.$petitionId.'\'},55,true);">İƏ/'.sprintf("%05d",(int)$petitionId)."-".date("Y",strtotime($petitionInf['petition_date'])).'</a>',
            'imtinaEden'=>$imtinaEden,
            //'tr1'=>($tr1===""?" - yoxdur":$tr1),
            //'tr2'=>($tr2===''?" - yoxdur":$tr2),
            'emrin_verilme_tarixi'=>$user->tarix($emrInf['emrin_verilme_tarixi']),
            'teyin_olunma_tarixi'=>$user->tarix($emrInf['teyin_olunma_tarixi']),
            'emrin_nomresi'=>htmlspecialchars($emrInf['emrin_nomresi']),
            'user_ad'=>htmlspecialchars($emrInf['user_ad']),
            'bolme_ad'=>htmlspecialchars($emrInf['kohne_bolme_ad']),
            'vezife_ad'=>htmlspecialchars($emrInf['kohne_vezife_ad']),
            'sened'=>count($senedler)==0 ? sprintf("<i>%s</i>", dil::soz("47yoxdur")) : implode("<br/>", $senedler),
            'qoshma'=>count($qoshma)==0 ? sprintf("<i>%s</i>", dil::soz("47yoxdur")) : implode("<br/>", $qoshma),
            'tesdiqBtn'=>(int)$tesdiqBtn,
            'qeyd'=>htmlspecialchars($emrInf['esas_ad']),
            'isden_cixma_kategory'=>htmlspecialchars($emrInf['ick_ad']),
            'son_haqq_hesab'=>$user->r_topla(0 , $emrInf['son_haqq_hesab']),
            'esas'=>htmlspecialchars($emrInf['qeyd']),
            'imtinaBtn'=>(int)$imtinaBtn,
            'editBtn'=>(int)((int)$emrInf['elave_edib']===$userId&&$emrInf['xitam_verilib']==0),

            "47emek_muqavilesine_xitam__senedin_tarixchesi"=>dil::soz("47emek_muqavilesine_xitam__senedin_tarixchesi"),
            "47emek_muqavilesine_xitam__duzelish_et"=>dil::soz("47emek_muqavilesine_xitam__duzelish_et"),
            "47xitam_verildiyi_vezife"=>dil::soz("47xitam_verildiyi_vezife"),
            "47xitam_verildiyi_bolme"=>dil::soz("47xitam_verildiyi_bolme"),
            "47sebebi_daxil_edin"=>dil::soz("47sebebi_daxil_edin"),
            "47emrin_verilme_tarixi"=>dil::soz("47emrin_verilme_tarixi"),
            "47xitam_verilme_tarixi"=>dil::soz("47xitam_verilme_tarixi"),
            "47imtinanin_sebebi"=>dil::soz("47imtinanin_sebebi"),
            "47tesdiqlemeyibler"=>dil::soz("47tesdiqlemeyibler"),
            "47ishchinin_erizesi"=>dil::soz("47ishchinin_erizesi"),
            "47tesdiqleyibler"=>dil::soz("47tesdiqleyibler"),
            "47emrin_nomresi"=>dil::soz("47emrin_nomresi"),
            "47imtina_edilib"=>dil::soz("47imtina_edilib"),
            "47deyishdirilib"=>dil::soz("47deyishdirilib"),
            "47qisa_mezmun"=>dil::soz("47qisa_mezmun"),
            "47imtina_et"=>dil::soz("47imtina_et"),
            "47deyishdir"=>dil::soz("47deyishdir"),
            "47tesdiqle"=>dil::soz("47tesdiqle"),
            "47tarixche"=>dil::soz("47tarixche"),
            "47emekdash"=>dil::soz("47emekdash"),
            "47imtina"=>dil::soz("47imtina"),
            "47qoshma"=>dil::soz("47qoshma"),
            "47sened"=>dil::soz("47sened"),
            "47bagla"=>dil::soz("47bagla"),
            "47sebeb"=>dil::soz("47sebeb"),
            "47esas"=>dil::soz("47esas"),
            "47son_haqqhesab"=>dil::soz("47son_haqqhesab"),
            "47duzelish_et" => dil::soz("47duzelish_et")
        );
        print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/termination_contract',$elementler, 'prodoc'))));
    }

}
