<?php

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $MN = 0;
    $pr = (int)$user->checkPrivilegia("icazeler");
    $sid = (int)$parametrler['sid'];
    $userId = (int)$_SESSION['erpuserid'];
    $language = $user->getLang();
    $dill = new dilStclass($language);

    $getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
    $userGroup = (int)$getUserInfo['Groupp'];

    //$pri_check = pdof()->query("SELECT module,(SELECT privs FROM tb_group_privs_new tb2 WHERE tb2.group_id=$userGroup AND tb2.modul_id=tb1.id) privs FROM tb_modules tb1 WHERE  tb1.module='icazeler' OR tb1.module='report_mevacib'");
    //
    //$pri = array();
    //while($aa = $pri_check->fetch())
    //{
    //	$pri[$aa['module']]=$aa["privs"];
    //}

    $icazeInf = pdof()->query("SELECT * FROM v_icazeler WHERE id='$sid'")->fetch();

    if(((int)$icazeInf['user_id']===$userId || (int)$icazeInf['elave_eden_user']===$userId || in_array($userId,explode(",",$icazeInf['rehberler'])) || in_array($userId,explode(",",$icazeInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_icazeler_tesdiqleme WHERE icaze_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()) || $pr>0)//|| $pri['icazeler']==1 || @$pri['report_mevacib']!=0
    {
        $changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='icazeler' AND kid='$sid' AND user_id='$userId'");

        $getUserInf2 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$icazeInf['elave_eden_user'] . "'")->fetch();
        $imtinaSebeb = "";
        $imtinaEden = "";
        if($icazeInf['status']==3)
        {
            $imtinaEden = (int)$icazeInf['imtinaEden'];
            if($imtinaEden===0)
            {
                $imtinaEdenInf = array("Avtomatik");
            }
            else
            {
                $imtinaEdenInf = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='$imtinaEden'")->fetch();
            }
            $sebeb = $icazeInf['imtinaSebebi'];
            $getImtinaDate = pdof()->query("SELECT TOP 1 date FROM tb_icazeler_logs WHERE icaze_id='$sid' AND ne='4' ORDER BY date DESC")->fetch();
            $imtinaSebeb = htmlspecialchars($sebeb);
            $imtinaEden = htmlspecialchars($imtinaEdenInf[0]) . " - " . date("d-m-Y H:i:s", strtotime($getImtinaDate[0]));
        }

        $edits = "";
        $edits1 = "";
        $getLogsEdit = pdof()->query("SELECT * FROM tb_icazeler_logs WHERE icaze_id='" . $sid . "' AND ne='1'");
        while($logEdit = $getLogsEdit->fetch())
        {
            if((int)$logEdit['user_id']===0)
            {
                $getuserInff = array("Avtomatik");
            }
            else
            {
                $getuserInff = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$logEdit['user_id'] . "'")->fetch();
            }
            $edits1 = "11";
            $edits .= "<div visit_uid=".(int)$logEdit['user_id'].">".htmlspecialchars($getuserInff[0],ENT_QUOTES)." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s", strtotime($logEdit['date']))."<br>".dil::soz("10sebeb").": ".htmlspecialchars($logEdit['qeyd'])."</div>";
        }

        $carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_icazeler_tesdiqleme WHERE icaze_id='$sid' AND status='0'")->fetch();
        $carQrup = (int)$carQrup[0];

        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_icazeler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.icaze_id='$sid' AND tb1.emeliyyat_tip='tesdiqleme'");
        $tr1 = '';
        $tr2 = '';

        $tesdiqBtn = false;
        $vekaletnameUserYoxla = 0;

        while($trInfo = $tesdiqleyenler->fetch())
        {
            if((int)$trInfo['status']===1)
            {
                $tr1 .= "<div visit_uid=".(int)$trInfo['user_id']." >".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("10elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("10evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
            }
            else
            {
                if((int)$trInfo['vekaletname_user']===$userId && $carQrup==(int)$trInfo['qrup'])
                {
                    $vekaletnameUserYoxla = (int)$trInfo['user_id'];
                    $tesdiqBtn = true;
                }
                if((int)$trInfo['user_id']===$userId && $carQrup==(int)$trInfo['qrup'])
                {
                    $tesdiqBtn = true;
                }
                $tr2 .= "<div visit_uid=".(int)$trInfo['user_id'].">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("10elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("10evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }

        if(($tesdiqBtn && (in_array($userId,explode(",",$icazeInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$icazeInf['tesdiqleme_geden_userler']))))===false) || (int)$icazeInf['status']==3)
        {
            $tesdiqBtn = false;
        }
        $imtinaBtn = false;
        if((int)$icazeInf['status']!==3 && (in_array($userId,explode(",",$icazeInf['tesdiqleme_geden_userler'])) || $userId==$icazeInf['user_id'] || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$icazeInf['tesdiqleme_geden_userler'])))))
        {
            $imtinaBtn = true;
        }
        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($icazeInf['document_id']);

        $elementler = array(
            "sid"=>$sid,
            "detailed_information" => $intDoc->getDetailedInformationHTML(),

            "10icazeliShexs"=>htmlspecialchars(dil::soz('10icazeliShexs'),ENT_QUOTES),
            "10tarixche"=>htmlspecialchars(dil::soz('10tarixche'),ENT_QUOTES),
            "10icazeAlanShexs"=>htmlspecialchars(dil::soz('10icazeAlanShexs'),ENT_QUOTES),
            "10testiqlemeyenRehberler"=>htmlspecialchars(dil::soz('10testiqlemeyenRehberler'),ENT_QUOTES),
            "10testiqleyenRehberler"=>htmlspecialchars(dil::soz('10testiqleyenRehberler'),ENT_QUOTES),
            "10deyishdirilib"=>htmlspecialchars(dil::soz('10deyishdirilib'),ENT_QUOTES),
            "10imtinaEdib"=>htmlspecialchars(dil::soz('10imtinaEdib'),ENT_QUOTES),
            "10sebeb"=>htmlspecialchars(dil::soz('10sebeb'),ENT_QUOTES),
            "10edit"=>htmlspecialchars(dil::soz('10edit'),ENT_QUOTES),
            "10testiqle"=>htmlspecialchars(dil::soz('10testiqle'),ENT_QUOTES),
            "10tarix"=>htmlspecialchars(dil::soz('10tarix'),ENT_QUOTES),
            "10melumat"=>htmlspecialchars(dil::soz('10melumat'),ENT_QUOTES),
            "10imtina"=>htmlspecialchars(dil::soz('10imtina'),ENT_QUOTES),
            "10bagla"=>htmlspecialchars(dil::soz('10bagla'),ENT_QUOTES),
            "10duzelishEt"=>htmlspecialchars(dil::soz('10duzelishEt'),ENT_QUOTES),
            "10testiqlenmeyib"=>htmlspecialchars(dil::soz('10testiqlenmeyib'),ENT_QUOTES),
            "10testiqlenib"=>htmlspecialchars(dil::soz('10testiqlenib'),ENT_QUOTES),
            "10imtinaEdilib"=>htmlspecialchars(dil::soz('10imtinaEdilib'),ENT_QUOTES),


            "MN"=>$MN,
            "userId"=>$userId,
            "from"=>(int)$icazeInf['from'],
            "icaze_elave_uid"=>$icazeInf['elave_eden_user'],
            "icaze_alinan_uid"=>(int)$icazeInf['user_id'],
            "edits"=>$edits,
            "edits1"=>$edits1,
            'imtinaSebeb'=>$imtinaSebeb,
            'imtinaEden'=>$imtinaEden,
            'imtinaEden_uid'=>(int)$icazeInf['imtinaEden'],
            'tr1'=>($tr1===""?" - ".dil::soz("10yoxdur")."":$tr1),
            'tr2'=>($tr2===''?" - ".dil::soz("10yoxdur")."":$tr2),
            'tip'=>(int)$icazeInf['tip']==0?dil::soz("10odenishli"):((int)$icazeInf['tip']==1?dil::soz("10xidmeticaze"):dil::soz("10hekimicazesi")),
            'icaze_alan'=>htmlspecialchars($getUserInf2[0]),
            'tarix'=>date("d-m-Y H:i", strtotime($icazeInf['start_date']))." - ".date("d-m-Y H:i", strtotime($icazeInf['end_date'])),
            'kim'=>htmlspecialchars($icazeInf['user_ad']),
            'melumat'=>htmlspecialchars($icazeInf['about']),
            'teyinat1'=>htmlspecialchars($icazeInf['teyinat1_ad']),
            'teyinat2'=>htmlspecialchars($icazeInf['teyinat2_ad']),
            'tesdiqBtn'=>(int)$tesdiqBtn,
            'imtinaBtn'=>(int)$imtinaBtn,
            'editBtn'=>(int)($userId==$icazeInf['user_id'] || $userId==$icazeInf['elave_eden_user']),
            "10icazetip" => dil::soz("10icazetip"),
            "10imtinaet" => dil::soz("10imtinaet"),
            "10sebebidaxiledin" => dil::soz("10sebebidaxiledin"),
            "10yeniIcaze" => dil::soz("10yeniIcaze"),
            "10İcazəSənədintarixçəsi" => dil::soz("10İcazəSənədintarixçəsi"),
        );
        print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/icaze_etrafli',$elementler, 'prodoc'))));
    }
    else
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red;'>".dil::soz("10sehv_olmaz")."</div>",ENT_QUOTES)));
        exit();
    }
}
