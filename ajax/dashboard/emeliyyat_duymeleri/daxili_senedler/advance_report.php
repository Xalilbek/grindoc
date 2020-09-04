<?php
defined('DIRNAME_INDEX') or die("hara?");


    $sid = (int)$_GET['id'];
    $userId = (int)$_SESSION['erpuserid'];
    $shablon_sal = 0;
    $bashliq = "";

    $mInf = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.tarix AS avans_tarix,tb3.mebleq AS avans_mebleq,(SELECT ad FROM tb_struktur_ish_yerleri WHERE id=(SELECT ish_yeri_id FROM tb_strukturlar_msk WHERE id=(SELECT struktur_msk_id FROM tb_Struktur WHERE struktur_id=tb2.struktur_id))) AS ish_yeri_ad,(SELECT ad FROM tb_valyuta WHERE id=(SELECT tb_avanslar.valyuta FROM tb_avanslar WHERE tb_avanslar.id=tb1.avans_id)) AS valyuta_ad FROM tb_avans_hesabat tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.kim LEFT JOIN tb_avanslar tb3 ON tb3.id=tb1.avans_id WHERE tb1.id='$sid'")->fetch();
    if(!$mInf)
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red;'>Səhv! Belə məlumat yoxdur!</div>",ENT_QUOTES)));
        exit();
    }
    $editBtn="";
    $testiqBtn="";
    $icraBtn="";
    $imtinaBtn="";
    $avans2="";
    if((int)$mInf['kim']===$userId || (int)$mInf['elave_etdi']===$userId || in_array($userId,explode(",",$mInf['rehberler'])) || in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_avans_hesabat_tesdiqleme WHERE avans_h_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch())
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
            $getImtinaDate = pdof()->query("SELECT TOP 1 date FROM tb_avans_hesabat_log WHERE avans_h_id='$sid' AND ne='4' ORDER BY date DESC")->fetch();
            $imtina = sprintf('<div class="form-body"><div class="form-group"><label class="col-md-4 control-label" style="font-weight:600;padding-top:13px;">%s:</label><div class="col-md-8" vezife="imtinaEdib">'.(htmlspecialchars($imtinaEdenInf[0]) . " - <i class='icon-clock'></i> " . date("d-m-Y H:i:s", strtotime($getImtinaDate[0]))).'</div></div></div><div class="form-body"><div class="form-group"><label class="col-md-4 control-label" style="font-weight:600;padding-top:13px;">%s:</label><div class="col-md-8" vezife="imtinaSebebi">'.htmlspecialchars($sebeb).'</div></div></div>', dil::soz("47imtinaedib"), dil::soz("48sebeb"));
        }

        $edits = "";
        $getLogsEdit = pdof()->query("SELECT * FROM tb_avans_hesabat_log WHERE avans_h_id='$sid' AND ne='1'");
        while($logEdit = $getLogsEdit->fetch())
        {
            $getuserInff = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$logEdit['user_id'] . "'")->fetch();
            $edits .= htmlspecialchars($getuserInff[0],ENT_QUOTES)." - <i class=\"fa fa-time\"></i> ".date("d-m-Y H:i:s", strtotime($logEdit['date']))."<br>".dil::soz("48sebeb").": ".htmlspecialchars($logEdit['qeyd'],ENT_QUOTES)."<br/>";
        }

        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_avans_hesabat_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.avans_h_id='$sid' AND (tb1.emeliyyat_tip='tesdiqleme' OR tb1.emeliyyat_tip='icraya_goturme')");
        $tr1 = '';
        $tr2 = '';

        $tesdiqBtn = false;
        $icraBtn = false;
        $vekaletnameUserYoxla = 0;

        while($trInfo = $tesdiqleyenler->fetch())
        {
            if((int)$trInfo['status']===1)
            {
                $tr1 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("47elektron_vekaletname__etrafli")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
            }
            else
            {
                if((int)$trInfo['vekaletname_user']===$userId)
                {
                    $vekaletnameUserYoxla = (int)$trInfo['user_id'];
                    $trInfo['emeliyyat_tip']=="tesdiqleme"?($tesdiqBtn = true):($icraBtn = true);
                }
                if((int)$trInfo['user_id']===$userId)
                {
                    $trInfo['emeliyyat_tip']=="tesdiqleme"?($tesdiqBtn = true):($icraBtn = true);
                }
                $tr2 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("47elektron_vekaletname__etrafli")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }

        if($tesdiqBtn && (in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mInf['tesdiqleme_geden_userler']))))===false)
        {
            $tesdiqBtn = false;
        }
        $imtinaBtn = false;
        if((int)$mInf['status']!==3 && (in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mInf['tesdiqleme_geden_userler']))) || $userId==$mInf['kim']))
        {
            $imtinaBtn = true;
        }

        $xercler = '';
        $say = 0;
        $cemiMebleq = 0;
        $getXercler = pdof()->query("SELECT * FROM tb_avans_hesabat_xercler WHERE avans_h_id='$sid'");
        while($avInf = $getXercler->fetch())
        {
            $say++;
            $xercler .= '<tr><td>'.$say.'</td><td>'.date("d-m-Y",strtotime($avInf['tarix'])).'</td><td>'.htmlspecialchars($avInf['xerc']).'</td><td>'.number_format((float)$avInf['mebleq'],2).' '.htmlspecialchars($mInf['valyuta_ad']).'</td></tr>';
            $cemiMebleq += (float)$avInf['mebleq'];
        }
        if($xercler=="")
        {
            $xercler = '<tr><td colspan="100%"><i>'.dil::soz("47xercyoxdu").'</i></td></tr>';
        }
        $xerclerYekun = '<tr><th colspan=3 style="text-align:right;">'.dil::soz("47yekun").':</th><th style="text-align:left;">'.number_format($cemiMebleq,2).' '.htmlspecialchars($mInf['valyuta_ad']).'</th></tr>';
        $senedler = "&nbsp;";
        foreach(explode(",",$mInf['senedler']) AS $sened)
        {
            if(trim($sened)!="")
            {
                $senedler .= '<div style="margin-top: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/daxili_senedler/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars(substr(basename($sened),strpos(basename($sened),"_")+1)).'</a> <a class="btn btn-xs btn-default" href="uploads/daxili_senedler/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
            }
        }

        $editBtn=    $userId==(int)$mInf['elave_etdi']&&(int)$mInf['qaime_id']==0?'<a type="button"  vezife="edit" class="btn blue btn-circle editBtn" href="?id='.$id.'&module=prodoc_daxili_senedler#advance_report">'.dil::soz("47duzelish_et").'</a>':"";
        $testiqBtn=  (int)$tesdiqBtn&&(int)$mInf['status']!=3&&(int)$mInf['qaime_id']==0?'<button type="button" vezife="testiqle" class="btn btn-circle green">'.dil::soz("47tesdiqle").'</button>':'';
        $icraBtn=    (int)$icraBtn&&(int)$mInf['status']!=3&&(int)$mInf['qaime_id']==0?'<button type="button" vezife="icra" class="btn btn-circle green">'.dil::soz("47icra_et").'</button>':'';
        $imtinaBtn=  (int)$imtinaBtn&&(int)$mInf['qaime_id']==0?'<button type="button" vezife="imtina" class="btn btn-circle btn-danger">'.dil::soz("47imtina_et").'</button>':'';
        $avans2=     (int)$mInf['avans_id'];
    }


    ?>

<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <?php

        print $editBtn;
        print $testiqBtn;
        print $icraBtn;
        print $imtinaBtn;


?>

    </div>
</div>

<script>
    if((parseInt('<?= $avans2 ?>')>0)===false)
    {
        $(" div.avans").hide();
    }
    $(" button[vezife='testiqle']").click(function()
    {
        modal_loading(1);
        $.post("includes/prodoc/avans/avans_hesabati/tesdiqle.php",{'sid':'<?= $sid ?>'},function()
        {
            location.reload();
        });
    });
    $(" button[vezife='imtina']").click(function()
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
                $.post("includes/prodoc/avans/avans_hesabati/imtina_et.php",{'sid':'<?= $sid ?>','sebeb':sebeb},function(netice)
                {
                    location.reload();
                });
            }
        });
    });

    $("button[vezife='icra']").click(function()
    {
        window.open('pages/procent/edit/xerclenenPullarinHesabati.php?avh_id=<?= $sid ?>', 'yeni_'+Math.random(), 'width=1120,height=500,menubar=no,location=no,resizable=yes,scrollbars=yes,status=no');
    });
</script>

