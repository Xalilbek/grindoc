<?php
defined('DIRNAME_INDEX') or die("hara?");


    $pid = (int)$_GET['id'];
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
    $mInf['type_name'] = $mInf['type']==1 ? dil::soz("47ishe_qebul") : ($mInf['type']==2 ? dil::soz("47ishe_xitam") : ($mInf['type']==3 ? dil::soz("47bashqa_ishe_kechirtme") : ($mInf['type']==6 ? dil::soz("47tibbi_sigorta") : dil::soz("73maddi_yardim") ) ));
    $testiqBtn="";
    $imtinaBtn="";
    $editBtn="";
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
        $editBtn7=$userId==(int)$mInf['who_registered']?'<button type="button" class="btn btn-circle blue" onclick="javascript:templateYukle(\'proid_employe_petition7\',\''.dil::soz("47ishchinin_erizesi__duzelish_et").'\',{\'sid\':\''.$pid.'\'},60,true);"><i class="fa fa-edit"></i> '.dil::soz("47deyishdir").'</button>':'';
        $editBtn6=$userId==(int)$mInf['who_registered']?'<button type="button" class="btn btn-circle blue" onclick="javascript:templateYukle(\'proid_employe_petition6\',\''.dil::soz("47ishchinin_erizesi__duzelish_et").'\',{\'sid\':\''.$pid.'\'},60,true);"><i class="fa fa-edit"></i> '.dil::soz("47deyishdir").'</button>':'';
        $editBtn=$userId==(int)$mInf['who_registered']?'<a type="button"  vezife="edit" class="btn btn-circle blue editBtn" href="?id='.$pid.'&module=prodoc_daxili_senedler#employee_petition">'. dil::soz("47duzelish_et").'</a>':'';
        $testiqBtn=(int)$tesdiqBtn&&(int)$mInf['status']==0?'<button type="button" vezife="testiqle" class="btn btn-circle green"><i class="fa fa-check"></i> '.dil::soz("47tesdiqle").'</button>':'';
        $imtinaBtn=(int)$imtinaBtn&&(int)$mInf['status']==0?'<button type="button" vezife="imtina" class="btn btn-circle btn-danger"><i class="fa fa-minus"></i> '.dil::soz("47imtina").' et</button>':'';

        }
    ?>

<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <?php
        print $testiqBtn ;
        print $imtinaBtn;
        ?>


        <span id="edit"><?php print $editBtn ;?></span>
        <span id="edit6" style="display: none"><?php print $editBtn6 ;?></span>
        <span id="edit7" style="display: none"><?php print $editBtn7 ;?></span>
        </div>
</div>
<script>
    console.log("<?php print $type?>");
//    if ("$type$"==6||"$type$"==7)
//    {
//        $("#edit").hide();
//        $("#fayl").hide();
//        if ("$type$"==6) {$("#edit6").attr("style","display:inline")}
//        if ("$type$"==7) {$("#edit7").attr("style","display:inline")}
//    }

    if ("<?php print $type?>"==6||"<?php print $type?>"==7)
    {
        $("#edit").hide();
        $("#fayl").hide();
        if ("<?php print $type?>"==6) {$("#edit6").attr("style","display:inline")}
        if ("<?php print $type?>"==7) {$("#edit7").attr("style","display:inline")}
    }
    if("<?php print $type?>"!=3) $(".for_type3").hide();
    $(" button[vezife='testiqle']").click(function()
    {
        modal_loading(1);
        $.post("ajax/proid/petition/employe_petition_approve.php",{'pid':'<?php print $pid?>','type':'<?php print $type?>'},function()
        {
            location.reload();
        });
    });
    $(" button[vezife='imtina']").click(function()
    {
        var mn2=modal_yarat("<?php print dil::soz("47imtina_et") ?>","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-4 control-label'><?php print dil::soz("47sebeb") ?></label><div class='col-md-6'><textarea class='form-control' placeholder='<?php print dil::soz("47sebebi_daxil_edin") ?>' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn btn-danger cancel_yes'><?php print dil::soz("47imtina_et") ?></button> <button class='btn default' data-dismiss='modal'><?php print dil::soz("47bagla") ?></button>","btn-danger","",true);
        $("#bosh_modal"+mn2+" textarea").textareaLimit();
        $("#bosh_modal"+mn2+" button.tesdiqle").unbind("click").click(function()
        {
            var sebeb = $("#bosh_modal"+mn2+" textarea").val().trim();
            if(sebeb=="")
            {
                $("#bosh_modal"+mn2+" textarea").css("border","1px dashed red");
            }
            else
            {
                modal_loading(1,mn2);
                $("#bosh_modal"+mn2+" button.tesdiqle").css("border","");
                $.post("ajax/proid/petition/employe_petition_cancel.php",{'pid':'<?php print $pid; ?>','sebeb':sebeb,'type':'<?php print $type; ?>'},function(netice)
                {
                    location.reload();
                });
            }
        });
    });
</script>

