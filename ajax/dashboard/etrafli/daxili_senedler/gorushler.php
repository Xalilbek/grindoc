<?php
defined('DIRNAME_INDEX') or die("hara?");

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $sid = (int)$parametrler['sid'];
    $userId = (int)$_SESSION['erpuserid'];
    $yoxlagorush = pdof()->query("SELECT * FROM tb_gorushler WHERE id='$sid'")->fetch();

    if(!$yoxlagorush)
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red; padding:20px;'>".dil::soz("11bele_gorush_yoxdur")."</div>",ENT_QUOTES)));
        exit();
    }

    $getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
    $userGroup = (int)$getUserInfo['Groupp'];

    $pri_check = pdof()->query("SELECT module,(SELECT top 1 privs FROM tb_group_privs_new tb2 WHERE tb2.group_id=$userGroup AND tb2.modul_id=tb1.id) privs FROM tb_modules tb1 WHERE tb1.module='ish_tapshiriqi' OR tb1.module='report_id1_new' OR tb1.module='report_mevacib' OR tb1.module='gorush_report'");

    $pri = array();

    $gorushInf = pdof()->query("SELECT tb_gorushler.*,v_user_adlar.user_ad_qisa AS arayish_teleb_etdi_ad,tb10.id AS arayish_id FROM tb_gorushler LEFT JOIN v_user_adlar ON tb_gorushler.arayish_teleb_etdi=v_user_adlar.USERID LEFT JOIN tb_arayishlar tb10 ON tb10.menbe='gorushler' AND tb10.menbe_id=tb_gorushler.id WHERE tb_gorushler.id='$sid'")->fetch();
    $testiqBtn = false;
    $qebulBtn = false;

    if(((int)$gorushInf['user_id']===$userId || (int)$gorushInf['gorushu_teyin_eden_user']===$userId || in_array($userId,explode(",",$gorushInf['rehberler'])) || in_array($userId,explode(",",$gorushInf['ishtirakchilar'])) || in_array($userId,explode(",",$gorushInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_gorushler_tesdiqleme WHERE gorush_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()) || (isset($pri['ish_tapshiriqi']) && $pri['ish_tapshiriqi']==2) || (isset($_POST['forTimecontrol']) && $_POST['forTimecontrol']==true && isset($pri['report_id1_new']) && $pri['report_id1_new']==2) || (isset($_POST['forReportp1']) && $_POST['forReportp1']==true && (isset($pri['report_mevacib']) && $pri['report_mevacib']!=0)) || (isset($pri['gorush_report']) && $pri['gorush_report']!=0) )
    {
        $changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='gorushler' AND kid='$sid' AND user_id='$userId'");
        $getCustomerInfo = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_Customers WHERE id='" . (int)$gorushInf['customer'] . "'")->fetch();
        $getUserInf1 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$gorushInf['user_id'] . "'")->fetch();
        $getCompInf = pdof()->query("SELECT Adi FROM tb_CustomersCompany WHERE id='" . (int)$gorushInf['company'] . "'")->fetch();
        $getUserInf2 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$gorushInf['gorushu_teyin_eden_user'] . "'")->fetch();

        $sebeb22="sehv";
        $imtinaEdens="sehv";
        $imtina_uid='';
        if($gorushInf['status']==3)
        {
            $imtinaEden = $gorushInf['imtinaEden'];
            $imtinaEdenInf = pdof()->query("SELECT USERID,CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . $imtinaEden . "'")->fetch();
            $sebeb = $gorushInf['imtinaSebebi'];
            $imtina_uid=$imtinaEdenInf['USERID'];
            $getImtinaDate = pdof()->query("SELECT TOP 1 date FROM tb_gorushler_logs WHERE gorush_id='$sid' AND ne='4' ORDER BY date DESC")->fetch();
            $imtinaEdens=htmlspecialchars($imtinaEdenInf[1]) . " - " . date("d-m-Y H:i", strtotime($getImtinaDate[0]));
            $sebeb22=htmlspecialchars($sebeb);
        }

        $teyinatlar1 = "";
        $teyinatlar2 = "";
        if($gorushInf['teyinat_gorush']!="" && $gorushInf['teyinat_gorush']!=null && $gorushInf['teyinat_tipi_gorush']!="" && $gorushInf['teyinat_tipi_gorush']!=null)
        {
            $getTeyinatInf = pdof()->query("SELECT * FROM tb_teyinatlar WHERE id='" . (int)$gorushInf['teyinat_gorush'] . "'")->fetch();
            $getTeyinatTipiInf = pdof()->query("SELECT * FROM tb_teyinatlar_tipi WHERE id='" . (int)$gorushInf['teyinat_tipi_gorush'] . "'")->fetch();
            $teyinatlar1 = $getTeyinatInf['tip'];
            $teyinatlar2 = $getTeyinatTipiInf['ad'];
        }
        $yolvaxti = floor((int)$gorushInf['yolVaxti']/60);
        $yolVaxtiS = ((int)$gorushInf['yolVaxti']-$yolvaxti*60);
        $istirakchilary = array();
        if($gorushInf['ishtirakchilar']!="")
        {
            foreach(explode(",", $gorushInf['ishtirakchilar']) AS $gishId)
            {
                $getIstirakchiInf = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . $gishId . "'")->fetch();
                $istirakchilary[] = '<span visit_uid='.$gishId.'>'.htmlspecialchars($getIstirakchiInf[0]).'</span>';
            }
        }
        $edits = array();
        $getLogsEdit = pdof()->query("SELECT * FROM tb_gorushler_logs WHERE gorush_id='" . $sid . "' AND ne='1'");
        while($logEdit = $getLogsEdit->fetch())
        {
            $getuserInff = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi),USERID FROM tb_users WHERE USERID='" . (int)$logEdit['user_id'] . "'")->fetch();
            $edits[] = array($getuserInff[0], date("d-m-Y H:i", strtotime($logEdit['date'])), htmlspecialchars($logEdit['qeyd']),$getuserInff[1]);
        }
        $getQebulLog2 = pdof()->query("SELECT TOP 1 tb_gorushler_logs.*,user_ad FROM tb_gorushler_logs LEFT JOIN v_user_adlar ON USERID=user_id WHERE gorush_id='$sid' AND ne='2' ORDER BY date DESC")->fetch();

        $carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_gorushler_tesdiqleme WHERE gorush_id='$sid' AND status='0'")->fetch();
        $carQrup = (int)$carQrup[0];

        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_gorushler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.gorush_id='$sid' AND tb1.emeliyyat_tip='tesdiqleme'");
        $tr1 = '';
        $tr2 = '';
        $tesdiqBtn = false;
        $vekaletnameUserYoxla = 0;

        while($trInfo = $tesdiqleyenler->fetch())
        {
            if((int)$trInfo['status']===1)
            {

                $tr1 .= "<div visit_uid=".(int)$trInfo['user_id'].">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("11elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("11evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
            }
            else
            {
                if((int)$trInfo['vekaletname_user']===$userId && $carQrup==(int)$trInfo['qrup'])
                {
                    $tesdiqBtn = true;
                    $vekaletnameUserYoxla = (int)$trInfo['user_id'];
                }
                if((int)$trInfo['user_id']===$userId && $carQrup==(int)$trInfo['qrup'])
                {
                    $tesdiqBtn = true;
                }
                $tr2 .= "<div visit_uid=".(int)$trInfo['user_id']. ">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("11elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("11evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }

        if($tesdiqBtn && (in_array($userId,explode(",",$gorushInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$gorushInf['tesdiqleme_geden_userler']))))===false)
        {
            $tesdiqBtn = false;
        }
        $elementler = array(
            'userId'=>$userId,
            'sid'=>$sid,
            'nvSifarish'=>$gorushInf['nv_sifarish']==1?"<i class='fa fa-check'></i>":"<i class='fa fa-remove'></i>",
            'gorush_ishtapshiriqi'=>dil::soz("11ishtapshrigi"),
            'gorushenin_uid'=>$gorushInf['user_id'],
            'gorush_teyinedenin_uid'=>$gorushInf['gorushu_teyin_eden_user'],
            'module'=>'ish_tapshiriqi',
            'qebulLog0'=>isset($getQebulLog2)?htmlspecialchars($getQebulLog2['user_ad']):"",
            'qebulLog1'=>isset($getQebulLog2)?date("d-m-Y H:i", strtotime($getQebulLog2['date'])):"",
            'testiqBtn'=>(int)$tesdiqBtn,
            'edits'=>addslashes(json_encode($edits)),
            'imtinaSebeb'=>$sebeb22,
            'imtinaEden'=>$imtinaEdens,
            'imtina_edenin_uid'=>$imtina_uid,
            'arayish_teleb_etdi'=>(int)$gorushInf['arayish_teleb_etdi'],
            'arayish_id'=>(int)$gorushInf['arayish_id'],
            'arayish_teleb_etdi_ad'=>htmlspecialchars($gorushInf['arayish_teleb_etdi_ad']),
            'editImtinaBtn'=>(((int)$gorushInf['status']!==3 && (in_array($userId,explode(",",$gorushInf['tesdiqleme_geden_userler']))))  || $userId==$gorushInf['user_id'] || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$gorushInf['tesdiqleme_geden_userler']))) || $userId==$gorushInf['gorushu_teyin_eden_user'])?1:0,
            'qebulBtn'=>(isset($_POST['forTimecontrol']) || isset($_POST['forReportp1'])) ? false : ($gorushInf['status']==2 && $userId==$gorushInf['user_id']),
            'sirket'=>htmlspecialchars($getCompInf[0]),
            'shirket_id'=>(int)$gorushInf['company'],
            'teyineden'=>htmlspecialchars($getUserInf2[0]),
            'kim'=>htmlspecialchars($getUserInf1[0]),
            'istirakcilar'=>count($istirakchilary)==0?"<i style='border-bottom:1px dotted red;'>".dil::soz("11yoxdur")."</i>":implode(", ", $istirakchilary),
            'musteri'=>$gorushInf['customer']==0?"<i>".dil::soz("11teyinolunmayib")."</i>":($gorushInf['customer']==""?"<i style='border-bottom:1px dotted red;'>".dil::soz("11hechkim")."</i>":htmlspecialchars($getCustomerInfo[0])),
            'tarix'=>date("d-m-Y", strtotime($gorushInf['tarix'])).' saat '.htmlspecialchars(substr($gorushInf['start_date'],0,5)).' - '.htmlspecialchars(substr($gorushInf['end_date'],0,5)),
            'yolVaxti'=>($yolvaxti<10 ? "0" . $yolvaxti : $yolvaxti) . ":" . ($yolVaxtiS<10 ? "0" . $yolVaxtiS : $yolVaxtiS),
            'teyinat1'=>$teyinatlar1,
            'teyinat2'=>$teyinatlar2,
            'melumat'=>htmlspecialchars($gorushInf['about']),
            'tesdiqleyenler'=>$tr1==""?"<i style='border-bottom:1px dotted red;'>".dil::soz("11yoxdur")."</i>":$tr1,
            'tesdiqlemeyenler'=>$tr2==""?"<i style='border-bottom:1px dotted red;'>".dil::soz("11yoxdur")."</i>":$tr2,
            'teyinedenuser'=>(int)$gorushInf['gorushu_teyin_eden_user'],
            'ofisde'=>(int)$gorushInf['ofisde'],

        );
        if(isset($pri['gorush_report']) && isset($parametrler['report']) && $parametrler['report']=='gorush')
        {
            $elementler['testiqBtn']=0;
            $elementler['editImtinaBtn']=0;
            $elementler['qebulBtn']=false;
            $elementler['teyinedenuser']=0;
        }
        print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/gorush_etrafli',$elementler, 'prodoc'))));
    }
    else
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red; padding:20px;'>Səhv! Olmaz!</div>",ENT_QUOTES)));
        exit();
    }

}