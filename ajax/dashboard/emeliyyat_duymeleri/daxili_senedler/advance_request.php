<?php
defined('DIRNAME_INDEX') or die("hara?");


    $sid = (int)$_GET['id'];
    $userId = (int)$_SESSION['erpuserid'];
    $shablon_sal = 0;
    $bashliq = "";
    $allowedFileExtensions = array("pdf", "doc", "docx", "xls", "xlsx", "jpg", "jpeg", "png", "gif", "zip", "rar");
    $hesabat = sprintf("<font style='color: red; font-weight: 600;'>%s</font>", dil::soz("47verilmeyib"));

    $mInf = pdof()->query("SELECT *,(SELECT ad FROM tb_struktur_ish_yerleri WHERE id=(SELECT ish_yeri_id FROM tb_strukturlar_msk WHERE id=(SELECT struktur_msk_id FROM tb_Struktur WHERE struktur_id=v_avanslar.struktur_id))) AS ish_yeri_ad,(SELECT ad FROM tb_valyuta WHERE id=v_avanslar.valyuta) AS valyuta_ad FROM v_avanslar WHERE id='$sid'")->fetch();
    if($mInf === FALSE)
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red;'>Səhv! Belə məlumat yoxdur!</div>",ENT_QUOTES)));
        exit();
    }
     $testiqBtn     = "";
     $hesabatVerBtn = "";
     $icraBtn       = "";
     $imtinaBtn     = "";
     $editBtn       = "";
    if( (int)$mInf['user_id']===$userId || (int)$mInf['elave_eden_user']===$userId || in_array($userId,explode(",",$mInf['rehberler'])) || in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_avanslar_tesdiqleme WHERE avans_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch())
    {
        $imtina = "";
        if($mInf['status']==3)
        {
            $imtinaEden = (int)$mInf['imtinaEden'];
            if($imtinaEden===0)
            {
                $imtinaEdenInf = array("Avtomatik");
            }
            else
            {
                $imtinaEdenInf = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . $imtinaEden . "'")->fetch();
            }
            $sebeb = $mInf['imtinaSebebi'];
            $getImtinaDate = pdof()->query("SELECT TOP 1 date FROM tb_avanslar_logs WHERE avans_id='$sid' AND ne='4' ORDER BY date DESC")->fetch();
            $imtina = '<div class="form-body"><div class="form-group"><label class="col-md-3 control-label" style="font-weight:600;padding-top:13px;">'.dil::soz("47imtinaedib").':</label><div class="col-md-8" vezife="imtinaEdib">'.(htmlspecialchars($imtinaEdenInf[0]) . " - <i class='icon-clock'></i> " . date("d-m-Y H:i:s", strtotime($getImtinaDate[0]))).'</div></div></div><div class="form-body"><div class="form-group"><label class="col-md-3 control-label" style="font-weight:600;padding-top:13px;">Səbəb:</label><div class="col-md-8" vezife="imtinaSebebi">'.htmlspecialchars($sebeb).'</div></div></div>';
        }

        $edits = "";
        $getLogsEdit = pdof()->query("SELECT * FROM tb_avanslar_logs WHERE avans_id='$sid' AND ne='1'");
        while($logEdit = $getLogsEdit->fetch())
        {
            $getuserInff = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$logEdit['user_id'] . "'")->fetch();
            $edits .= htmlspecialchars($getuserInff[0],ENT_QUOTES)." - <i class=\"fa fa-time\"></i> ".date("d-m-Y H:i:s", strtotime($logEdit['date']))."<br>Səbəb: ".htmlspecialchars($logEdit['qeyd'],ENT_QUOTES)."<br/>";
        }

        $carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_avanslar_tesdiqleme WHERE avans_id='$sid' AND status='0'")->fetch();
        $carQrup = (int)$carQrup[0];

        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_avanslar_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.avans_id='$sid' AND (tb1.emeliyyat_tip='tesdiqleme' OR tb1.emeliyyat_tip='icraya_goturme')");
        $tr1 = '';
        $tr2 = '';

        $tesdiqBtn = false;
        $icraBtn = false;
        $vekaletnameUserYoxla = 0;

        while($trInfo = $tesdiqleyenler->fetch())
        {
            if((int)$trInfo['status']===1)
            {
                $tr1 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"Elektron vəkalətnamə - Ətraflı\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>E-Vəkalətnamə</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
            }
            else
            {
                if((int)$trInfo['vekaletname_user']===$userId && $carQrup==(int)$trInfo['qrup'])
                {
                    $vekaletnameUserYoxla = (int)$trInfo['user_id'];
                    $trInfo['emeliyyat_tip']=="tesdiqleme"?($tesdiqBtn = true):($icraBtn = true);
                }
                if((int)$trInfo['user_id']===$userId && $carQrup==(int)$trInfo['qrup'])
                {
                    $trInfo['emeliyyat_tip']=="tesdiqleme"?($tesdiqBtn = true):($icraBtn = true);
                }
                $tr2 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"Elektron vəkalətnamə - Ətraflı\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>E-Vəkalətnamə</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }

        if($tesdiqBtn && (in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mInf['tesdiqleme_geden_userler']))))===false)
        {
            $tesdiqBtn = false;
        }
        $imtinaBtn = false;
        if((int)$mInf['status']!==3 && (in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mInf['tesdiqleme_geden_userler']))) || $userId==$mInf['user_id']) && (int)$mInf['qaime_id']==0)
        {
            $imtinaBtn = true;
        }

        $senedler = array();
        foreach(explode(",", $mInf['senedler']) AS $senedEl)
        {
            if(empty($senedEl)) continue;
            $senedler[] = "<a href='uploads/prodoc/common/".addslashes(htmlspecialchars($senedEl))."' target='_blank'><i class='fa fa-file'></i> ".htmlspecialchars($senedEl)."</a>";
        }

        $sorgu = pdof()->query("SELECT * FROM tb_avans_hesabat WHERE avans_id='".(int)$mInf["id"]."' ")->fetch();
        if($sorgu){
            $hesabat = '<a href="javascript:templateYukle(\'avans_hesabat_etrafli\',\'\',{\'sid\':\''.(int)$sorgu["id"].'\'},0,true);">ATH/'.date("Y",strtotime($sorgu['tarix'])).'-'.sprintf("%05d",(int)$sorgu["id"]).'</a> ('.date("d-m-Y",strtotime($sorgu["tarix"])).')';
        }
        $testiqBtn=(int)$tesdiqBtn&&(int)$mInf['status']!=3?'<button type="button" vezife="testiqle" class="btn btn-circle green"><i class="fa fa-check"></i> '.dil::soz("47tesdiqle").'</button>':'';
        $hesabatVerBtn=(int)($mInf['status']==1 && $mInf['mebleq']>$mInf['hesabat_verilen_mebleq']);
        $icraBtn=(int)$icraBtn&&(int)$mInf['status']!=3&&(int)$mInf['qaime_id']==0?'<button type="button" vezife="icra" class="btn btn-circle green"><i class="fa fa-check"></i> '.dil::soz("47icra_et").'</button>':'';
        $imtinaBtn=(int)$imtinaBtn&&(int)$mInf['qaime_id']==0?'<button type="button" vezife="imtina" class="btn btn-circle btn-danger">'.dil::soz("47imtina_et").'</button>':'';
        $editBtn=(int)((int)$mInf['user_id']===$userId && (int)$mInf['qaime_id']==0);
    }

?>


<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <button class="btn btn-circle green" onclick="templateYukle('avans_hesabati','<?php  print dil::soz("47avans_hesabati"); ?>//',{'sid':'0'},55,false);" type="button" id="hesabatVerBtn123" hesabatt><?php  print dil::soz("47hesabat_ver"); ?></button>
        <a type="button"  vezife="edit" class="btn btn-circle blue editBtn"
           href="?id=<?= $id ?>&module=prodoc_daxili_senedler#advance_request"><?php  print dil::soz("47duzelish_et"); ?>
        </a>
       <?php
       print $testiqBtn;
       print $icraBtn;
       print $imtinaBtn;
    ?>


    </div>
</div>
<script>
    if(parseInt("<?= $hesabatVerBtn ?>")==0)
    {
        $("#hesabatVerBtn123").remove();
    }
    $("button[vezife='testiqle']").click(function()
    {

        $.post("includes/prodoc/avans/tesdiqle.php",{'sid':'<?php print $sid ?>'},function()
        {
            location.reload();
        });
    });

    if(parseInt('<?= $editBtn ?>')==0)
    {
        $(" button[editt]").remove();
        $("button[hesabatt]").remove();
    }

    $("button[vezife='imtina']").click(function()
    {
        var mn2=modal_yarat("<?php print dil::soz("47imtina_et") ?>","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-4 control-label'><?php print dil::soz("47sebeb") ?></label><div class='col-md-6'><textarea class='form-control' placeholder='<?php print dil::soz("47sebebi_daxil_edin") ?>' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn btn-danger tesdiqle'><?php print dil::soz("47imtina_et") ?></button> <button class='btn default' data-dismiss='modal'><?php print dil::soz("47bagla") ?></button>","btn-danger","",true);
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
                $.post("includes/prodoc/avans/imtina_et.php",{'sid':'<?php print $sid ?>','sebeb':sebeb},function(netice)
                {
                    location.reload();
                });
            }
        });
    });

    $(" button[vezife='icra']").click(function()
    {
        window.open('pages/procent/edit/emekdashaPulunVerilmesi.php?avans_id=<?php print $sid ?>', 'yeni_'+Math.random(), 'width=1120,height=500,menubar=no,location=no,resizable=yes,scrollbars=yes,status=no');
    });
</script>