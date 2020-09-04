<?php
defined('DIRNAME_INDEX') or die("hara?");

Prodoc::activateClasses();


    $from_dashboard = isset($parametrler['from_dashboard']) && $parametrler['from_dashboard'] == '1' ? 1 : 0;

    $sid = (int)$_GET['id'];
    $userId = (int)$_SESSION['erpuserid'];
    $language = $user->getLang();
    $dill = new dilStclass($language);

    $getUserInfo = ProdocInternal::getUserInfo($userId);
    $userGroup = (int)$getUserInfo['Groupp'];

    $emrInf = pdof()->query("SELECT * FROM tb_prodoc_formlar_xitam_erizesi WHERE id='$sid'")->fetch();

    $tip = "prodoc_formlar_xitam_erizesi";
    Prodoc::activateClasses();
    if (
        true ||
        (int)$emrInf['elave_edib']===$userId ||
        in_array($userId,explode(",",$emrInf['rehberler'])) ||
        in_array($userId,explode(",",$emrInf['tesdiqleme_geden_userler'])) ||
        pdof()->query("SELECT 1 FROM tb_prodoc_formlar_tesdiqleme WHERE document_id='$sid' AND tip='$tip' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()
    )
    {
        $changeNotifStatus = ProdocInternal::updateNotifications($tip, $sid, $userId);

        $getUserInf2 = ProdocInternal::getUserInfo((int)$emrInf['elave_edib']);

        list($imtinaSebeb, $imtinaEden) = ProdocInternal::imtina($emrInf['status'], $emrInf['imtinaEden'], $emrInf['imtinaSebebi'], $sid, $tip);

        $edits = ProdocInternal::deyisdirib($sid, $tip);

        list($tr1, $tr2, $tesdiqBtn, $imtinaBtn) = ProdocInternal::tesdiqleyenler($sid, $tip, $userId, $emrInf['tesdiqleme_geden_userler'], (int)$emrInf['elave_edib']);
        $tesdiqBtn  =(int)$tesdiqBtn;
        $imtinaBtn  =(int)$imtinaBtn;
        $editBtn    =(int)((int)$emrInf['elave_edib'] == $userId);
        $status=(int)$emrInf['status'];
        $hide_btns= isset($parametrler['hide_buttons']) ? '1' : '0';


        }

    ?>


<button type="button" vezife="tesdiqle" class="btn btn-circle green"><?php  print dil::soz("47tesdiqle"); ?></button>

<a type="button"  vezife="edit" class="btn btn-circle blue editBtn"
   href="?id=<?= $id ?>&module=prodoc_daxili_senedler#termination_petition"><?php  print dil::soz("47duzelish_et"); ?>
</a>
<button type="button" vezife="imtina" class="btn btn-circle  btn-danger" style="margin-right:10px;"><?php  print dil::soz("47imtina"); ?></button>

<script type="text/javascript">
//    $(" .form-group label").css("padding-top", "13px");


    var userId = '<?php print $userId ?>';
    var sid = ' <?php print $sid ?>';







    if(parseInt('<?= $imtinaBtn ?>')==0)
    {
        $("button[vezife='imtina']").hide();
    }
    if(parseInt('<?= $tesdiqBtn ?>')==0)
    {

        $("button[vezife='tesdiqle']").hide();

    }
    if(parseInt('<?= $editBtn ?>')==0)
    {
        $("[vezife='edit']").hide();
    }


    if(parseInt('<?= $status ?>')!=0)
    {
        $(" button[vezife='tesdiqle']").hide();
        $(" button[vezife='edit']").hide();
        $(" button[vezife='imtina']").hide();
    }

    $(" button[vezife='tesdiqle']").click(function()
    {
        modal_loading(1,"$MN$");
        $.post("ajax/prodoc/formlar/form_approve.php",
            {
                'gid':'$sid$',
                'type':'$type$'
            },
            function(status)
            {
                if ('$from_dashboard$' == '1')
                {
                    modal_loading(0,"$MN$");
                    modal.modal('hide');
                }
                else
                {
                    var href = location.href;
                    href = href.replace(/hideNotify/, '');
                    href = href.replace(/forNotify/, '');
                    location.href = href;
                }
            }
        );
    });

    $(" button[vezife='imtina']").click(function()
    {
        var mn2=modal_yarat("<?php print dil::soz("47imtina_et") ?>","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-4 control-label'><?php print dil::soz("47sebeb") ?></label><div class='col-md-6'><textarea class='form-control' placeholder='<?php print dil::soz("47sebebi_daxil_edin") ?>' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn btn-danger cancel_yes'><?php print dil::soz("47imtina_et") ?></button> <button class='btn default' data-dismiss='modal'><?php print dil::soz("47bagla") ?></button>","btn-danger","",true);
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
                $.post("ajax/prodoc/formlar/form_cancel.php",
                    {
                        'gid':'$sid$',
                        'sebeb':sebeb,
                        'type':'$type$'
                    },
                    function(netice)
                    {
                        if ('$from_dashboard$' == '1')
                        {
                            modal_loading(0,mn2);
                            modal.modal('hide');
                        }
                        else
                        {
                            var href = location.href;
                            href = href.replace(/hideNotify/, '');
                            href = href.replace(/forNotify/, '');
                            location.href = href;
                        }
                    }
                );
            }
        });
    });

</script>


