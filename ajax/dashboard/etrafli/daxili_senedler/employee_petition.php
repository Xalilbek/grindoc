<?php
defined('DIRNAME_INDEX') or die("hara?");

if(isset($_POST['sened_id'])  && is_numeric($_POST['sened_id']) && $_POST['sened_id']>0)
{
    $pid = (int)$_POST['sened_id'];
    $userId = (int)$_SESSION['erpuserid'];
    $shablon_sal = 0;
    $bashliq = "";

    $mInf = pdof()->query("SELECT tb1.*,(SELECT user_ad FROM v_user_adlar WHERE USERID=who_registered) AS who_registered_name,(SELECT user_ad FROM v_user_adlar WHERE USERID=employe) AS employe_name FROM tb_proid_employe_petition tb1 WHERE tb1.id='$pid'")->fetch();
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
    $type = $mInf['type'];
    $mInf['type_name'] = $mInf['type']==1 ? dil::soz("47ishe_qebul") : ($mInf['type']==2 ? dil::soz("47ishe_xitam") : dil::soz("47bashqa_ishe_kechirtme"));
    if((int)$mInf['who_registered']===$userId || (int)$mInf['employe']===$userId || in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_proid_employe_petition_tesdiqleme WHERE petition_id='$pid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch())
    {
        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_proid_employe_petition_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.petition_id='$pid' AND tb1.emeliyyat_tip='tesdiqleme'")->fetchAll();
        $tr1 = '';
        $tr2 = '';

        $tesdiqBtn = false;
        $vekaletnameUserYoxla = 0;

        foreach($tesdiqleyenler AS $trInfo)
        {
            if((int)$trInfo['status']===1)
            {

                $tr1 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"pid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>E-Vəkalətnamə</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
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
                $tr2 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"pid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>E-Vəkalətnamə</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }
        if($tesdiqBtn && (in_array($userId, explode(",",$mInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mInf['tesdiqleme_geden_userler']))))===false)
        {
            $tesdiqBtn = false;
        }
        $imtinaBtn = false;
        if((int)$mInf['status']!==3 && in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mInf['tesdiqleme_geden_userler']))))
        {
            $imtinaBtn = true;
        }
        $attachment = "&nbsp;";
        foreach(explode(",",$mInf['attachment']) AS $sened)
        {
            if(trim($sened)!="")
            {
                $attachment .= '<div style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/proid/petitions/employe_petition/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/proid/petitions/employe_petition/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
            }
        }
        $date = $type==(6||7) ? $user->tarix($mInf['petition_date']) :
            $user->tarix($mInf['petition_date'])." , ".date("H:i:s",strtotime($mInf['petition_date']));
        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($mInf['document_id']);

        $elementler = array(
            "type" =>$type,
            "detailed_information" => $intDoc->getDetailedInformationHTML(),
            "pid"=>$pid,
            "MN"=>time().rand(1,1000),
            "status"=>(int)$mInf['status'],
            "attachment"=>$attachment,
            "who_registered"=>htmlspecialchars($mInf['who_registered_name']),
            "erizenin_nomresi"=>htmlspecialchars($mInf['number']),
            "cari_shobe"=> $type==3 ? htmlspecialchars($fetch_position['cari_shobe']) : '',
            "cari_vezife"=> $type==3 ? htmlspecialchars($fetch_position['cari_vezife']) : '',
            "yeni_shobe"=> $type==3 ? htmlspecialchars($fetch_position['yeni_shobe']) : '',
            "yeni_vezife"=> $type==3 ? htmlspecialchars($fetch_position['yeni_vezife']) : '',
            "employe"=>htmlspecialchars($mInf['employe_name']),
            "petition_type"=>htmlspecialchars($mInf['type_name']),
            "note"=>htmlspecialchars($mInf['note']),
            "date"=>$date,
            'testiqleyibler'=>($tr1===""?" - yoxdur":$tr1),
            'testiqleyecekler'=>($tr2===''?" - yoxdur":$tr2),
            'editBtn7'=>$userId==(int)$mInf['who_registered']?'<button type="button" class="btn blue" onclick="javascript:templateYukle(\'proid_employe_petition7\',\''.dil::soz("47ishchinin_erizesi__duzelish_et").'\',{\'sid\':\''.$pid.'\'},60,true);"><i class="fa fa-edit"></i> '.dil::soz("47deyishdir").'</button>':'',
            'editBtn6'=>$userId==(int)$mInf['who_registered']?'<button type="button" class="btn blue" onclick="javascript:templateYukle(\'proid_employe_petition6\',\''.dil::soz("47ishchinin_erizesi__duzelish_et").'\',{\'sid\':\''.$pid.'\'},60,true);"><i class="fa fa-edit"></i> '.dil::soz("47deyishdir").'</button>':'',
            'editBtn'=>$userId==(int)$mInf['who_registered']?'<button type="button" class="btn blue" onclick="javascript:templateYukle(\'proid_employe_petition\',\''.dil::soz("47ishchinin_erizesi__duzelish_et").'\',{\'sid\':\''.$pid.'\'},60,true);"><i class="fa fa-edit"></i> '.dil::soz("47deyishdir").'</button>':'',
            'testiqBtn'=>(int)$tesdiqBtn&&(int)$mInf['status']==0?'<button type="button" vezife="testiqle" class="btn green"><i class="fa fa-check"></i> '.dil::soz("47tesdiqle").'</button>':'',
            'imtinaBtn'=>(int)$imtinaBtn&&(int)$mInf['status']==0?'<button type="button" vezife="imtina" class="btn btn-danger"><i class="fa fa-minus"></i> '.dil::soz("47imtina").' et</button>':'',

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
    else
    {
        print json_encode(array("status"=>"hazir","html"=>("<div style='color:red;'>Səhv! Olmaz!</div>")));
        exit();
    }
}
