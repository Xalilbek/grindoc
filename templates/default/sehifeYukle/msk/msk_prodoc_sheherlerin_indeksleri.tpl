<style type="text/css">
	#tab_13 tbody[vezife='menyular1'] td{
		cursor: pointer;
	}
</style>

<div id="tab_13">
	<div class="row">
		<div class="col-md-8">
			<table class="table table-striped table-bordered table-advance table-hover" id="contact_types">
				<thead>
				<tr>
					<th>№</th>
					<th>Şəhərlərin indeksləri</th>
					<th style="width: 200px;">Region</th>
					$theadElements$
				</tr>
				</thead>
				<tbody vezife="menyular1">
				$contactTypes$
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">

	$('select').select2();

	var fildTypesHtml = '<select class="form-control"><option></option>$tipler$</select>';

	$("#tab_13 tbody[vezife='menyular1'] tr:first td:first").click();

	//// CONTACT TYPES ////
	$('#contact_type_add').click(function()
	{
		var tableId = "contact_types";
		if($("#"+tableId+" tbody tr[time='null']").length)
		{
			$('#'+tableId+' tbody tr').remove();
		}
		var say = $('#'+tableId+' tbody tr').length+1;
		$('#'+tableId+' tbody').append("<tr tr_id='0' del='1'><td style='width:20px;text-align:center;'>"+say+"</td><td><input maxlength='75' class='form-control'></td><td>"+fildTypesHtml+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> </a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> </a></td></tr>");

		$('input[maxlength]').maxlength();
		$('#'+tableId+' tbody').find('select').select2();
		$('#'+tableId+' tbody tr:last .yellow').click(function()
		{
			$(this).parent('td').parent('tr').remove();
		});
	});

	$('#contact_types>tbody').on("click",".blue",function(e)
	{
		var tableId = $(this).parents("table").eq(0).attr("id"),
			t = $(this),
			tr = t.parents("tr").eq(0),
			sayi = tr.children('td').eq(0).text(),
			ad = tr.children('td').eq(1).find("input").val(),
			fildType = tr.children('td').eq(2).find("select").val(),
			fildTypeName = tr.children('td').eq(2).find("select option:selected").text(),
			tid = tr.attr("tr_id");

		// seçilib?
		var sechilib = "";
		if (tr.find("td[sechilib='1']").length) {
			sechilib = "sechilib='1'";
		}

		var disabled = "";
		if (tr.attr('del') === '0') disabled = " disabled ";

		if(ad.trim()=="")
		{
			tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">$9956error2$</td></tr>');
			setTimeout(function(){$('#'+tableId+' tbody tr[error]').remove();}, 3000);
		}
		else
		{
			$.post("prodoc/ajax/msk/msk_prodoc_sheherlerin_indeksleri.php", {'tid':tid,'ad':ad, "fildType":fildType},function(netice)
			{
				if(netice == "error")
				{
					tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">$9956error3$</td></tr>');
					setTimeout(function(){$('#'+tableId+' tbody tr[error]').remove();}, 3000);
				}
				else
				{
					tr.attr("tr_id",netice).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+ad+"</td><td val="+fildType+" "+sechilib+">"+fildTypeName+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> </a><a "+disabled+" href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> </a></td>");
				}
			});
		}
		e.stopPropagation();
	});

	$('#contact_types>tbody').on("click",".purple",function(e)
	{
		var th = $(this).parent('td').parent('tr'),
			idN = th.attr("tr_id"),
			ad = th.children("td").eq(1).text(),
			fildType = th.children("td").eq(2).attr('val'),
			fildTypeName = th.children("td").eq(2).text(),
			say = $(this).parent('td').parent("tr").children('td').eq(0).text();

		// seçilib?
		var sechilib = "";
		if (th.find("td[sechilib='1']").length) {
			sechilib = "sechilib='1'";
		}

		var disabled = "";
		if (th.attr('del')=='0') disabled = " disabled ";

		th.html("<td style='width:20px;text-align:center;'>"+say+"</td><td><input  maxlength='75' class='form-control' value='"+ad+"'></td><td "+sechilib+">"+fildTypesHtml+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> </a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> </a></td>");
		$('input[maxlength]').maxlength();
		th.find('select').select2().select2('val', fildType);
		th.find('.yellow').click(function()
		{
			th.html("<td style='width:20px;text-align:center;'>"+say+"</td><td>"+ad+"</td><td val="+fildType+" "+sechilib+">"+fildTypeName+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> </a><a "+disabled+" href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> </a></td>");
		});
		e.stopPropagation();
	});

	$('#contact_types>tbody').on("click",".red",function(e)
	{
		tableId = $(this).parents("table").eq(0).attr("id");
		var idsi = $(this).parent('td').parent('tr').attr("tr_id");
		var t = $(this);

		swals({
				title: "$9956eminsinizhead$",
				text: "$9956eminsinizdesc$",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-danger",
				confirmButtonText: "$9956ok$",
				cancelButtonText: "$9956bagla$",
				closeOnConfirm: true
			},
			function(){
				$.post("prodoc/ajax/msk/msk_prodoc_sheherlerin_indeksleri.php", {'tid':idsi,'ne':"sil"}).done(function(netice)
				{
					t.parent('td').parent('tr').remove();
					if($('#'+tableId+' tbody').children('tr').children('td').length==0)
					{
						$('#'+tableId+' tbody').html("<tr time='null'><td colspan='100%'>$9956empty$</td></tr>");
					}
					swals("$9956silindi$", "$9956silindi$", "success");
				});
			});
		e.stopPropagation();
	});

</script>