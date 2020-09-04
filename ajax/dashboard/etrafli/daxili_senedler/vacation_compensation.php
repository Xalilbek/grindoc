<?php
defined('DIRNAME_INDEX') or die("hara?");

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $sid = (int)$parametrler['sid'];
    $userId = (int)$_SESSION['erpuserid'];

    $emrInf = pdof()->query("SELECT * FROM v_emrler WHERE id='$sid' AND emr_tip='mezuniyyete_gore_kompensasiya'")->fetch();

    if((int)$emrInf['user_id']===$userId || (int)$emrInf['elave_edib']===$userId || in_array($userId,explode(",",$emrInf['rehberler'])) || in_array($userId,explode(",",$emrInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_emrler_tesdiqleme WHERE emr_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch())
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
        $tr1 = '';
        $tr2 = '';

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
                $tr1 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("47evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
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
                $tr2 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("47evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }

        if($tesdiqBtn && (in_array($userId,explode(",",$emrInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$emrInf['tesdiqleme_geden_userler']))))===false)
        {
            $tesdiqBtn = false;
        }
        $imtinaBtn = false;
        if((int)$emrInf['status']!=3 && (in_array($userId,explode(",",$emrInf['tesdiqleme_geden_userler'])) || $userId==$emrInf['elave_edib'] || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$emrInf['tesdiqleme_geden_userler'])))))
        {
            $imtinaBtn = true;
        }
        $senedSeria = pdof()->query("SELECT seria FROM tb_sened_novleri WHERE forma_ad='mezuniyyete_gore_kompensasiya'")->fetch();

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($emrInf['document_id']);
        $elementler = array(
            "sid"=>$sid,
            "detailed_information" => $intDoc->getDetailedInformationHTML(),

            "MN"=>time().rand(1,1000),
            "userId"=>$userId,
            "edits"=>$edits,
            'imtinaSebeb'=>$imtinaSebeb,
            'imtinaEden'=>$imtinaEden,
            'tr1'=>($tr1===""?" - yoxdur":$tr1),
            'tr2'=>($tr2===''?" - yoxdur":$tr2),
            'emrin_nomresi'=>htmlspecialchars($senedSeria[0]).sprintf("%03d", $emrInf['emrin_nomresi']),
            'user_ad'=>htmlspecialchars($emrInf['user_ad']),
            'qeyd'=>htmlspecialchars($emrInf['qeyd']),
            'silinen_mezuniyyet_gunleri'=>(int)$emrInf['silinen_mezuniyyet_gunleri'],
            'ish_ili1'=>(int)$emrInf['ish_ili'],
            'ish_ili2'=>(int)$emrInf['ish_ili']+1,
            'tesdiqBtn'=>(int)$tesdiqBtn,
            'imtinaBtn'=>(int)$imtinaBtn,
            'editBtn'=>(int)((int)$emrInf['elave_edib']===$userId),

            "47sebebi_daxil_edin"=>dil::soz("47sebebi_daxil_edin"),
            "47imtinanin_sebebi"=>dil::soz("47imtinanin_sebebi"),
            "47tesdiqlemeyibler"=>dil::soz("47tesdiqlemeyibler"),
            "47tesdiqleyibler"=>dil::soz("47tesdiqleyibler"),
            "47emrin_nomresi"=>dil::soz("47emrin_nomresi"),
            "47imtina_edilib"=>dil::soz("47imtina_edilib"),
            "47silinecek_mg"=>dil::soz("47silinecek_mg"),
            "47deyishdirilib"=>dil::soz("47deyishdirilib"),
            "47qisa_mezmun"=>dil::soz("47qisa_mezmun"),
            "47imtina_et"=>dil::soz("47imtina_et"),
            "47deyishdir"=>dil::soz("47deyishdir"),
            "47tesdiqle"=>dil::soz("47tesdiqle"),
            "47emekdash"=>dil::soz("47emekdash"),
            "47tarixche"=>dil::soz("47tarixche"),
            "47imtina"=>dil::soz("47imtina"),
            "47ish_ili"=>dil::soz("47ish_ili"),
            "47bagla"=>dil::soz("47bagla"),
            "47sebeb"=>dil::soz("47sebeb"),
            "47bagla"=>dil::soz("47bagla"),
            "47gun"=>dil::soz("47gun"),
        );
        print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/vacation_compensation',$elementler, 'prodoc'))));


    }
}
