<?php

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']))
{
    $MN = 0;
	$melumat=array();
	$userId = (int)$_SESSION['erpuserid'];
	$getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
	$userGroup = (int)$getUserInfo['Groupp'];
	
	$pri_check = pdof()->query("SELECT module,(SELECT TOP 1 privs FROM tb_group_privs_new tb2 WHERE tb2.group_id=$userGroup AND tb2.modul_id=tb1.id) privs FROM tb_modules tb1 WHERE tb1.module='icazeler' OR tb1.module='report_id1_new' OR tb1.module='mezuniyyetler'");
	
	$pri = array();
	while($aa = $pri_check->fetch())
	{
		$pri[$aa['module']]=$aa["privs"];
	}
	
	$gid = (int)$parametrler['sid'];
	$mezuniyyetInf = pdof()->query("SELECT *,(SELECT dovr FROM tb_teyinatlar WHERE id=tb1.vacation_type) AS vacation_period FROM v_mezuniyyetler tb1 WHERE id='$gid'")->fetch();

//	if( ((int)$mezuniyyetInf['user_id']===$userId || (int)$mezuniyyetInf['elave_eden_user']===$userId || in_array($userId,explode(",",$mezuniyyetInf['rehberler'])) || in_array($userId,explode(",",$mezuniyyetInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_mezuniyyetler_tesdiqleme WHERE mezuniyyet_id='$gid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()) || $pri['icazeler']==1 || (isset($_POST['forTimecontrol']) && $_POST['forTimecontrol']==true && @$pri['report_id1_new']==2) || (isset($_POST['forReportp1']) && $_POST['forReportp1']==true) || (isset($pri['mezuniyyetler']) && (int)$pri['mezuniyyetler']===2))
//	{
		$changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='mezuniyyetler' AND kid='$gid' AND user_id='$userId'");
		$melumat = array();
		$melumat['MN'] =$MN;
		$melumat['gid'] =$gid;
		$melumat['elave_eden_user_uid'] = $mezuniyyetInf['elave_eden_user'];
		$melumat['status'] ="hazir";
		$melumat['start_date'] = date("d-m-Y", strtotime($mezuniyyetInf['start_date']));
		$melumat['end_date'] = date("d-m-Y", strtotime($mezuniyyetInf['end_date']));
		$getUserInf1 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$mezuniyyetInf['user_id'] . "'")->fetch();
		$melumat['kim'] = htmlspecialchars($getUserInf1[0]);
		$melumat['kim_id'] = (int)$mezuniyyetInf['user_id'];
		$melumat['mezuniyyet_tip'] = (int)$mezuniyyetInf['mezuniyyet_tip']=="1"?"".dil::soz("26_mez_etr_Ödənişli")."":"".dil::soz("26_mez_etr_Ödənişsiz")."";
		$melumat['gunluk_emek_haqqi'] = ( $mezuniyyetInf['mezuniyyet_tip'] == 1 ? $user->r_round( $mezuniyyetInf['gunluk_emek_haqqi'] , 2 )." ".dil::soz("26_mez_etr_AZN")." <i class=\"fa fa-info-circle\" style=\"cursor: pointer;\" onclick='templateYukle(\"gunlukEmekhaqqiEtrafli\",\"$26_mez_etr_ətrafli_melumat$\",{\"mid\":$gid},0,true);'></i>" : ' - ' );
		$melumat['about'] = htmlspecialchars($mezuniyyetInf['about']);
		$getUserInf2 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$mezuniyyetInf['elave_eden_user'] . "'")->fetch();
		$melumat['elave_eden_user'] = htmlspecialchars($getUserInf2[0]);
		
		$carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_mezuniyyetler_tesdiqleme WHERE mezuniyyet_id='$gid' AND status='0'")->fetch();
		$carQrup = (int)$carQrup[0];
		$tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_mezuniyyetler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.mezuniyyet_id='$gid' AND (emeliyyat_tip='tesdiqleme' OR emeliyyat_tip='hr_approve')")->fetchAll();
		$tr1 = '';
		$tr2 = '';
		$tesdiqBtn = false;
		$HRapproveBTN = false;
		$vekaletnameUserYoxla = 0;
		foreach($tesdiqleyenler AS $trInfo)
		{
			if((int)$trInfo['status']===1)
			{
				if((int)$trInfo['user_id']===$userId && $carQrup==(int)$trInfo['qrup'])
				{
					$tesdiqBtn = false;
				}
				$tr1 .= "<div visit_uid=".$trInfo['user_id'].">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("26_mez_etr_Elektron vəkalətnamə - Ətraflı")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>E-Vəkalətnamə</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
			}
			else
			{
				if((int)$trInfo['vekaletname_user']===$userId && $carQrup==(int)$trInfo['qrup'] && $trInfo['emeliyyat_tip']=="tesdiqleme")
				{
					$vekaletnameUserYoxla = (int)$trInfo['user_id'];
					$tesdiqBtn = true;
				}
				if((int)$trInfo['user_id']===$userId && $carQrup==(int)$trInfo['qrup'] && $trInfo['emeliyyat_tip']=="tesdiqleme")
				{
					$tesdiqBtn = true;
				}
				if((int)$trInfo['user_id']===$userId && $carQrup==(int)$trInfo['qrup'] && $trInfo['emeliyyat_tip']=="hr_approve")
				{
					if($mezuniyyetInf['vacation_period']!=1)
					{
						$tesdiqBtn = true;
					}
					else
					{
						$HRapproveBTN = true;
					}
				}
				$tr2 .= "<span visit_uid=".$trInfo['user_id'].">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("26_mez_etr_Elektron vəkalətnamə - Ətraflı")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>E-Vəkalətnamə</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</span>";
			}
		}
		
		$start_date = date("m/d/Y",strtotime($mezuniyyetInf['start_date']));
		$end_date = date("m/d/Y",strtotime($mezuniyyetInf['end_date']));
		
		$attachment = "&nbsp;";
		foreach(explode(",",$mezuniyyetInf['attachment']) AS $sened)
		{
			if(trim($sened)!="")
			{
				$attachment .= '<span style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/proid/vacations/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/proid/vacations/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></span>';
			}
		}
		$melumat['attachment'] = $attachment;
		$melumat['nece_gun'] = $mezuniyyetInf['number_of_days'];
		$imtinaBtn = false;
		if((int)$mezuniyyetInf['status']!==3 && (in_array($userId,explode(",",$mezuniyyetInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mezuniyyetInf['tesdiqleme_geden_userler']))) || $userId==$mezuniyyetInf['user_id'] || $userId==$mezuniyyetInf['elave_eden_user']))
		{
			$imtinaBtn = true;
		}
		$hasOrder = pdof()->query("SELECT * FROM tb_prodoc_vacation_orders WHERE vacation_id='$gid'")->fetch();
		$melumat['testiqleyibler'] = ($tr1==""?" - ".dil::soz("26_mez_etr_heçkim")."":$tr1);
		$melumat['testiqleyecekler'] = $tr2==""?" - ".dil::soz("26_mez_etr_yoxdur")."":$tr2;
		$melumat['testiqBtn'] = (int)$tesdiqBtn;
		$melumat['testiqBtn'] = $melumat['testiqBtn']==0?'display:none':'display:inline';
		$melumat['HRapproveBTN'] = $HRapproveBTN==false?'display:none':'display:inline';
		$melumat['editImtinaBtn'] = (isset($_POST['forTimecontrol']) || isset($_POST['forReportp1'])) ? 0 : $imtinaBtn;
		$melumat['editImtinaBtn'] = $hasOrder || $melumat['editImtinaBtn']==0?'display:none':'display:inline';
		$melumat['editBtn'] = $mezuniyyetInf['elave_eden_user']==$userId&&!$hasOrder?'display:inline':'display:none';
		
		$vacation_type = (int)$mezuniyyetInf['vacation_type'];
		$getTeyinatInf = pdof()->query("SELECT * FROM tb_teyinatlar WHERE id='" .(int)$vacation_type. "'")->fetch();
		$melumat['vacation_type'] = htmlspecialchars($getTeyinatInf['tip']);
		
		$melumat['26_mez_etr_mezuniyyet_alacaq_shexs'] = dil::soz("26_mez_etr_mezuniyyet_alacaq_shexs");
		$melumat['26_mez_etr_mezuniyyeti_teyin_edib'] = dil::soz("26_mez_etr_mezuniyyeti_teyin_edib");
		$melumat['26_mez_etr_1_gunluk_emek_haqqi'] = dil::soz("26_mez_etr_1_gunluk_emek_haqqi");
		$melumat['26_mez_etr_ətrafli_melumat'] = dil::soz("26_mez_etr_ətrafli_melumat");
		$melumat['26_mez_etr_tarix'] = dil::soz("26_mez_etr_tarix");
		$melumat['26_mez_etr_teyinat'] = dil::soz("26_mez_etr_teyinat");
		$melumat['26_mez_etr_mezuniyyet_tipi'] = dil::soz("26_mez_etr_mezuniyyet_tipi");
		$melumat['26_mez_etr_fayl'] = dil::soz("26_mez_etr_fayl");
		$melumat['26_mez_etr_melumat'] = dil::soz("26_mez_etr_melumat");
		$melumat['26_mez_etr_tesdiqleyen_rehberler'] = dil::soz("26_mez_etr_tesdiqleyen_rehberler");
		$melumat['26_mez_etr_tesdiqlemeyen_rehberler'] = dil::soz("26_mez_etr_tesdiqlemeyen_rehberler");
		$melumat['26_mez_etr_tarixce'] = dil::soz("26_mez_etr_tarixce");
		$melumat['26_mez_etr_duzelish_et'] = dil::soz("26_mez_etr_duzelish_et");
		$melumat['26_mez_etr_tesdiqle'] = dil::soz("26_mez_etr_tesdiqle");
		$melumat['26_mez_etr_imtina'] = dil::soz("26_mez_etr_imtina");
		$melumat['26_mez_etr_bagla'] = dil::soz("26_mez_etr_bagla");
		$melumat['26_mez_etr_mezuniyyet__senedin_tarixchesi'] = dil::soz("26_mez_etr_mezuniyyet__senedin_tarixchesi");
		$melumat['26_mez_etr_duzelish_et'] = dil::soz("26_mez_etr_duzelish_et");
		$melumat['26_mez_etr_ugurla_tesdiqlendi'] = dil::soz("26_mez_etr_ugurla_tesdiqlendi");
		$melumat['26_mez_etr_tesdiqlenmeyib'] = dil::soz("26_mez_etr_tesdiqlenmeyib");
		$melumat['26_mez_etr_tesdiqlenib'] = dil::soz("26_mez_etr_tesdiqlenib");
		$melumat['26_mez_etr_sebeb'] = dil::soz("26_mez_etr_sebeb");
		$melumat['26_mez_etr_sebebi_daxil_edin'] = dil::soz("26_mez_etr_sebebi_daxil_edin");
		$melumat['26_mez_etr_imtina_et'] = dil::soz("26_mez_etr_imtina_et");
		$melumat['26_mez_etr_ugurla_imtina_edildi'] = dil::soz("26_mez_etr_ugurla_imtina_edildi");
		$melumat['26_mez_etr_imtina_edilib'] = dil::soz("26_mez_etr_imtina_edilib");
		
		$melumat['26_mez_etr_gün'] = dil::soz("26_mez_etr_gün");
		$melumat['26_mez_etr_testiqle'] = dil::soz("26_mez_etr_testiqle");

		$melumat['order_btn'] = ($mezuniyyetInf['status']==1 && pdof()->query("SELECT * FROM tb_mezuniyyetler_tesdiqleme WHERE emeliyyat_tip='emr_hazirlanmasi' AND mezuniyyet_id='$gid' AND user_id='$userId'")->fetch() && !pdof()->query("SELECT * FROM tb_prodoc_vacation_orders WHERE vacation_id='$gid'")->fetch()) ? '<button class="btn btn-warning" onclick="templateYukle(\'prodoc_vacation_order\',\''.dil::soz("26_mez_etr_Məzuniyyət əmri - Əlavə et").'\',{\'sid\':0,\'vacation_id\':\''.$gid.'\'},65,true);" type="button">Əmr formalaşdır</button>' : '';


		$melumat['mezuniyyet_emri'] = "-";

		if ($hasOrder !== FALSE) {
            require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
            $melumat['mezuniyyet_emri'] = getDocumentNumberByDocumentId($hasOrder['document_id']);
        }

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($mezuniyyetInf['document_id']);

        require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';

        $melumat["detailed_information"] = $intDoc->getDetailedInformationHTML();
        $melumat["tree"] = $intDoc->getRelatedInternalDocumentsHTMLTree();


//        print json_encode(htmlspecialchars($user->template_yukle('mezuniyyet/mezuniyyet_etrafli',$melumat, 'prodoc'),ENT_QUOTES)));
        print json_encode(array("status" => "success", "html" => $user->template_yukle('daxili_senedler/mezuniyyet_etrafli',$melumat, 'prodoc') ));
//	}

}