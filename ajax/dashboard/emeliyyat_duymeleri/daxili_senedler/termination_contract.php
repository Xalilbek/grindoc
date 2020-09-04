<?php

defined('DIRNAME_INDEX') or die("hara?");

    $sid = (int)$_GET['id'];
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
            if(!empty($senedEl)){

                $senedler[] = "<a href='uploads/emr_documents/emek_muqavilesine_xitam/".addslashes(htmlspecialchars($senedEl))."' target='_blank'><i class='fa fa-file'></i> ".htmlspecialchars($senedEl)."</a>";
            }
        }
        $qoshma = array();
        foreach(explode(",", $emrInf['qoshma']) AS $senedEl)
        {
            if(!empty($senedEl)) {
                $qoshma[] = "<a href='uploads/emr_documents/emek_muqavilesine_xitam/".addslashes(htmlspecialchars($senedEl))."' target='_blank'><i class='fa fa-file'></i> ".htmlspecialchars($senedEl)."</a>";

            }
        }
        $senedSeria = pdof()->query("SELECT seria FROM tb_sened_novleri WHERE forma_ad='emr_emek_muqavilesine_xitam'")->fetch();
        $petitionId = (int)$emrInf['employe_petition'];
        $petitionInf = pdof()->query("SELECT * FROM tb_proid_employe_petition WHERE id='$petitionId'")->fetch();
        $imtinaBtn=(int)$imtinaBtn;
        $editBtn=(int)((int)$emrInf['elave_edib']===$userId&&$emrInf['xitam_verilib']==0);

    }
    ?>

<div class="modal-footer" style="border-top: 0;">
    <div style="float: left; color: red;" vezife="error"></div>
    <div style="float: right;">

        <a type="button"  vezife="edit" class="btn btn-circle blue editBtn"
           href="?id=<?= $id ?>&module=prodoc_daxili_senedler#termination_contract"><?php  print dil::soz("47duzelish_et"); ?>
        </a>
        <button type="button" vezife="tesdiqle" class="btn btn-outline btn-circle green-meadow"><?php  print dil::soz("47tesdiqle"); ?></button>
        <button type="button" vezife="imtina" class="btn btn-outline btn-circle"><?php  print dil::soz("47imtina"); ?></button>


    </div>
</div>
<script>
    var userId = '<?php print $userId ?>';
    var sid = ' <?php print $sid ?>';







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

    $("button[vezife='tesdiqle']").click(function()
    {

        $.post("includes/emrler/ishe_qebul/tesdiqle.php", {'gid':sid}, function(status)
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
                $.post("includes/emrler/ishe_qebul/imtina_et.php", {'gid':sid,'sebeb':sebeb}, function(netice)
                {
                    location.reload();
                });
            }
        });
    });

</script>
