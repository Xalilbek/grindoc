<?php

    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$_GET['id'];

    $BusinessTripInfoInfo = pdof()->query("SELECT *,(SELECT user_ad FROM v_user_adlar WHERE USERID=created_user) as user_name FROM tb_prodoc_business_trip tb1 WHERE tb1.id='$sid' ")->fetch();
    if($BusinessTripInfoInfo==false)
    {
        print json_encode(array("status"=>"sehv"));
        exit();
    }

    $attachment = "";
    foreach(explode(",",$BusinessTripInfoInfo['attachment']) AS $sened)
    {
        if(trim($sened)!="")
        {
            $attachment .= '<div style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/prodoc/business_trip/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/prodoc/business_trip/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
        }
    }

    $approveBtn = ((int)$BusinessTripInfoInfo['status']!=3 && (int)$BusinessTripInfoInfo['status']!=1 && (pdof()->query("SELECT * FROM tb_prodoc_business_trip_tesdiqleme WHERE user_id='$userId' AND status='0' AND trip_id='".(int)$sid."' AND qrup=(SELECT MIN(qrup) FROM tb_prodoc_business_trip_tesdiqleme WHERE status='0' AND trip_id='".(int)$sid."' )")->fetch())!=false) ? true : false;
    $cancelBtn = ((int)$BusinessTripInfoInfo['status']!=3 && (in_array($userId,explode(",",$BusinessTripInfoInfo['tesdiqleme_geden_userler'])) || (pdof()->query("SELECT * FROM tb_proid_business_trip_users WHERE user_id='$userId' AND eid='".(int)$BusinessTripInfoInfo["business_trip_id"]."' AND order_id='$sid'")->fetch()) ))? true : false;

    $trioinfo = pdof()->query("SELECT TOP 20 CONCAT(N'Ezamiyyət №',tb1.id) as name,tb1.*,tb3.ad AS olke_ad,tb4.struktur_bolmesi as bolme_ad,tb5.Adi AS shirket_ad,tb2.ad AS sheher_ad FROM tb_ezamiyyetler tb1 LEFT JOIN tb_Struktur tb4 ON tb4.struktur_id=tb1.bolme LEFT JOIN tb_istehsalchilar tb5 ON tb5.id=tb1.shirket LEFT JOIN tb_general_cities tb2 ON tb2.id=tb1.sheher LEFT JOIN tb_general_countries tb3 ON tb3.id=tb1.olke WHERE tb1.status=1 AND tb1.id='".(int)$BusinessTripInfoInfo["business_trip_id"]."' ")->fetch();

    $ezaInfo = pdof()->query("SELECT * FROM tb_ezamiyyetler tb1 WHERE tb1.id='".(int)$BusinessTripInfoInfo["business_trip_id"]."' ")->fetch();
    $approveBtn= (int)$approveBtn;
    $cancelBtn = (int)$cancelBtn;
    $editBtn   = ($userId==(int)$BusinessTripInfoInfo["created_user"])?true:false;

?>

<div class="modal-footer" style="border-top: 0;">
    <div style="float: left; color: red;" vezife="error"></div>
    <div style="float: right;">
        <a type="button"  vezife="edit" class="btn blue editBtn"
           href="?id=<?= $id ?>&module=prodoc_daxili_senedler#business_trip"><?php  print dil::soz("47duzelish_et"); ?>
        </a>
        <button type="button" vezife="tesdiq" class="btn green approveBtn"><?php print dil::soz("47tesdiqle") ?></button>
        <button type="button" vezife="imtinaEt" class="btn btn-danger cancelBtn" data-toggle="modal"><?php print dil::soz("47imtina") ?></button>

    </div>
</div>
<script>
    if(parseInt('<?= $cancelBtn ?>')==0)
    {
        $("button[vezife='imtinaEt']").hide();
    }
    if(parseInt('<?= $approveBtn ?>')==0)
    {
        $("button[vezife='tesdiq']").hide();
    }
    if(parseInt('<?= $editBtn ?>')==0)
    {
        $("[vezife='edit']").hide();
    }



    $("button[vezife='tesdiq']").click(function()
    {
        modal_loading(1, '$MN$');
        $.post("ajax/prodoc/business_trip/business_trip_approve.php", {'gid':<?php print $sid ?>}, function(status)
        {
            location.reload();
        });
    });
    $("button[vezife='imtinaEt']").click(function()
    {
        var mn2=modal_yarat("<?php print dil::soz("47imtina_et") ?>","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-4 control-label'><?php print dil::soz("47sebeb") ?></label><div class='col-md-6'><textarea class='form-control' placeholder='<?php print dil::soz("47sebebi_daxil_edin") ?>' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn btn-danger cancel_yes'><?php print dil::soz("47imtina_et") ?></button> <button class='btn default' data-dismiss='modal'><?php print dil::soz("47bagla") ?></button>","btn-danger","",true);
        $("#bosh_modal"+mn2+" textarea").textareaLimit();
        $("#bosh_modal"+mn2+" .cancel_yes").unbind("click").click(function()
        {
            var sebeb = $("#bosh_modal"+mn2+" textarea").val().trim();
            if(sebeb=="")
            {
                $("#bosh_modal"+mn2+" textarea").css("border","1px dashed red");
            }
            else
            {
                modal_loading(1,mn2);
                $("#bosh_modal"+mn2+" .cancel_yes").css("border","");
                $.post("ajax/prodoc/business_trip/business_trip_cancel.php", {'gid':'<?php print $sid ?>','sebeb':sebeb}, function(netice)
                {
                    location.reload();
                });
            }
        });
    });
</script>
