<div style="padding: 0;">
    <form class="form-horizontal form-bordered form-row-stripped">
        <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-4">$26yeni_mez_Məzuniyyət alacaq şexs$:</label>
                <div class="col-md-6">
                    <input class="form-control select2me" vezife="employe" placeholder="$26yeni_mez_Məzuniyyət alacaq şexs$" />
                    <!-- <select class="form-control select2me" vezife="employe" placeholder="$26yeni_mez_Məzuniyyət alacaq şexs$" onchange="$(this).next('span').hide().text('$26yeni_mez_Bolme$: '+($(this).children(':selected').attr('bolme') || '-')).fadeIn(150);">
                        $emekdashlar$
                    </select> -->
                    <span class="help-block"></span>
                </div>
                <div class="col-md-1"><button class="btn btn-default tooltips" onclick="var uid=$(this).parent('div').prev('div').children('input').eq(0).select2('val');if(uid>0){templateYukle('tesdiqleyecek_emekdashlarin_siyahisi','$26yeni_mez_Təsdiqləyəcək əməkdaşların siyahısı$',{'sened':'mezuniyyet','uid':uid},0,true);}" data-original-title="$26yeni_mez_Təsdiqləyəcək əməkdaşlar$" type="button"><i class="fa fa-info"></i></button></div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">$26yeni_mez_İlk tarix$:</label>
                <div class="col-md-3">
                    <input type="text" class="form-control" value="<?= $start_date ?>" vezife="start_date" placeholder="$26yeni_mez_Başlanğıc tarixi$">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">$26yeni_mez_Məzuniyyət müddəti$:</label>
                <div class="col-md-2">
                    <input disabled="" type="text" class="form-control" vezife="vacation_day_count" placeholder="$26yeni_mez_Məzuniyyət gün sayı$" value="<?= $vac_days ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">$26yeni_mez_Son tarix$:</label>
                <div class="col-md-3">
                    <input disabled="" type="text" class="form-control" value="<?= $end_date ?>" vezife="end_date" placeholder="$26yeni_mez_Son tarix$">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">$26yeni_mez_Təyinat$:</label>
                <div class="col-md-6">
                    <select class="form-control select2me" vezife="vacation_type" placeholder="$26yeni_mez_Təyinatı seçin$">
                        <?= $types ?>
                    </select>
                    <span class="help-block vacation_type2" style='display: none;'></span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">$26yeni_mez_Qoşma$:</label>
                <div class="col-md-8">
                    <div class="row" style="padding-bottom:10px;"><div class="col-md-12 senedlerSiyahi23"><?= $attachments ?></div></div>
                    <div class="row"><div class="col-md-8"><input vezife="sened" type="file" multiple></div><div class="col-md-2"><i class="fa fa-trash" style="cursor: pointer;" vezife="sil" onclick='$(this).parent("div").parent("div").fadeOut(200, function(){$(this).remove()});'></i></div></div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-link addNewFile" type="button" style="padding-left: 2px;"><i class="icon-plus"></i> $26yeni_mez_Yenisini əlavə et$</button>
                            <p class="help-block">$26yeni_mez_İcazəli fayl uzantıları$: .pdf, .doc, .docx, .xls, .xlsx, .jpg, .jpeg, .png, .gif, .zip, .rar</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">$26yeni_mez_Məlumat$:</label>
                <div class="col-md-6">
                    <textarea class="form-control" vezife="about" style="resize:vertical;"><?= $about ?></textarea>
                </div>
            </div>
        </div>
    </form>
</div>
<div style="border-top: 0;">
    <div style="float: left; color: red;" vezife="error"></div>
    <div style="float: right;">
        <button type="button" data-v="testiqle" class="btn green">$26yeni_mez_Əlavə et$</button>
        <button type="button" data-dismiss="modal" class="btn default">$26yeni_mez_Imtina$</button>
    </div>
</div>
<script>
    bosh_modal.find("input[vezife='employe']").change(function()
    {
        getDataIntervalDays('enddate');
    });
    bosh_modal.find("select").select2();
    bosh_modal.find("input[vezife='employe']").select2({
        allowClear: true,
        minimumInputLength: userAjaxInputLength,
        ajax: {
            url: "includes/plugins/axtarish.php",
            type: 'POST',
            dataType: 'json',
            data: function (soz)
            {
                return {
                    'a': soz,
                    'ne': 'employee',
                    'tip': 'ishleyenler'
                };
            },
            results: function(data,a)
            {
                return data
            },
            cache: true
        }
    }).on('change',function(){
        //if($(this).select2('val') != '')
        //{
        //	if(parseInt('<?= $userId ?>') == 0){
        //		$(this).next('span').hide().text('$26yeni_mez_Bolme$: '+($(this).select2('data').stName || '-')).fadeIn(150);
        //	} else {
        //		$(this).next('span').hide().text('$26yeni_mez_Bolme$: <?= $user_bolme ?>').fadeIn(150);
        //	}
        //}
    });

    function showHolidays(e)
    {
        if((e>0)==false) return;

        var start_date = bosh_modal.find("input[vezife='start_date']").val(),
            end_date = bosh_modal.find("input[vezife='end_date']").val(),
            employe = bosh_modal.find("input[vezife='employe']").select2("val");

        bosh_modal.find("div[melumat] i").proPopover({"ajax":{"template":"holidays","data":{"start_date":start_date,"end_date":end_date,"employe":employe>0?employe:0}},"title":"Test"});
    }

    function getDataIntervalDays(type)
    {
        var start_date = bosh_modal.find("input[vezife='start_date']").val(),
            employe = bosh_modal.find("input[vezife='employe']").select2("val"),
            vacation_day_count = bosh_modal.find("input[vezife='vacation_day_count']").val();

        if(start_date!="" && vacation_day_count>0)
        {
            console.log(employe);
            $.post("ajax/proid/vacation/gunSayiCHixart.php",{'employe':Number(employe)>0?employe:0,'type':type,'vacation_day_count':vacation_day_count>0?vacation_day_count:0,'tarix1':start_date},function(netice)
            {
                try
                {
                    netice = JSON.parse(netice);
                    if(netice['status']=="error")
                    {
                        errorModal(netice['error_msg'], 2000, true);
                    }
                    else
                    {
                        bosh_modal.find("div[vezife='error']").text("");
                        bosh_modal.find("input[vezife='vacation_day_count']").parents(".col-md-6").eq(0).find("div[melumat]").remove();
                        bosh_modal.find("input[vezife='vacation_day_count']").parents(".col-md-6").eq(0).append('<div melumat style="color: #2D8F3C;height:20px;margin-top: 5px;"> Qeyri iş günləri: '+netice["holidayCount"]+' gün <i class="fa fa-info-circle" style="cursor:pointer" onclick="showHolidays('+netice["holidayCount"]+')"></i> </div>');
                        bosh_modal.find("input[vezife='end_date']").parents(".col-md-6").eq(0).find("div[melumat]").hide().slideDown(300);

                        bosh_modal.find("input[vezife='vacation_day_count']").val(netice["vacation_day_count"]);
                        bosh_modal.find("input[vezife='start_date']").val(netice["start_date"]);
                        bosh_modal.find("input[vezife='end_date']").val(netice["end_date"]);
                    }
                }
                catch(e)
                {

                }
            });
        }
    }

    if(parseInt('<?= $userId ?>')>0)
    {
        bosh_modal.find("input[vezife='start_date']").trigger('changeDate');
        bosh_modal.find("input[vezife='employe']").select2('data', {id: parseInt('<?= $userId ?>'), text : '<?= $adSoyad ?>'});
        // getDataIntervalDays('enddate');
    }

    bosh_modal.find("input[vezife='start_date']").datepicker({
        autoclose: true,
        format: "dd-mm-yyyy"
    }).on("changeDate",function()
    {
        bosh_modal.find("input[vezife='vacation_day_count']").removeAttr("disabled");
        getDataIntervalDays('enddate');
    });

    bosh_modal.find("input[vezife='vacation_day_count']").change(function()
    {
        getDataIntervalDays('enddate');
    });
    var WRconfirmOK = false;
    bosh_modal.find("button[data-v='testiqle']").click(function()
    {
        bosh_modal.find("input[vezife='end_date']").datepicker('update');
        var t = $(this),
            date1 = bosh_modal.find("input[vezife='start_date']").val(),
            vacation_day_count = bosh_modal.find("input[vezife='vacation_day_count']").val(),
            date_21 = bosh_modal.find("input[vezife='start_date']").datepicker("getDate"),
            about = bosh_modal.find("textarea[vezife='about']").val(),
            employe = bosh_modal.find("input[vezife='employe']").select2('val'),
            vacation_type = parseInt(bosh_modal.find("select[vezife='vacation_type']").val()),
            oldAttachments = "",
            hasError = false;
        // console.log(employe);

        bosh_modal.find(".senedlerSiyahi23 a[sened]").each(function()
        {
            oldAttachments += (oldAttachments==""?"":",") + $(this).attr("sened");
        });

        // if(about.trim()=="")
        // {
        // 	hasError = true;
        // 	bosh_modal.find("textarea[vezife='about']").css("border","1px dotted red");
        // }
        // else
        // {
        // 	bosh_modal.find("textarea[vezife='about']").css("border","");
        // }

        if(date_21==null)
        {
            hasError = true;
            bosh_modal.find("input[vezife='start_date']").css("border","1px dotted red");
        }
        else
        {
            bosh_modal.find("input[vezife='start_date']").css("border","");
        }
        if((vacation_day_count>0)==false)
        {
            hasError = true;
            bosh_modal.find("input[vezife='vacation_day_count']").css("border","1px dotted red");
        }
        else
        {
            bosh_modal.find("input[vezife='vacation_day_count']").css("border","");
        }

        if((employe>0)==false)
        {
            hasError = true;
            bosh_modal.find("input[vezife='employe']").prev("div").attr("style","border: 1px dotted red !important");
        }
        else
        {
            bosh_modal.find("select[vezife='vacation_type']").prev("div").css("border","");
        }
        if((vacation_type>0)==false)
        {
            hasError = true;
            bosh_modal.find("select[vezife='vacation_type']").prev("div").attr("style","border: 1px dotted red !important");
        }
        else
        {
            bosh_modal.find("select[vezife='vacation_type']").prev("div").css("border","");
        }

        if(!hasError)
        {
            modal_loading(1, '$MN$');
            var data = new FormData();
            data.append("gid",'<?= $gid ?>');
            data.append("date1",date1);
            data.append("vacation_day_count",vacation_day_count);
            data.append("about",about);
            data.append("employe",employe);
            data.append("vacation_type",vacation_type);
            data.append("WRconfirmOK", WRconfirmOK);
            if(bosh_modal.find("input[vezife='sened']")[0].files.length)
            {
                bosh_modal.find("input[vezife='sened']").each(function()
                {
                    for(var ii in $(this)[0].files)
                    {
                        data.append("attachments[]",$(this)[0].files[ii]);
                    }
                });
            }
            data.append("old_attachments",oldAttachments);
            $.ajax({
                url: "ajax/proid/vacation/vacation.php",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: "POST",
                success: function(result)
                {
                    modal_loading(0, '$MN$');
                    try
                    {
                        result = JSON.parse(result);
                        if(result['status']=="error" && typeof result['confirm']!="undefined" && result['confirm']===true)
                        {
                            var mn = modal_yarat("Diqqət!", "<div style='padding: 15px;'>"+result['error_msg']+"</div>", "<button type='button' class='btn btn-success confirmButton'><i class='fa fa-check'></i> Bəli</button> <button type='button' class='btn default' data-dismiss='modal'><i class='fa fa-remove'></i> Xeyr</button>", "btn-danger", 0, true)
                            $("#bosh_modal"+mn+" .confirmButton").click(function()
                            {
                                WRconfirmOK = true;
                                bosh_modal.find("button[data-v='testiqle']").click();
                                $("#bosh_modal"+mn).modal("hide");
                            });
                        }
                        else if(result['status']=="error")
                        {
                            errorModal(result['error_msg'], 5000, true);
                        }
                        else
                        {
                            modal_loading(1,'$MN$');
                            location.reload();
                        }
                    }
                    catch(e)
                    {

                    }
                    WRconfirmOK = false;
                }
            });
        }
    });

    bosh_modal.find("select[vezife='vacation_type']").change(function()
    {
        var tip = $(this).children(":selected").attr("tip");
        $(this).next(".vacation_type2").fadeIn(200).html("$26_mez_etr_mezuniyyet_tipi$: "+(tip==0?"$26Ödənişsiz$":"$26Ödənişli$"));
    });

    bosh_modal.find("input[vezife='employe']").trigger("change");
    bosh_modal.find(".addNewFile").click(function()
    {
        $(this).parent("div").parent("div").before('<div class="row"><div class="col-md-8" style="overflow: hidden;"><input vezife="sened" type="file" multiple></div><div class="col-md-2"><i class="fa fa-trash" style="cursor: pointer;color:trash;" vezife="sil"></i></div></div>');
        $(this).parent("div").parent("div").prev().find("i[vezife='sil']").click(function()
        {
            $(this).parent("div").parent("div").fadeOut(200, function(){$(this).remove()});
        });
        $(this).parent("div").parent("div").prev().hide().fadeIn(200);
    });

</script>