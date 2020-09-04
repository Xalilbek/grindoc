<?php

$sessionUserId = $_SESSION['erpuserid'];

$userId = (int)$_SESSION['erpuserid'];
$getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
$userGroup = (int)$getUserInfo['Groupp'];

$pri_check = pdof()->query("SELECT module,(SELECT TOP 1 privs FROM tb_group_privs_new tb2 WHERE tb2.group_id=$userGroup AND tb2.modul_id=tb1.id) privs FROM tb_modules tb1 WHERE tb1.module='icazeler' OR tb1.module='report_id1_new' OR tb1.module='mezuniyyetler'");
$pri = array();
while ($aa = $pri_check->fetch()) {
    $pri[$aa['module']] = $aa["privs"];
}

$mezuniyyetInf = pdof()->query("SELECT *,(SELECT dovr FROM tb_teyinatlar WHERE id=tb1.vacation_type) AS vacation_period FROM v_mezuniyyetler tb1 WHERE id='$id'")->fetch();

$changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='mezuniyyetler' AND kid='$id' AND user_id='$userId'");

$getUserInf1 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$mezuniyyetInf['user_id'] . "'")->fetch();
$getUserInf2 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$mezuniyyetInf['elave_eden_user'] . "'")->fetch();

$carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_mezuniyyetler_tesdiqleme WHERE mezuniyyet_id='$id' AND status='0'")->fetch();
$carQrup = (int)$carQrup[0];
$tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_mezuniyyetler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.mezuniyyet_id='$id' AND (emeliyyat_tip='tesdiqleme' OR emeliyyat_tip='hr_approve')")->fetchAll();

$tesdiqBtn = false;
$HRapproveBTN = false;
$vekaletnameUserYoxla = 0;
foreach ($tesdiqleyenler AS $trInfo) {
    if ((int)$trInfo['status'] === 1) {
        if ((int)$trInfo['user_id'] === $userId && $carQrup == (int)$trInfo['qrup']) {
            $tesdiqBtn = false;
        }
    } else {
        if ((int)$trInfo['vekaletname_user'] === $userId && $carQrup == (int)$trInfo['qrup'] && $trInfo['emeliyyat_tip'] == "tesdiqleme") {
            $vekaletnameUserYoxla = (int)$trInfo['user_id'];
            $tesdiqBtn = true;
        }
        if ((int)$trInfo['user_id'] === $userId && $carQrup == (int)$trInfo['qrup'] && $trInfo['emeliyyat_tip'] == "tesdiqleme") {
            $tesdiqBtn = true;
        }
        if ((int)$trInfo['user_id'] === $userId && $carQrup == (int)$trInfo['qrup'] && $trInfo['emeliyyat_tip'] == "hr_approve") {
            if ($mezuniyyetInf['vacation_period'] != 1) {
                $tesdiqBtn = true;
            } else {
                $HRapproveBTN = true;
            }
        }
    }
}

$imtinaBtn = false;
if ((int)$mezuniyyetInf['status'] !== 3 && (in_array($userId, explode(",", $mezuniyyetInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla > 0 && in_array($vekaletnameUserYoxla, explode(",", $mezuniyyetInf['tesdiqleme_geden_userler']))) || $userId == $mezuniyyetInf['user_id'] || $userId == $mezuniyyetInf['elave_eden_user'])) {
    $imtinaBtn = true;
}

$hasOrder = pdof()->query("SELECT * FROM tb_prodoc_vacation_orders WHERE vacation_id='$id'")->fetch();
$testiqBtn = (int)$tesdiqBtn == 0 ? 'display:none' : 'display:inline';
$HRapproveBTN = $HRapproveBTN == false ? 'display:none' : 'display:inline';
$editImtinaBtn = (isset($_POST['forTimecontrol']) || isset($_POST['forReportp1'])) ? 0 : $imtinaBtn;
$editImtinaBtn = $hasOrder || $editImtinaBtn == 0 ? 'display:none' : 'display:inline';
$editBtn = $mezuniyyetInf['elave_eden_user'] == $userId && !$hasOrder ? 'display:inline' : 'display:none';

$order_btn = ($mezuniyyetInf['status']==1 && pdof()->query("SELECT * FROM tb_mezuniyyetler_tesdiqleme WHERE emeliyyat_tip='emr_hazirlanmasi' AND mezuniyyet_id='$id' AND user_id='$userId'")->fetch() && !pdof()->query("SELECT * FROM tb_prodoc_vacation_orders WHERE vacation_id='$id'")->fetch()) ? '<button class="btn btn-warning" onclick="templateYukle(\'prodoc_vacation_order\',\''.dil::soz("26_mez_etr_Məzuniyyət əmri - Əlavə et").'\',{\'sid\':0,\'vacation_id\':\''.$id.'\'},65,true);" type="button">Əmr formalaşdır</button>' : '';

?>
<div id="mezuniyyet-buttonlar">
	<div class="btn-group" role="group">

		<div style="display: inline-block;" vezife="export">
			<a type="button" class="btn btn-default" href="pages/mezuniyyet.php?id=0" target="_blank"><i
					class="fa fa-download"></i></a>

			<div class="btn-group" role="group">
				<button aria-expanded="false" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-angle-down"></i>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="pages/mezuniyyet.php?id=0" target="_blank">HTML</a></li>
					<li><a href="pages/mezuniyyet.php?id=0&export=pdf">PDF</a></li>
				</ul>
			</div>
		</div>

        <?= $order_btn ?>


		<a type="button" style="<?= $editBtn ?> ;margin-right: 8px;" vezife="edit" class="btn btn-circle btn-info"
		   href="?id=<?= $id ?>&module=prodoc_daxili_senedler#mezuniyyet_erizesi">Düzəliş et
		</a>

		<button type="button" style="<?= $testiqBtn ?>" vezife="testiqle" class="btn btn-circle btn-success">Təstiqlə
		</button>

		<button type="button" style="<?= $HRapproveBTN ?>" vezife="hr_approve" class="btn btn-circle btn-success">Təstiqlə
		</button>

		<button type="button" style="<?= $editImtinaBtn ?>" vezife="imtina" class="btn btn-circle btn-danger" data-toggle="modal"
				href="#mezuniyyetImtina">İmtina
		</button>
	</div>
</div>

<script>
	var gid = '<?= $id ?>';

	$("#mezuniyyet-buttonlar div[vezife='export']").html($("#mezuniyyet-buttonlar div[vezife='export']").html().replace(/mezuniyyet\.php\?id\=([0-9]+)/g,"mezuniyyet.php?id="+gid));

	$("#mezuniyyet-buttonlar button[vezife='testiqle']").click(function()
	{
		$("#mezuniyyet-buttonlar div[vezife='loading']").show();
		if(gid>0)
		{
			$.post("includes/mezuniyyetler/mezuniyyetTestiqle.php", {gid:gid}, function(status)
			{
				$("#mezuniyyet-buttonlar div[vezife='loading']").hide();
				$("#mezuniyyet-buttonlar").modal('hide');
				if(module=='dashboard')
				{
					$("tr[ne=mezuniyyet]"+"tr[tid='" + gid + "']").fadeOut(150,function(){$(this).remove();table_siralama()});
					toastr['success']('Uğurla təstiqləndi!');
				}
				else
				{
					location.reload();
					//var btnType = (status==0) ? ["danger", "$26_mez_etr_tesdiqlenmeyib$"] : ["success", "$26_mez_etr_tesdiqlenib$"];
					//var aa = $("tbody[vezife='mezuniyyetlerSiyahi'] tr[gid='" + gid + "']");
					//aa.fadeOut(300, function()
					//{
					//	$(this).children("td").eq(12).html('<button type="button" class="btn btn-xs btn-' + btnType[0] + '">' + btnType[1] + '</button>');
					//	aa.fadeIn(300);
					//});
				}
			});
		}
	});
	$("#mezuniyyet-buttonlar button[vezife='hr_approve']").click(function()
	{
		templateYukle("proid_vacation_hr_approve", "Təstiqlə", {'gid':gid});
	});

	$("#mezuniyyet-buttonlar button[vezife='imtina']").click(function()
	{
		var mn2= imtinaModalYarat();

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
				$.post("includes/mezuniyyetler/mezuniyyetImtinaEt.php", {'gid':gid,'sebeb':sebeb}, function(netice)
				{
					modal_loading(0,mn2);
					if(module=='dashboard')
					{
						$("tr[ne=mezuniyyet]"+"tr[tid='" + gid + "']").fadeOut(150,function(){$(this).remove();table_siralama()}
						);
						toastr['error']("İmtina edildi");
					}
					else
					{
						location.reload();
					}
					$("#bosh_modal"+mn2).modal("hide");
					$("#mezuniyyet-buttonlar").modal("hide");
				});
			}
		});
	});
</script>