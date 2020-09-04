<?php
defined('DIRNAME_INDEX') or die("hara?");
    $sid = (int)$_GET['id'];
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
        $editBtn=(int)((int)$emrInf['elave_edib']===$userId);
        $tesdiqBtn= ($tesdiqBtn==true)?1:0;



    }
?>
<div class="modal-footer" style="border-top: 0;">
    <div style="float: left; color: red;" vezife="error"></div>
    <div style="float: right;">
        <div class="btn-group" vezife="export"><a type="button" class="btn btn-default" style="margin: 0;" href="pages/emrler.php?id=$sid$" target="_blank"><i class="fa fa-download"></i></a><button aria-expanded="false" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button><ul class="dropdown-menu" role="menu"><li><a href="pages/emrler.php?id=$sid$" target="_blank">HTML</a></li><li><a href="pages/emrler.php?id=$sid$&export=pdf">PDF</a></li></ul></div>
         <a type="button"  vezife="edit" class="btn btn-circle blue editBtn"
           href="?id=<?= $id ?>&module=prodoc_daxili_senedler#vacation_compensation"><?php  print dil::soz("47duzelish_et"); ?>
        </a>
        <button type="button" vezife="tesdiqle" class="btn btn-circle green"><i class="icon-check"></i> <?php  print dil::soz("47tesdiqle"); ?></button>
        <button type="button" vezife="imtina" class="btn btn-circle btn-danger"><i class="fa fa-minus"></i>  <?php  print dil::soz("47imtina"); ?></button>

    </div>
</div>
<script>

    var userId = '<?php print $userId ?>';
    if(parseInt('<?= $imtinaBtn ?>')==0)
    {
        $("button[vezife='imtina']").hide();
    }
    if(parseInt('<?= $tesdiqBtn ?>')==0)
    {

        $("button[vezife='tesdiqle']").hide();

    }
    if(parseInt('<?= $editBtn ?>')==0)
    {
        $("[vezife='edit']").hide();
    }


//
//    if("$edits$"=="")
//    {
//        $("#bosh_modal$MN$ div[vezife='edits']").hide();
//    }
//    if(parseInt('$tesdiqBtn$')===0)
//    {
//        $("#bosh_modal$MN$ button[vezife='testiqle']").hide();
//    }
//    if(parseInt('$imtinaBtn$')===0)
//    {
//        $("#bosh_modal$MN$ button[vezife='imtina']").hide();
//    }
//    if(parseInt('$editBtn$')===0)
//    {
//        $("#bosh_modal$MN$ button[vezife='edit']").hide();
//    }

    $(" button[vezife='testiqle']").click(function()
    {

        $.post("includes/emrler/mezuniyyete_gore_kompensasiya/tesdiqle.php", {'gid':'$sid$'}, function(status)
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
                $.post("includes/emrler/mezuniyyete_gore_kompensasiya/imtina_et.php", {'gid':'$sid$','sebeb':sebeb}, function(netice)
                {
                    //location.reload();
                });
            }
        });
    });


</script>