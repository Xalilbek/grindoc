<?php

$userId = (int)$_SESSION['erpuserid'];
$getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
$userGroup = (int)$getUserInfo['Groupp'];
$pri_check = pdof()->query("SELECT module,(SELECT privs FROM tb_group_privs_new tb2 WHERE tb2.group_id=$userGroup AND tb2.modul_id=tb1.id) privs FROM tb_modules tb1 WHERE tb1.module='icazeler' OR tb1.module='report_id1_new'");

$pri = array();
while($aa = $pri_check->fetch())
{
    $pri[$aa['module']]=$aa["privs"];
}
$sid = $id;
$ezamiyyetInf = pdof()->query("SELECT tb8.ad AS valyuta_name,tb1.*,tb2.ad AS sheher_ad,tb3.ad AS olke_ad,tb4.struktur_bolmesi as bolme_ad,tb5.Adi AS shirket_ad,v_user_adlar.user_ad_qisa AS arayish_teleb_etdi_ad,tb10.id AS arayish_id FROM tb_ezamiyyetler tb1 LEFT JOIN tb_general_cities tb2 ON tb2.id=tb1.sheher LEFT JOIN tb_general_countries tb3 ON tb3.id=tb1.olke LEFT JOIN tb_Struktur tb4 ON tb4.struktur_id=tb1.bolme LEFT JOIN tb_istehsalchilar tb5 ON tb5.id=tb1.shirket LEFT JOIN v_user_adlar ON tb1.arayish_teleb_etdi=v_user_adlar.USERID LEFT JOIN tb_arayishlar tb10 ON tb10.menbe='ezamiyyetler' AND tb10.menbe_id=tb1.id LEFT JOIN tb_valyuta 	tb8 ON tb1.valyuta = tb8.id WHERE tb1.id='$sid'")->fetch();

if(
(
    (int)$ezamiyyetInf['user_id']===$userId ||
    (int)$ezamiyyetInf['elave_eden_user']===$userId ||
    in_array($userId,explode(",",$ezamiyyetInf['rehberler'])) ||
    in_array($userId,explode(",",$ezamiyyetInf['tesdiqleme_geden_userler'])) ||
    pdof()->query("SELECT 1 FROM tb_ezamiyyetler_tesdiqleme WHERE ezamiyyet_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()
)
||
(
    isset($_POST['forTimecontrol']) &&
    $_POST['forTimecontrol']==true && $pri['report_id1_new']==2
)
||
(
    // tab_diger
    !in_array($userId,explode(",",$ezamiyyetInf['rehberler'])) &&
    (int)$ezamiyyetInf['accountant']!==$userId &&
    (int)pdof()->query("SELECT COUNT(id) FROM tb_ezamiyyetler_tesdiqleme WHERE ezamiyyet_id='$sid' AND vekaletname_user='$userId'")->fetchColumn() === 0 &&
    (int)$ezamiyyetInf['user_id']!==$userId

)
) {
    $carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_ezamiyyetler_tesdiqleme WHERE ezamiyyet_id='$sid' AND status='0'")->fetch();
    $carQrup = (int)$carQrup[0];
    $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_ezamiyyetler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.ezamiyyet_id='$sid' AND emeliyyat_tip<>'emr_hazirlanmasi'");

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
    if ($tesdiqBtn && (in_array($userId, explode(",", $ezamiyyetInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla > 0 && in_array($vekaletnameUserYoxla, explode(",", $ezamiyyetInf['tesdiqleme_geden_userler'])))) === false) {
        $tesdiqBtn = false;
    }
    $imtinaBtn = false;
    if ((int)$ezamiyyetInf['status'] !== 3 && (in_array($userId, explode(",", $ezamiyyetInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla > 0 && in_array($vekaletnameUserYoxla, explode(",", $ezamiyyetInf['tesdiqleme_geden_userler']))) || $userId == $ezamiyyetInf['user_id'] || $userId == $ezamiyyetInf['elave_eden_user'])) {
        $imtinaBtn = true;
    }

    $hasOrder = pdof()->query("SELECT * FROM tb_prodoc_business_trip WHERE business_trip_id='$sid'")->fetch();
    $order_btn = ($ezamiyyetInf['status'] == 1 && pdof()->query("SELECT * FROM tb_ezamiyyetler_tesdiqleme WHERE emeliyyat_tip='emr_hazirlanmasi' AND ezamiyyet_id='$sid' AND user_id='$userId'")->fetch() && !$hasOrder) ? sprintf('<button class="btn btn-warning" onclick="templateYukle(\'prodoc_business_trip\',\'' . dil::soz("25eeadd") . '\',{\'sid\':0,\'business_trip_id\':\'' . $sid . '\'},65,true);" type="button">%s</button>', dil::soz("73Əmr formalaşdır")) : '';
    $imtinaBtn = $imtinaBtn && !$hasOrder ? '' : 'display:none;';
    $edit_vez = ($userId == (int)$ezamiyyetInf['elave_eden_user'] && !$hasOrder) ? '' : 'display:none;';
    $testiqBtn = $tesdiqBtn ? '' : 'display:none;';
}
?>

<div id="ezamiyyet-buttonlar">
    <div class="btn-group" role="group">

        <?= $order_btn ?>

        <a type="button" style="<?= $edit_vez ?>" vezife="edit" class="btn btn-info"
                href="?id=<?= $id ?>&module=prodoc_daxili_senedler#ezamiyyet_erizesi">Düzəliş et
        </a>

        <button type="button" style="<?= $testiqBtn ?>" vezife="testiqle" class="btn btn-success">Təstiqlə</button>

        <button type="button" style="<?= $imtinaBtn ?>" vezife="imtina" class="btn btn-danger" data-toggle="modal"
                href="#ezamiyyetImtina">İmtina
        </button>
    </div>
</div>

<script>

    function yazdir_notify(sid,hansi){
        var lenn = $("#notifySiyahisi li[kid='"+sid+"'][bolme='"+hansi+"']").length,
            say = $("#notifyaSayi").text();
        $("#notifySiyahisi li[kid='"+sid+"'][bolme='"+hansi+"']").remove();
        $("#notifyaSayi").text(parseInt(say)-parseInt(lenn));
    }
    //$("#ezamiyyet-buttonlar").modal("hide");


    $("#ezamiyyet-buttonlar button[vezife='testiqle']").click(function()
    {
        $("#ezamiyyet-buttonlar").modal("hide");
        var gid = $sid$,
            arayish_teleb_etdi=$("#ezamiyyet-buttonlar div[vezife='arayish_teleb_olunur'] input[type='checkbox']").is(":checked")?1:0;
        $("#ezamiyyetEtrafli div[vezife='loading']").show();
        if(gid>0)
        {
            var mn2=modal_yarat("$ezam_etrafli_Təsdiqlə$","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group' ><label  class='col-md-4 control-label' style='padding-top:13px'>$ezam_etrafli_Arayısh tələb et$</label><div vezife='arayish_teleb_olunur' class='col-md-6'><input type='checkbox'></div></div></div></form>","<button class='btn green tesdiqle'>$ezam_etrafli_Təsdiqlə$</button> <button class='btn default' data-dismiss='modal'>$ezam_etrafli_Bağla$</button>","btn green","",true);
            $('input[type=checkbox]').uniform();
            $("#bosh_modal"+mn2+" button.tesdiqle").unbind("click").click(function()
            {
                arayish_teleb_etdi = $("div[vezife='arayish_teleb_olunur'] input[type='checkbox']").is(":checked")?1:0;
                $.post("includes/ezamiyyetler/ezamiyyetTestiqle.php", {'gid':gid,'arayish_teleb_etdi':arayish_teleb_etdi}, function(status)
                {
                    location.reload();
                });
            });
        }
    });



    $("#ezamiyyet-buttonlar button[vezife='imtina']").click(function()
    {
        var mn2=imtinaModalYarat();
        $("#bosh_modal"+mn2+" button.tesdiqle").click(function()
        {
            var sebeb = $("#bosh_modal"+mn2+" textarea").val().trim();
            // var avans = $("#bosh_modal"+).val().trim();
            if(sebeb=="")
            {
                $("#bosh_modal"+mn2+" textarea").css("border","1px dashed yellow");
            }
            else
            {
                modal_loading(1,mn2);
                $("#bosh_modal"+mn2+" button.tesdiqle").css("border","");
                var gid = $sid$
                $.post("includes/ezamiyyetler/ezamiyyetImtinaEt.php", {gid:gid,sebeb:sebeb}, function(netice)
                {
                    modal_loading(0,mn2);
                    yazdir_notify("$sid$","ezamiyyetler");
                    location.reload();
                    //	$("#bosh_modal"+mn2+" div[vezife='loading']").hide();
                    //	$("#ezamiyyetEtrafli .close[data-dismiss='modal']").click();
                    //	var aa = $("tbody[vezife='ezamiyyetlerSiyahi'] tr[gid='" + gid + "']");
                    //	//aa.find("td").eq(10).html('<button class="btn btn-xs btn-info" type="button">$ezam_etrafli_Imtina edilib$</button>');
                    //	var yeniCedvel = aa.parent("tbody").parent("table").attr("id");
                    //	var setirr = aa.html();
                    //	$("#bosh_modal"+mn2).modal('hide');
                    //	aa.fadeOut(300, function()
                    //	{
                    //		aa.find("td").eq(11).html('<button class="btn btn-xs btn-info" type="button">$ezam_etrafli_Imtina edilib$</button>');
                    //		aa.fadeIn(300);
                    //	});
                });
            }
        });
    });
</script>
