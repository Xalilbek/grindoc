<a type="button" vezife="edit" class="btn btn-outline btn-circle blue-steel"
   href="?id=$sid$&daxil_olan_sened_id=$daxil_olan_sened_id$&module=prodoc_daxili_senedler#$type$">Düzəliş et
</a>

<button type="button" vezife="testiqle" data-tip="$approveType$" class="btn btn-outline btn-circle green-meadow"> $approveBtnTitle$</button>
<button type="button" vezife="imtina" class="btn btn-outline btn-circle red-intense"> $47imtina$</button>

<button type="button" style="display: none" class="btn btn-outline btn-circle red-intense qiymetlendirme"
		onclick='templateYukle("satin_alma","Mal-Material",{"sender_id": "$sid$", "edit":true, "testiq_id": "$testiq_id$"},80,true,"green");'>
	<i class="fa fa-commenting"></i> Qiymətləndirmə
</button>

<button type="button" style="display: none" class="btn btn-outline btn-circle red-intense umumi_shobe_netice"
		onclick='templateYukle("umumi_shobe_netice","Nəticə",{"sid": "$testiq_id$", "from_daxili": 1},0,true,"red");'>
	<i class="fa fa-commenting"></i> Nəticə qeyd et
</button>

<script type="text/javascript">
	$(".form-group label").css("padding-top", "13px");

	if("$edits$"=="")
	{
		$("div[vezife='edits']").hide();
	}
	if(parseInt('$tesdiqBtn$')===0)
	{
		$("button[vezife='testiqle']").hide();
	}
	if(parseInt('$imtinaBtn$')===0)
	{
		$("button[vezife='imtina']").hide();
	}
	if(parseInt('$editBtn$')===0)
	{
		$("a[vezife='edit']").hide();
	}

	if('$approveType$'==='umumi_shobe_netice')
	{
		$(".umumi_shobe_netice").show();
	}
	else if('$approveType$'==='qiymetlendirme')
    {
        $(".qiymetlendirme").show();
        $("button[vezife='testiqle']").hide();
        $("button[vezife='edit']").hide();
        $("button[vezife='imtina']").hide();
    }

	if(parseInt('$project_id$')===1)
	{
        $("#emeliyyat-duymeleri").on('click', '.yoxlayici_qebul', function () {
            var tr = getActiveTr();
            var id = tr.attr('sened-id');
            var tip = tr.attr('tip');
            sherhYazilsin(function(sebeb) {
                    $.post('prodoc/ajax/change_status.php?action=yoxlayici_qebul', {
                    	tip: tip,
                    	id: id,
                        note: sebeb
                }, function () {
                    $('#senedler-tbody').find('tr.selected td').trigger('click');
                    toastr["success"]('Qəbul olundu');
                    refreshActiveDocument();
                });
            }, $(this).text());
        });


        $("button[vezife='testiqle']").click(function()
        {
            var tip = $(this).attr('data-tip');

            if (tip === "mezuniyet_emri") {
                location.href = "index.php?module=prodoc_daxili_senedler&vacation_id=$sid$#mezuniyet_emri";
            } else if (tip === "ezamiyet_emri") {
                location.href = "index.php?module=prodoc_daxili_senedler&business_trip_id=$sid$#ezamiyet_emri";
            } else {
                sherhYazilsin(function(sebeb) {
                $.post("ajax/prodoc/formlar/form_approve.php",
                    {
                        'testiq_id':'$testiq_id$',
                        'note': sebeb
                    },
                    function(status)
                    {
                        refreshActiveDocument()
                    }
                );
                }, $(this).text());
            }
        });


    }
    else{

        $("button[vezife='testiqle']").click(function()
        {
            var tip = $(this).attr('data-tip');

            if (tip === "mezuniyet_emri") {
                location.href = "index.php?module=prodoc_daxili_senedler&vacation_id=$sid$#mezuniyet_emri";
            } else if (tip === "ezamiyet_emri") {
                location.href = "index.php?module=prodoc_daxili_senedler&business_trip_id=$sid$#ezamiyet_emri";
            } else {
                $.post("ajax/prodoc/formlar/form_approve.php",
                    {
                        'testiq_id':'$testiq_id$',
                        'type':'$type$'
                    },
                    function(status)
                    {
                        refreshActiveDocument()
                    }
                );
            }
        });
    }

	if(parseInt('$status$')!=0)
	{
		$("button[vezife='testiqle']").hide();
		$("button[vezife='edit']").hide();
		$("button[vezife='imtina']").hide();
	}


	$("button[vezife='imtina']").click(function()
	{
		var mn2=modal_yarat("$47imtina_et$","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-4 control-label'>$47sebeb$</label><div class='col-md-6'><textarea class='form-control' placeholder='$47sebebi_daxil_edin$' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn red btn-circle tesdiqle'>$47imtina_et$</button> <button class='btn default btn-circle' data-dismiss='modal'>$47bagla$</button>","btn-danger","",true);
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
				$("#bosh_modal"+mn2+" button.tesdiqle").css("border","");
				$.post("ajax/prodoc/formlar/form_cancel.php",
					{
						'gid':'$sid$',
						'sebeb':sebeb,
						'type':'$type$'
					},
					function(netice)
					{
						refreshActiveDocument();
						$("#bosh_modal"+mn2).modal('hide');
					}
				);
			}
		});
	});

</script>