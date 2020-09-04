<?php

$MN = 0;
$userId = (int)$_SESSION['erpuserid'];
$getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
$userGroup = (int)$getUserInfo['Groupp'];

$pri_check = pdof()->query("SELECT privs FROM tb_group_privs_new tb1 WHERE group_id='$userGroup' AND tb1.modul_id = (SELECT id FROM tb_modules tb2 WHERE tb2.module='xestelik_vereqi')")->fetch();
$pri = (int)$pri_check[0];

$sid = $id;
$mtInf = pdof()->query("SELECT * FROM v_xestelik_vereqleri WHERE id='$sid'")->fetch();
$goster = false;
$legal_illegal      = $user->getActiveSystemType();

if($mtInf['user_id']==$userId)
{
    $goster = true;
}

$tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_xestelik_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.xestelik_vereqi_id='$sid' AND tb1.emeliyyat_tip='tesdiqleme'");

$testiqBtn = false;
$vekaletnameUserYoxla = 0;

while($trInfo = $tesdiqleyenler->fetch())
{
    if((int)$trInfo['status']===1)
    {
    }
    else
    {
        if((int)$trInfo['vekaletname_user']===$userId)
        {
            $vekaletnameUserYoxla = (int)$trInfo['user_id'];
            $testiqBtn = true;
        }
        if((int)$trInfo['user_id']===$userId)
        {
            $testiqBtn = true;
        }
    }
}

if($goster==true || (isset($pri) && (int)$pri!==0))
{
    $changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='xestelik_vereqi' AND kid='$sid' AND user_id='$userId'");

    if($testiqBtn && (in_array($userId,explode(",",$mtInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mtInf['tesdiqleme_geden_userler']))))===false)
    {
        $testiqBtn = false;
    }
    $imtinaBtn = false;
    if((int)$mtInf['status']!==3 && (in_array($userId,explode(",",$mtInf['tesdiqleme_geden_userler'])) || $userId==$mtInf['user_id'] || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$mtInf['tesdiqleme_geden_userler'])))))
    {
        $imtinaBtn = true;
    }

    $elementler = array(
        "sid"           => $sid,
        "editBtn"       => (int)($userId==$mtInf['user_id'] || $userId==$mtInf['elave_eden_user']),
        'tesdiqBtn'     => (int)$testiqBtn,
        'imtinaBtn'     => (int)$imtinaBtn,
    );

    extract($elementler);
}
?>

<div id="xestelik-vereqi-buttonlar">
    <div class="btn-group" role="group">

        <a type="button" style="<?= $edit_vez ?>" vezife="edit" class="btn btn-circle btn-info"
           href="?id=<?= $id ?>&module=prodoc_daxili_senedler#xestelik_vereqi">Düzəliş et
        </a>

        <button type="button" style="<?= $testiqBtn ?>" vezife="testiqle" class="btn btn-circle btn-success">Təstiqlə</button>

        <button type="button" style="<?= $imtinaBtn ?>" vezife="imtina" class="btn btn-circle btn-danger" data-toggle="modal"
                href="#ezamiyyetImtina">İmtina
        </button>

    </div>
</div>

<script>

    if(parseInt('<?= $tesdiqBtn ?>')===0)
    {
        $("#xestelik-vereqi-buttonlar button[vezife='testiqle']").hide();
    }
    if(parseInt('<?= $imtinaBtn ?>')===0)
    {
        $("#xestelik-vereqi-buttonlar button[vezife='imtina']").hide();
    }
    if(parseInt('<?= $editBtn ?>')===0)
    {
        $("#xestelik-vereqi-buttonlar button[vezife='edit']").hide();
    }

    $("#xestelik-vereqi-buttonlar button[vezife='testiqle']").click(function()
    {
        modal_loading(1,"<?= $MN ?>");
        $.post("includes/xestelik_vereqi/xestelik_vereqiTestiqle.php", {'gid':'<?= $sid ?>'}, function(status)
        {
            modal_loading(0,"<?= $MN ?>");
            location.reload();
            if(module=='dashboard')
            {
                $("tr[ne=icaze]"+"tr[tid='" + <?= $sid ?> + "']").fadeOut(150,function(){$(this).remove();table_siralama()}
                );
                toastr['success']('<?= dil::soz("xv_etr_ugurla_testiqlendi") ?>');
                $("#xestelik-vereqi-buttonlar").modal("hide");
            }
            else
            {
                toastr['success']('<?= dil::soz("xv_etr_ugurla_testiqlendi") ?>');
                $("#xestelik-vereqi-buttonlar").modal("hide");
                var aa = $("tbody[vezife='icazelerSiyahi'] tr[gid='<?= $sid ?>']");
                if(aa.length>0)
                {
                    var btnType = (status==0) ? ["danger", "xv_etr_testiqlenmeyib"] : ["success", "xv_etr_testiqlenib"];
                    aa.fadeOut(300, function()
                    {
                        $(this).children("td").eq(6).html('<button type="button" class="btn btn-xs btn-'+btnType[0]+'">'+btnType[1]+'</button>');
                        aa.fadeIn(300);
                    });
                }
            }
        });
    });

    $("#xestelik-vereqi-buttonlar button[vezife='imtina']").click(function()
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
                $.post("includes/xestelik_vereqi/xestelik_vereqiImtinaEt.php", {'gid':'<?= $sid ?>','sebeb':sebeb}, function(netice)
                {
                    modal_loading(0,mn2);
                    if(module=='dashboard')
                    {
                        $("tr[ne=icaze]"+"tr[tid='" + <?= $sid ?> + "']").fadeOut(150,function(){$(this).remove();table_siralama()}
                        );
                        toastr['error']('<?= dil::soz("xv_etr_ugurla_imtina_edildi") ?>')

                    }
                    else
                    {
                        toastr['error']('<?= dil::soz("xv_etr_ugurla_imtina_edildi") ?>')
                        location.reload();
                        var aa = $("tbody[vezife='icazelerSiyahi'] tr[gid='<?= $sid ?>']");
                        if(aa.length>0)
                        {
                            aa.children("td").eq(6).html('<button class="btn btn-xs btn-info" type="button">xv_etr_imtina_edilib</button>');
                            var yeniCedvel = aa.parent("tbody").parent("table").attr("id");
                            var setirr = aa.html();
                            aa.fadeOut(300, function()
                            {
                                $(this).children("td").eq(6).html('<button class="btn btn-xs btn-info" type="button">xv_etr_imtina_edilib</button>');
                                aa.fadeIn(300);
                            });
                        }
                    }
                    $("#bosh_modal"+mn2).modal("hide");
                    $("#xestelik-vereqi-buttonlar").modal("hide");
                });
            }
        });
    });

</script>
