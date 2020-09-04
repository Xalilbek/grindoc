<?php
if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']))
{

    $MN = 0;
    $userId = (int)$_SESSION['erpuserid'];
    $getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
    $userGroup = (int)$getUserInfo['Groupp'];
    $pri_check = pdof()->query("SELECT module,(SELECT TOP 1 privs FROM tb_group_privs_new tb2 WHERE tb2.group_id=$userGroup AND tb2.modul_id=tb1.id) privs FROM tb_modules tb1 WHERE tb1.module='icazeler' OR tb1.module='report_id1_new'");

    $pri = array();
    while($aa = $pri_check->fetch())
    {
        $pri[$aa['module']]=$aa["privs"];
    }
    $sid = (int)$parametrler['sid'];
    $ezamiyyetInf = pdof()->query("SELECT tb8.ad AS valyuta_name,tb1.*,tb2.ad AS sheher_ad,tb3.ad AS olke_ad,tb4.struktur_bolmesi as bolme_ad,tb5.Adi AS shirket_ad,v_user_adlar.user_ad_qisa AS arayish_teleb_etdi_ad,tb10.id AS arayish_id FROM tb_ezamiyyetler tb1 LEFT JOIN tb_general_cities tb2 ON tb2.id=tb1.sheher LEFT JOIN tb_general_countries tb3 ON tb3.id=tb1.olke LEFT JOIN tb_Struktur tb4 ON tb4.struktur_id=tb1.bolme LEFT JOIN tb_istehsalchilar tb5 ON tb5.id=tb1.shirket LEFT JOIN v_user_adlar ON tb1.arayish_teleb_etdi=v_user_adlar.USERID LEFT JOIN tb_arayishlar tb10 ON tb10.menbe='ezamiyyetler' AND tb10.menbe_id=tb1.id LEFT JOIN tb_valyuta 	tb8 ON tb1.valyuta = tb8.id WHERE tb1.id='$sid'")->fetch();
    $goster = false;

    if(
        (
            (int)$ezamiyyetInf['user_id']===$userId ||
            (int)$ezamiyyetInf['elave_eden_user']===$userId ||
            in_array($userId,explode(",",$ezamiyyetInf['rehberler'])) ||
            in_array($userId,explode(",",$ezamiyyetInf['tesdiqleme_geden_userler'])) ||
            pdof()->query("SELECT 1 FROM tb_ezamiyyetler_tesdiqleme WHERE ezamiyyet_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()
        )
        ||
        (
            isset($_POST['forTimecontrol']) &&
            $_POST['forTimecontrol']==true && $pri['report_id1_new']==2
        )
        ||
        (
            // tab_diger
            !in_array($userId,explode(",",$ezamiyyetInf['rehberler'])) &&
            (int)$ezamiyyetInf['accountant']!==$userId &&
            (int)pdof()->query("SELECT COUNT(id) FROM tb_ezamiyyetler_tesdiqleme WHERE ezamiyyet_id='$sid' AND vekaletname_user='$userId'")->fetchColumn() === 0 &&
            (int)$ezamiyyetInf['user_id']!==$userId

        )
    )
    {
        $carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_ezamiyyetler_tesdiqleme WHERE ezamiyyet_id='$sid' AND status='0'")->fetch();
        $carQrup = (int)$carQrup[0];
        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_ezamiyyetler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.ezamiyyet_id='$sid' AND emeliyyat_tip<>'emr_hazirlanmasi'");
        $tr1 = '';
        $tr2 = '';
        $edits = '';
        $tesdiqBtn = false;
        $vekaletnameUserYoxla = 0;
        while($trInfo = $tesdiqleyenler->fetch())
        {
            if((int)$trInfo['status']===1)
            {
                $tr1 .= "<div visit_uid=".(int)$trInfo['user_id']." >".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("25evmore")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>E-Vəkalətnamə</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
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
                $tr2 .= "<div visit_uid=".(int)$trInfo['user_id'].">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("25evmore")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>E-Vəkalətnamə</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }
        if($tesdiqBtn && (in_array($userId,explode(",",$ezamiyyetInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$ezamiyyetInf['tesdiqleme_geden_userler']))))===false)
        {
            $tesdiqBtn = false;
        }
        $imtinaBtn = false;
        if((int)$ezamiyyetInf['status']!==3 && (in_array($userId,explode(",",$ezamiyyetInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$ezamiyyetInf['tesdiqleme_geden_userler']))) || $userId==$ezamiyyetInf['user_id'] || $userId==$ezamiyyetInf['elave_eden_user']))
        {
            $imtinaBtn = true;
        }

        $getImtinaEden = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$ezamiyyetInf['imtinaEden']."'")->fetch();
        $getLogsEdit = pdof()->query("SELECT * FROM tb_ezamiyyetler_logs WHERE ezamiyyet_id='" . $sid . "' AND ne='1'");
        while($logEdit = $getLogsEdit->fetch())
        {
            $getuserInff = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$logEdit['user_id'] . "'")->fetch();
            $edits.='<span visit_uid='.(int)$logEdit['user_id'].'>'.$getuserInff[0].'</span> -' .date("d-m-Y H:i", strtotime($logEdit['date'])).'<br>'.'Səbəb: '.htmlspecialchars($logEdit['qeyd']).'<br/>';
        }
        $getUserInf2 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$ezamiyyetInf['elave_eden_user']."'")->fetch();
        $getUserInf1 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$ezamiyyetInf['user_id'] . "'")->fetch();
        $melumat['edits'] = json_encode($edits);
        $txt=htmlspecialchars(((int)$ezamiyyetInf['shirket_daxili']!=0?$ezamiyyetInf['bolme_ad']:($ezamiyyetInf['olke_ad'].", ".$ezamiyyetInf['sheher_ad'].(trim($ezamiyyetInf['shirket_ad'])==""?"":", ".$ezamiyyetInf['shirket_ad']))));

        $qalacaqi_yer = array();
        foreach(explode(",",$ezamiyyetInf['qalacaqi_yer']) AS $qYer4)
        {
            if($qYer4==1)
            {
                $qalacaqi_yer[] = dil::soz("73Mehmanxana");
            }
            else if($qYer4==2)
            {
                $qalacaqi_yer[] = dil::soz("73Qonaq evi");
            }
            else if($qYer4==3)
            {
                $qalacaqi_yer[] = dil::soz("73Kirayə ev");
            }
        }
        $ezamiyyetInf['qalacaqi_yer'] = implode(", ",$qalacaqi_yer);

        foreach(explode(",",$ezamiyyetInf['qalacaqi_yer']) AS $qYer4)
        {
            if($qYer4==1)
            {
                $qalacaqi_yer[] =   dil::soz("73Mehmanxana");
            }
            else if($qYer4==2)
            {
                $qalacaqi_yer[] =  dil::soz("73Qonaq evi");
            }
            else if($qYer4==3)
            {
                $qalacaqi_yer[] =  dil::soz("73Kirayə ev");
            }
        }
        $attachment = "";
        foreach(explode(",",$ezamiyyetInf['attachment']) AS $sened)
        {
            if(trim($sened)!="")
            {
                $attachment .= '<div style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/proid/business_trips/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/proid/business_trips/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
            }
        }
        $ezamiyyetInf['qalacaqi_yer'] = implode(", ",$qalacaqi_yer);
        $hasOrder = pdof()->query("SELECT * FROM tb_prodoc_business_trip WHERE business_trip_id='$sid'")->fetch();
        $order_btn = ($ezamiyyetInf['status']==1 && pdof()->query("SELECT * FROM tb_ezamiyyetler_tesdiqleme WHERE emeliyyat_tip='emr_hazirlanmasi' AND ezamiyyet_id='$sid' AND user_id='$userId'")->fetch() && !$hasOrder) ? sprintf('<button class="btn btn-warning" onclick="templateYukle(\'prodoc_business_trip\',\''.dil::soz("25eeadd").'\',{\'sid\':0,\'business_trip_id\':\''.$sid.'\'},65,true);" type="button">%s</button>', dil::soz("73Əmr formalaşdır")) : '';
        $employes = "";
        $getEmployes = pdof()->query("SELECT *,(SELECT user_ad FROM v_user_adlar WHERE USERID=user_id) AS user_ad FROM tb_proid_business_trip_users WHERE eid='$sid' AND (order_id='0' OR order_id='-1')");
        foreach($getEmployes AS $employeInf)
        {
            $employes .= "<span visit_uid=".(int)$employeInf['user_id'].">".htmlspecialchars($employeInf['user_ad'])."</span>";
        }

        $elementler = array(
            "sid"=>$sid,
            "MN"=>$MN,
            "userId"=>$userId,
            "imtina_sebeb"=>htmlspecialchars($ezamiyyetInf['imtinaSebebi']),
            "imtina_vez"=>$getImtinaEden[0]==''?'display:none':'',
            "imtina_eden_ad"=>$getImtinaEden[0],
            "imtinaBtn"=>$imtinaBtn && !$hasOrder?'':'display:none;',
            "edit"=>$edits,
            "deyish_disp"=>$edits==''?'display:none':'',
            "kim"=>$employes,
            "kim_id"=>(int)$ezamiyyetInf['user_id'],
            "testiqleyibler"=>($tr1==""?" - ".dil::soz("ezam_etrafli_hechlim")."":$tr1),
            "testiqleyecekler"=>($tr2==""?" - ".dil::soz("ezam_etrafli_yoxdur")."":$tr2),
            "start_date"=>date("d-m-Y",strtotime($ezamiyyetInf['start_date'])),
            "end_date"=>date("d-m-Y",strtotime($ezamiyyetInf['end_date'])),
            "about"=>htmlspecialchars($ezamiyyetInf['about']),
            "total_cost"=>htmlspecialchars($ezamiyyetInf['total_cost']),
            "base"=>htmlspecialchars($ezamiyyetInf['base']),
            "valyuta_name"=>htmlspecialchars($ezamiyyetInf['valyuta_name']),
            "ezam_olunduqu_yer"=>htmlspecialchars($ezamiyyetInf['ezam_olunduqu_yer']),
            "elaqe_nomreleri"=>htmlspecialchars($ezamiyyetInf['elaqe_nomreleri']),
            "otel_xercleri"  =>htmlspecialchars($ezamiyyetInf['otel_xercleri']),
            // "yemek_xercleri"  =>htmlspecialchars($ezamiyyetInf['yemek_xercleri']),
            "qalacaqi_yer"=>htmlspecialchars($ezamiyyetInf['qalacaqi_yer']),
            //"avans" => htmlspecialchars($ezamiyyetInf['total_cost']),
            // "neqliyyat_novu"=>$neqliyyat_novu2,
            "qarshilanma_telebi"=>$ezamiyyetInf['qarshilanma_telebi']==1?"".dil::soz("ezam_etrafli_Bəli")." , ".dil::soz("ezam_etrafli_Saat").": ".date("H:i",strtotime($ezamiyyetInf['qarshilanma_saati'])):"".dil::soz("ezam_etrafli_Xeyr")."",
            "olkeSheherTxt"=>$txt==''?'- '.dil::soz("ezam_etrafli_yoxdur").'':$txt,
            "elave_eden_user"=>"<span visit_uid=".$ezamiyyetInf['elave_eden_user'].">".htmlspecialchars($getUserInf2[0])."</span>",
            "edit_vez"=>($userId==(int)$ezamiyyetInf['elave_eden_user'] && !$hasOrder) ?'':'display:none;',
            "arayish_teleb_etdi_ad"=>$ezamiyyetInf['arayish_teleb_etdi_ad']==''?''.dil::soz("ezam_etrafli_Yox").'':htmlspecialchars($ezamiyyetInf['arayish_teleb_etdi_ad']),
            "bolme"=>htmlspecialchars($ezamiyyetInf['bolme_ad']),
            "testiqBtn"=>$tesdiqBtn?'':'display:none;',
            "attachment"=>$attachment,
            "order_btn"=>$order_btn,

            "ezam_etrafli_Ezam olunacaq şəxs" => dil::soz("ezam_etrafli_Ezam olunacaq şəxs"),
            "ezam_etrafli_Təyin edən şəxs" => dil::soz("ezam_etrafli_Təyin edən şəxs"),
            "ezam_etrafli_Ezam olunacaq yer" => dil::soz("ezam_etrafli_Ezam olunacaq yer"),
            "ezam_etrafli_Gedəcəyi ünvan" => dil::soz("ezam_etrafli_Gedəcəyi ünvan"),
            "ezam_etrafli_Tarix" => dil::soz("ezam_etrafli_Tarix"),
            "ezam_etrafli_Nəqliyyat növü" => dil::soz("ezam_etrafli_Nəqliyyat növü"),
            "ezam_etrafli_Qarşılanma tələbi" => dil::soz("ezam_etrafli_Qarşılanma tələbi"),
            "ezam_etrafli_Qalacaq yer" => dil::soz("ezam_etrafli_Qalacaq yer"),
            "ezam_etrafli_Təsdiqləməyənlər" => dil::soz("ezam_etrafli_Təsdiqləməyənlər"),
            "ezam_etrafli_Təsdiqləyənlər" => dil::soz("ezam_etrafli_Təsdiqləyənlər"),
            "ezam_etrafli_Fayl" => dil::soz("ezam_etrafli_Fayl"),
            "ezam_etrafli_Ezamiyyətin məqsədi" => dil::soz("ezam_etrafli_Ezamiyyətin məqsədi"),
            "ezam_etrafli_Dəyişdirilib" => dil::soz("ezam_etrafli_Dəyişdirilib"),
            "ezam_etrafli_Imtina edib" => dil::soz("ezam_etrafli_Imtina edib"),
            "ezam_etrafli_Imtina səbəbi" => dil::soz("ezam_etrafli_Imtina səbəbi"),
            "ezam_etrafli_Arayış tələb etdi" => dil::soz("ezam_etrafli_Arayış tələb etdi"),
            "ezam_etrafli_Düzəliş et" => dil::soz("ezam_etrafli_Düzəliş et"),
            "ezam_etrafli_Təsdiqlə" => dil::soz("ezam_etrafli_Təsdiqlə"),
            "ezam_etrafli_Tarixçə" => dil::soz("ezam_etrafli_Tarixçə"),
            "ezam_etrafli_İmtina" => dil::soz("ezam_etrafli_İmtina"),
            "ezam_etrafli_Bağla" => dil::soz("ezam_etrafli_Bağla"),
            "ezam_etrafli_Arayısh tələb et" => dil::soz("ezam_etrafli_Arayısh tələb et"),
            "ezam_etrafli_İmtina et" => dil::soz("ezam_etrafli_İmtina et"),
            "ezam_etrafli_Səbəb" => dil::soz("ezam_etrafli_Səbəb"),
            "ezam_etrafli_Səbəbi daxil edin" => dil::soz("ezam_etrafli_Səbəbi daxil edin"),
            "ezam_etrafli_Imtina edilib" => dil::soz("ezam_etrafli_Imtina edilib"),
            "ezam_etrafli_İcazəSənədintarixçəsi" => dil::soz("ezam_etrafli_İcazəSənədintarixçəsi"),

            "yeni_ezam_Əlaqə nömrələri" => dil::soz("yeni_ezam_Əlaqə nömrələri"),
            "yeni_ezam_Otel xərcləri"   => dil::soz("yeni_ezam_Otel xərcləri"),
            "yeni_ezam_yemek xərcləri" => dil::soz("yeni_ezam_yemek xərcləri"),
            "122avans" => dil::soz("122avans")

        );

        $neqliyyat_novleri = array(
            0 => '-',
            1 => dil::soz("yeni_ezam_Qatar"),
            2 => dil::soz("yeni_ezam_Şirkətin maşını"),
            3 => dil::soz("yeni_ezam_Təyarrə"),
            4 => dil::soz("yeni_ezam_Şəxsi maşın"),
            5 => dil::soz("yeni_ezam_Digər")
        );
        $valyutalar = array(
            0 => '-'
        );
        foreach (fetchAll("SELECT * FROM tb_valyuta") as $valyuta)
        {
            $valyutalar[$valyuta['id']] = $valyuta['ad'];
        }

        $elementler['nov_xerc_table'] = "";
        $nov_xerc = json_decode($ezamiyyetInf['neqliyyat_novu']);

        if (is_array($nov_xerc))
        {
            $elementler['nov_xerc_table'] .= "<table class='table table-striped table-bordered table-advance table-hover'><tdody>";
            $elementler['nov_xerc_table'] .= sprintf("<thead><th>%s</th><th>%s</th></thead>", dil::soz("yeni_ezam_Nəqliyyat növü"), dil::soz("yeni_ezam_Nəqliyyat xərci"));
            for ($i = 0, $len = count($nov_xerc); $i < $len; ++$i)
            {
                $elementler['nov_xerc_table'] .= "<tr>";
                $elementler['nov_xerc_table'] .= sprintf("<td>%s</td>", $neqliyyat_novleri[(int)$nov_xerc[$i][0]]);
                $elementler['nov_xerc_table'] .= "<td>{$nov_xerc[$i][1]}";
                $elementler['nov_xerc_table'] .= sprintf(" %s</td>", $valyutalar[(int)$nov_xerc[$i][2]]);
                $elementler['nov_xerc_table'] .= "</tr>";
            }
            $elementler['nov_xerc_table'] .= "</table></tdody>";
        }

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($ezamiyyetInf['document_id']);

        require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';

        $elementler["detailed_information"] = $intDoc->getDetailedInformationHTML();

        $elementler['mezuniyyet_emri'] = "-";
        if ($hasOrder !== FALSE) {
            require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
            $elementler['mezuniyyet_emri'] = getDocumentNumberByDocumentId($hasOrder['document_id']);
        }

        print json_encode(array("status" => "success", "html"=>$user->template_yukle('daxili_senedler/ezamiyyet_etrafli',$elementler, 'prodoc'),ENT_QUOTES));
    }
    else{
        print json_encode(array("status"=>"hazir","template"=>"Bu ezamiyyətə baxa bilməssiz!!!"));
    }
}