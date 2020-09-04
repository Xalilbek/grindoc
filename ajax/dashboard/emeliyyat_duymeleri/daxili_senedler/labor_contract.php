<?php
defined('DIRNAME_INDEX') or die("hara?");


$MN = 0;
$pr = (int)$user->checkPrivilegia("gorushler");
$sid = $id;
$userId = (int)$_SESSION['erpuserid'];
$language = $user->getLang();
$dill = new dilStclass($language);
defined('DIRNAME_INDEX') or die("hara?");


    $cid = (int)$_GET['id'];
    $userId = (int)$_SESSION['erpuserid'];
    $shablon_sal = 0;
    $bashliq = "";

    $mInf = pdof()->query("SELECT tb1.*,(SELECT user_ad FROM v_user_adlar WHERE USERID=who_registered) AS who_registered_name,(SELECT user_ad FROM v_user_adlar WHERE USERID=employe) AS employe_name,tb2.name AS type_name,tb2.standart_type FROM tb_proid_labor_contracts tb1 OUTER APPLY (SELECT name,standart_type FROM tb_proid_contract_types WHERE id=type) tb2 WHERE tb1.id='$cid'")->fetch();
    if(!$mInf)
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red;'>Səhv! Belə məlumat yoxdur!</div>",ENT_QUOTES)));
        exit();
    }

    if((int)$mInf['who_registered']===$userId || (int)$mInf['employe']===$userId || in_array($userId,explode(",",$mInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_proid_labor_contracts_tesdiqleme WHERE contract_id='$cid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch())
    {
        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_proid_labor_contracts_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.contract_id='$cid' AND tb1.emeliyyat_tip='tesdiqleme'")->fetchAll();
        $tr1 = '';
        $tr2 = '';

        $tesdiqBtn = false;
        $vekaletnameUserYoxla = 0;

        foreach($tesdiqleyenler AS $trInfo)
        {
            if((int)$trInfo['status']===1)
            {
                $tr1 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"cid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("47elektron_vekaletname__etrafli")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
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
                $tr2 .= "<div>".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("47elektron_vekaletname__etrafli")."\",{\"cid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("47elektron_vekaletname__etrafli")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
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
                $attachment .= '<div style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/proid/contracts/labor_contract/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/proid/contracts/labor_contract/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
            }
        }
        $petitionName = "-";
        if($mInf['petition_id']>0 && $mInf['standart_type']=="labor_contract")
        {
            $petitionInf = pdof()->query("SELECT petition_date FROM tb_proid_employe_petition WHERE id='".(int)$mInf['petition_id']."'")->fetch();
            $petitionName = "<a href=\"javascript:templateYukle('proid_employe_petition_info','".dil::soz("47ishchinin_erizesi__etrafli")."',{'pid':'".(int)$mInf['petition_id']."'},45,true);\">İƏ/".sprintf("%05d",(int)$mInf['petition_id'])."-".date("Y",strtotime($petitionInf['petition_date'])).'</a>';
        }


        $editBtn=$userId==(int)$mInf['who_registered']?'<a type="button" vezife="edit" class="btn btn-circle btn-info" href="?id='.$id.'&module=prodoc_daxili_senedler#labor_contract">Düzəliş et</a>':'';
            $testiqBtn=(int)$tesdiqBtn&&(int)$mInf['status']==0?'<button type="button" vezife="testiqle" class="btn btn-circle green"><i class="fa fa-check"></i> '.dil::soz("47tesdiqle").'</button>':'';
            $imtinaBtn=(int)$imtinaBtn&&(int)$mInf['status']==0?'<button type="button" vezife="imtina" class="btn btn-circle btn-danger"><i class="fa fa-minus"></i> '.dil::soz("47imtina").'</button>':'';



    }



?>
<div style="display: inline;">
    <?php print $testiqBtn; ?>

    <?php print $imtinaBtn; ?>

    <?php print $editBtn; ?>



</div>


<script>
    $(" button[vezife='testiqle']").click(function()
    {

        $.post("ajax/proid/contracts/labor_contract_approve.php",{'cid':'<?php print $cid; ?>'},function()
        {
            location.reload();
        });
    });
    $("button[vezife='imtina']").click(function()
    {
        var mn2=modal_yarat("<?php dil::soz("47imtina_et") ?>","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-3 control-label'><?php dil::soz("47sebeb") ?></label><div class='col-md-6'><textarea class='form-control' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn btn-danger tesdiqle'><?php dil::soz("47imtina_et") ?></button> <button class='btn default' data-dismiss='modal'><?php dil::soz("47bagla") ?></button>","btn-danger","",true);
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
                $.post("ajax/proid/contracts/labor_contract_cancel.php",{'cid':'<?php print $cid; ?>','sebeb':sebeb},function(netice)
                {
                    location.reload();
                });
            }
        });
    });
</script>