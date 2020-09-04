<?php

$MN = 0;
$pr = (int)$user->checkPrivilegia("icazeler");
$sid = $id;
$userId = (int)$_SESSION['erpuserid'];
$language = $user->getLang();
$dill = new dilStclass($language);

$getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
$userGroup = (int)$getUserInfo['Groupp'];



$icazeInf = pdof()->query("SELECT * FROM v_icazeler WHERE id='$sid'")->fetch();

if (((int)$icazeInf['user_id'] === $userId || (int)$icazeInf['elave_eden_user'] === $userId || in_array($userId, explode(",", $icazeInf['rehberler'])) || in_array($userId, explode(",", $icazeInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_icazeler_tesdiqleme WHERE icaze_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()) || $pr > 0)
{
    $changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='icazeler' AND kid='$sid' AND user_id='$userId'");

    $carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_icazeler_tesdiqleme WHERE icaze_id='$sid' AND status='0'")->fetch();
    $carQrup = (int)$carQrup[0];

    $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_icazeler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.icaze_id='$sid' AND tb1.emeliyyat_tip='tesdiqleme'");

    $tesdiqBtn = false;
    $vekaletnameUserYoxla = 0;

    while ($trInfo = $tesdiqleyenler->fetch()) {
        if ((int)$trInfo['status'] !== 1) {
            if ((int)$trInfo['vekaletname_user'] === $userId && $carQrup == (int)$trInfo['qrup']) {
                $vekaletnameUserYoxla = (int)$trInfo['user_id'];
                $tesdiqBtn = true;
            }
            if ((int)$trInfo['user_id'] === $userId && $carQrup == (int)$trInfo['qrup']) {
                $tesdiqBtn = true;
            }
        }
    }

    if (($tesdiqBtn && (in_array($userId, explode(",", $icazeInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla > 0 && in_array($vekaletnameUserYoxla, explode(",", $icazeInf['tesdiqleme_geden_userler'])))) === false) || (int)$icazeInf['status'] == 3) {
        $tesdiqBtn = false;
    }
    $imtinaBtn = false;
    if ((int)$icazeInf['status'] !== 3 && (in_array($userId, explode(",", $icazeInf['tesdiqleme_geden_userler'])) || $userId == $icazeInf['user_id'] || ($vekaletnameUserYoxla > 0 && in_array($vekaletnameUserYoxla, explode(",", $icazeInf['tesdiqleme_geden_userler']))))) {
        $imtinaBtn = true;
    }

    $tesdiqBtn = (int)$tesdiqBtn;
    $imtinaBtn = (int)$imtinaBtn;
    $editBtn = (int)($userId == $icazeInf['user_id'] || $userId == $icazeInf['elave_eden_user']);
}
?>

<div id="buttonlar">
    <div class="btn-group" role="group">

        <div style="display: inline-block;" vezife="export">
            <a type="button" class="btn btn-default" href="pages/icaze.php?id=0" target="_blank"><i
                    class="fa fa-download"></i></a>

            <div class="btn-group" role="group">
                <button aria-expanded="false" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="pages/icaze.php?id=0" target="_blank">HTML</a></li>
                    <li><a href="pages/icaze.php?id=0&export=pdf">PDF</a></li>
                </ul>
            </div>
        </div>

        <a type="button" vezife="edit" class="btn btn-circle btn-info"
           href="?id=<?= $id ?>&module=prodoc_daxili_senedler#icazeler">Düzəliş et
        </a>

        <button type="button" vezife="testiqle" class="btn btn-circle btn-success">Təstiqlə
        </button>

        <button type="button" vezife="imtina" class="btn btn-circle btn-danger" data-toggle="modal">İmtina
        </button>
    </div>
</div>

<script>
    var userId = '<?= $userId ?>';
    if(parseInt('<?= $tesdiqBtn ?>')===0)
    {
        $("#buttonlar button[vezife='testiqle']").hide();
    }
    if(parseInt('<?= $imtinaBtn ?>')===0)
    {
        $("#buttonlar button[vezife='imtina']").hide();
    }
    if(parseInt('<?= $editBtn ?>')===0)
    {
        $("#buttonlar button[vezife='edit']").hide();
    }

    function yazdir_notify(sid,hansi){
        var lenn = $("#notifySiyahisi li[kid='"+sid+"'][bolme='"+hansi+"']").length,
            say = $("#notifyaSayi").text();
        $("#notifySiyahisi li[kid='"+sid+"'][bolme='"+hansi+"']").remove();
        $("#notifyaSayi").text(parseInt(say)-parseInt(lenn));
    }

    $("#buttonlar button[vezife='testiqle']").click(function()
    {
        modal_loading(1,"<?= $MN ?>");

        yazdir_notify("<?= $sid ?>","icazeler");

        $.post("includes/icazeler/icazeTestiqle.php", {'gid':'<?= $sid ?>'}, function(status)
        {
            modal_loading(0,"<?= $MN ?>");
            location.reload();
            return;
            ///if(module=='dashboard')
            ///{
            ///	$("tr[ne=icaze]"+"tr[tid='" + <?= $sid ?> + "']").fadeOut(150,function(){$(this).remove();table_siralama()}
            ///	);
            ///	toastr['success']('Uğurla təstiqləndi')
            ///	$("#buttonlar").modal("hide");
            ///}
            ///else
            ///{
            ///	toastr['success']('Uğurla təsdiqləndi')
            ///	$("#buttonlar").modal("hide");
            ///	var aa = $("tbody[vezife='icazelerSiyahi'] tr[gid='<?= $sid ?>']");
            ///	if(aa.length>0)
            ///	{
            ///		var btnType = (status==0) ? ["danger", "$10testiqlenmeyib$"] : ["success", "$10testiqlenib$"];
            ///		aa.fadeOut(300, function()
            ///		{
            ///			$(this).children("td").eq(7).html('<button type="button" class="btn btn-xs btn-'+btnType[0]+'">'+btnType[1]+'</button>');
            ///			aa.fadeIn(300);
            ///		});
            ///	}
            ///}
        });
    });

    $("#buttonlar button[vezife='imtina']").click(function()
    {
        var mn2=imtinaModalYarat();
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
                $.post("includes/icazeler/icazeImtinaEt.php", {'gid':'<?= $sid ?>','sebeb':sebeb}, function(netice)
                {
                    modal_loading(0,mn2);
                    yazdir_notify("<?= $sid ?>","icazeler");
                    location.reload();
                    return;
                    if(module=='dashboard')
                    {
                        $("tr[ne=icaze]"+"tr[tid='" + <?= $sid ?> + "']").fadeOut(150,function(){$(this).remove();table_siralama()}
                        );
                        toastr['error']('Uğurla imtina edildi')

                    }
                    else
                    {
                        toastr['error']('Uğurla imtina edildi')
                        var aa = $("tbody[vezife='icazelerSiyahi'] tr[gid='<?= $sid ?>']");
                        if(aa.length>0)
                        {
                            aa.children("td").eq(7).html('<button class="btn btn-xs btn-info" type="button">$10imtinaEdilib$</button>');
                            var yeniCedvel = aa.parent("tbody").parent("table").attr("id");
                            var setirr = aa.html();
                            aa.fadeOut(300, function()
                            {
                                $(this).children("td").eq(7).html('<button class="btn btn-xs btn-info" type="button">$10imtinaEdilib$</button>');
                                aa.fadeIn(300);
                            });
                        }
                    }
                    $("#bosh_modal"+mn2).modal("hide");
                    $("#buttonlar").modal("hide");
                });
            }
        });
    });


</script>
