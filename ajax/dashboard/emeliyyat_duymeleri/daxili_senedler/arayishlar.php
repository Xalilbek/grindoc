<?php

    $userId = (int)$_SESSION['erpuserid'];
    $sid = $_GET['id'];
    $VocInfo = pdof()->query("SELECT *,(SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.created_user) as user_name,(SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.employe) as employee, (SELECT name FROM tb_prodoc_certificate_organizations WHERE tb_prodoc_certificate_organizations.id = tb1.organization_id) as qurum_ad FROM tb_prodoc_certificate tb1 WHERE tb1.id='$sid' ")->fetch();

    $approveBtn = ((int)$VocInfo['status']!=3 && (int)$VocInfo['status']!=1 && pdof()->query("SELECT * FROM tb_prodoc_certificate_tesdiqleme WHERE user_id='$userId' AND status='0' AND qrup=(SELECT MIN(qrup) FROM tb_prodoc_certificate_tesdiqleme WHERE status='0')")->fetch()) ? true : false;
    $cancelBtn = ((int)$VocInfo['status']!==3 && (in_array($userId,explode(",",$VocInfo['tesdiqleme_geden_userler'])) || $userId==$VocInfo['employe']))? true : false;
    $editBtn=($userId==(int)$VocInfo["created_user"]);

    $approveBtn = (int)$approveBtn;
    $cancelBtn  = (int)$cancelBtn;
	$editBtn = (int)$editBtn;

?>
<div id="buttonlar" style="float: right;" >
    <a type="button" vezife="edit" class="btn btn-circle btn-info"
       href="?id=<?= $id ?>&module=prodoc_daxili_senedler#arayish">Düzəliş et
    </a>
    <button type="button" vezife="tesdiqle" class="btn btn-circle green approveBtn"><?= dil::soz("47tesdiqle") ?></button>
    <button type="button" vezife="imtina" class="btn btn-circle btn-danger cancelBtn" data-toggle="modal">İmtina</button>
</div>


<script>
    if(parseInt('<?= $cancelBtn ?>')==0)
    {
        $('[vezife="imtina"]').hide();
    }
    if(parseInt('<?= $approveBtn ?>')==0)
    {
        $('[vezife="tesdiqle"]').hide();
    }
    if(parseInt('<?= $editBtn ?>'))
    {
        $('[vezife="edit"]').show();
    }

    $('[vezife="tesdiqle"]').click(function()
    {

        $.post("ajax/prodoc/certificate/certificate_approve.php", {'gid':parseInt('<?= $sid ?>')}, function(status)
        {
            // location.reload();
        });
    });

    $('[vezife="imtina"]').click(function()
    {
        var mn2=modal_yarat("<?= dil::soz("47imtina_et") ?>","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-4 control-label'><?= dil::soz("47sebeb") ?></label><div class='col-md-6'><textarea class='form-control' placeholder='<?= dil::soz("47sebebi_daxil_edin") ?>' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn btn-danger cancel_yes'><?= dil::soz("47imtina_et") ?></button> <button class='btn default' data-dismiss='modal'><?= dil::soz("47bagla") ?></button>","btn-danger","",true);
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
                $.post("ajax/prodoc/certificate/certificate_cancel.php", {'gid':'$sid$','sebeb':sebeb}, function(netice)
                {
                    // location.reload();
                });
            }
        });
    });
</script>
