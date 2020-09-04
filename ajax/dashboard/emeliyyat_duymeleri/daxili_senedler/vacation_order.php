<?php


    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$_GET['id'];
    $VocInfo = pdof()->query("SELECT *,(SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.created_user) as user_name,(SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.employe) as employee FROM tb_prodoc_vacation_orders tb1 WHERE tb1.id='$sid' ")->fetch();

    $wystr = "";
    $wyearsarr = @json_decode($VocInfo["work_years"]);
    if(is_array($wyearsarr))
    {
        foreach ($wyearsarr as $value) {
            if(isset($value[0]) && is_numeric($value[0]) && isset($value[1]) && is_numeric($value[1]))
                $workYearsq = pdof()->query("SELECT CONCAT(DATEPART(YEAR, tarix1), ' - ', DATEPART(YEAR, tarix2)) AS years FROM tb_mezuniyyet_gunleri_illik WHERE id='".(int)$value[0]."' ")->fetch();
            $wystr .= sprintf($workYearsq[0]." ( ".$value[1]." %s )<br>", dil::soz("9907gun"));
        }
    }
    $attachment = "";
    foreach(explode(",",$VocInfo['attachment']) AS $sened)
    {
        if(trim($sened)!="")
        {
            $attachment .= '<div style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/prodoc/vacation_order/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/prodoc/vacation_order/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
        }
    }

    $approveBtn = ((int)$VocInfo['status']!=3 && (int)$VocInfo['status']!=1 && pdof()->query("SELECT * FROM tb_prodoc_vacation_orders_tesdiqleme WHERE user_id='$userId' AND status='0' AND qrup=(SELECT MIN(qrup) FROM tb_prodoc_vacation_orders_tesdiqleme WHERE status='0')")->fetch()) ? 1 : 0;
    $cancelBtn = ((int)$VocInfo['status']!==3 && (in_array($userId,explode(",",$VocInfo['tesdiqleme_geden_userler'])) || $userId==$VocInfo['employe']))? 1 : 0;
    $editBtn=($userId==(int)$VocInfo["created_user"])?1:0;


?>

<div id="buttonlar" class="modal-footer" style="border-top: 0;">
    <div style="float: left; color: red;" vezife="error"></div>
    <div style="float: right;">
        <a type="button"  vezife="edit" class="btn btn-circle blue editBtn"
           href="?id=<?= $id ?>&module=prodoc_daxili_senedler#vacation_order"><?php  print dil::soz("47duzelish_et"); ?>
        </a>

        <button type="button" vezife="tesdiq" class="btn btn-circle green approveBtn"><?php print dil::soz("47tesdiqle") ?></button>
        <button type="button" vezife="imtinaEt" class="btn btn-circle btn-danger cancelBtn" data-toggle="modal"><?php print dil::soz("47imtina") ?></button>

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

        $.post("ajax/prodoc/vacation_order/vacation_order_approve.php", {'gid':<?= $sid ?>}, function(status)
        {
            location.reload();
        });
    });
    $("button[vezife='imtinaEt']").click(function()
    {
        var mn2=modal_yarat("İmtina et","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-4 control-label'>Səbəb</label><div class='col-md-6'><textarea class='form-control' placeholder='Səbəbi daxil edin' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn btn-danger cancel_yes'>İmtina et</button> <button class='btn default' data-dismiss='modal'>Bağla</button>","btn-danger","",true);
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
                $.post("ajax/prodoc/vacation_order/vacation_order_cancel.php", {'gid':'$sid$','sebeb':sebeb}, function(netice)
                {
                    location.reload();
                });
            }
        });
    });
</script>
