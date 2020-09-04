<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />
<style>
	input.pattern_prefix {
		width: 49%;
		display: inline-block;
	}
	input.pattern {
		width: 49%;
		display: inline-block;
	}

	tr.selected {
		background-color: #fff1d0 !important;
	}
</style>

<div class="row">
	<div class="col-md-3">
		<label class="control-label">Bron olunma müddəti:</label>
		<div class="input-group">
			<input class="form-control brond_muddet" value="$brondMuddet$">
			<span class="input-group-addon" style="background: #FFFFFF;">Dəqiqə</span>
		</div>
	</div>
</div>

<h1>Daxil olan sənəd</h1>
<div class="row" style="margin-bottom: 10px; padding: 10px;">
	<div class="col-md-12" style="text-align: right">
		<button class="btn default copy" data-clipboard-text="$soyad$">
			<i class="fa fa-user"></i> Soyad
		</button>
		<button class="btn default copy" data-clipboard-text="$seria$">
			<i class="fa fa-list-ol"></i> Seria
		</button>
		<button class="btn default copy" data-clipboard-text="$il$">
			<i class="fa fa-calendar"></i> Il
		</button>
	</div>
</div>
<table class="table table-striped table-bordered table-advance table-hover vezife_table" data-direction="incoming">
	<thead>
		<tr>
			<th>№</th>
			<th style="width: 17%;"> Seçimlər</th>
			<th style="width: 30%;"> Seçim tipi</th>
			<th style="width: 10%;"> Sənəd növü</th>
			<th style="width: 20%;"> Nömrə</th>
			$theadElements$
		</tr>
	</thead>
	<tbody>
		$getVezifeler$
	</tbody>
</table>

<h1>Xaric olan sənəd</h1>
<div class="row" style="margin-bottom: 10px; padding: 10px;">
	<div class="col-md-12" style="text-align: right">
		<button class="btn default copy" data-clipboard-text="$shobe$">
			<i class="fa fa-building"></i> Şöbə
		</button>
		<button class="btn default copy" data-clipboard-text="$seher$">
			<i class="fa fa-map-signs"></i> Şəhər
		</button>
		<button class="btn default copy" data-clipboard-text="$seria$">
			<i class="fa fa-list-ol"></i> Seria
		</button>
		<button class="btn default copy" data-clipboard-text="$il$">
			<i class="fa fa-calendar"></i> Il
		</button>
	</div>
</div>
<table class="table table-striped table-bordered table-advance table-hover vezife_table" data-direction="outgoing">
	<thead>
	<tr>
		<th>№</th>
		<th style="width: 17%;"> Seçimlər</th>
		<th style="width: 30%;"> Seçim tipi</th>
		<th style="width: 10%;"> Sənəd növü</th>
		<th style="width: 20%;"> Nömrə</th>
		$theadElements$
	</tr>
	</thead>
	<tbody>
	$chixanNomrelenme$
	</tbody>
</table>

<h1>Daxili sənəd</h1>
<div class="row" style="margin-bottom: 10px; padding: 10px;">
	<div class="col-md-12" style="text-align: right">
		<button class="btn default copy" data-clipboard-text="$seria$">
			<i class="fa fa-list-ol"></i> Seria
		</button>
		<button class="btn default copy" data-clipboard-text="$il$">
			<i class="fa fa-calendar"></i> Il
		</button>
	</div>
</div>
<table class="table table-striped table-bordered table-advance table-hover vezife_table" data-direction="internal">
	<thead>
		<tr>
			<th>№</th>
			<th style="width: 17%;"> Seçimlər</th>
			<th style="width: 30%;"> Sənəd növü</th>
			<th style="width: 10%;"> Sənədin alt növü</th>
			<th style="width: 20%;"> Nömrə</th>
			$theadElements$
		</tr>
	</thead>
	<tbody>
	$internalNomrelenme$
	</tbody>
</table>
<script type="text/javascript" src="assets/scripts/clipboard.min.js"></script>

<script type="text/javascript">
	/* **************** FFunctions **************** */

	$('.vezife_add').click(function()
	{
		var closestTable = $(this).closest('table');

		if(closestTable.find("tbody tr[time='null']").length)
		{
			closestTable.find('tbody tr').remove();
		}
		var say = closestTable.find('tbody tr').length+1;
		var kateqoriyalar = "";//<td><input class='form-control' name='jobs'></td>
		closestTable.find('tbody').append("\
			<tr tr_id='0'>\
				<td style='width:20px;text-align:center;'>"+say+"</td>\
				<td>\
					<input type='text' class='form-control option' style='height:30px;'>\
				</td>\
				<td>\
					<input type='text' class='form-control option_values' style='height:30px;'>\
				</td>\
				<td>\
					<input type='text' class='form-control document_types' style='height:30px;'>\
				</td>\
				<td>\
					<input type='text' class='form-control pattern_prefix' style='height:30px;'>\
					<input type='text' class='form-control pattern' style='height:30px;'>\
				</td>\
				<td style='text-align:center;'>\
					<a href=\"javascript:;\" class=\"disabled btn default btn-xs green\"><i class=\"fa fa-cogs\"></i> Tənzimlə</a>\
				</td>\
				<td style='text-align:center;'>\
					<a href='javascript:;' class='btn default btn-xs blue'>\
					<i class='fa fa-save'>\
					</i> $9956save$</a>\
				</td>\
				<td style='width:100px;text-align:center;'>\
					<a href='javascript:;' class='btn default btn-xs yellow'>\
					<i class='fa fa-remove'>\
					</i> $9956cancel$</a>\
				</td>\
			</tr>");

		select22(closestTable.find('tbody tr:last .option'), 'document_number_pattern_option_list', false, closestTable.find('tbody tr:last'));
		selectOptionValues(closestTable.find('tbody tr:last .option_values'), closestTable.find('tbody tr:last'));
		select22(closestTable.find('tbody tr:last .document_types'), 'document_types', true, closestTable.find('tbody tr:last'));

		closestTable.find('tbody .yellow').click(function()
		{
			$(this).parent('td').parent('tr').remove();
		});

		vezife_save(closestTable.find('tbody tr:last .blue'));
	});

	function get_select2_text( data )
	{
		if (_.isNull(data)) {
			return '';
		}

		if(data.length > 0){
			var array_to_str = [];
			$.each(data, function(i, v){
				array_to_str.push(v.text);
			});

			return array_to_str.join(',');
		} else {
			return data.text;
		}
	}

	function set_select2_data( id, text )
	{
		console.log(id.split(',').length);
		console.log(text.split(',').length);
		if(id.split(',').length > 1 && text.split(',').length > 1 && id.split(',').length == text.split(',').length) {
			var data = [], ids = id.split(','), texts = text.split(',');
			for (var i = 0; i < ids.length; i++) {
				var obj = { 'id':ids[i],'text':texts[i] };
				data.push( obj );
			}
			return data;
		} else {
			var obj = { 'id':id,'text':text };
			return obj;
		}
	}

	function selectOptionValues(input, tr) {
		input.select2({
			multiple: true,
			allowClear: true,
			ajax: {
				url: "prodoc/includes/plugins/axtarish.php",
				type: 'POST',
				dataType: 'json',
				data: function (soz)
				{
					var direction = '';
					if (typeof tr !== "undefined") {
						direction = tr.closest('table').attr('data-direction');
					}

					var option = input.closest('tr').find('.option').select2('val');

					if (_.isEmpty(option)) {
						errorModal("İlk öncə seçimi daxil edin...",5000,true);
						return {};
					}

					return {
						'a': soz,
						'ne': 'nomrelenme_sechim_tipi',
						'option_id': option,
						'from_setting': 1,
						'direction': direction
					};
				},
				results: function(data,a)
				{
					return data
				}
			}
		});
	}

	function select22(input, ne, m, tr) {
		input.select2({
			multiple: m && ne !== 'document_number_pattern_option_list',
			allowClear: true,
			ajax: {
				url: "prodoc/includes/plugins/axtarish.php",
				type: 'POST',
				dataType: 'json',
				data: function (soz)
				{
					var direction = '';
					if (typeof tr !== "undefined") {
						direction = tr.closest('table').attr('data-direction');
					}

					if (typeof tr !== "undefined" && ne === 'document_types') {
						direction = tr.closest('table').attr('data-direction');
						if ('outgoing' === direction) {
							ne = 'muraciet_tip';
						} else if ('incoming' === direction) {
							ne = 'document_types';
						} else if ('internal' === direction) {
							ne = 'daxili_alt_nov';
						}
					}

					var options = tr.find('.option_values').select2('val');

					return {
						'a': soz,
						'ne': ne,
						'from_setting': 1,
						'direction': direction,
						'options': options
					};
				},
				results: function(data,a)
				{
					return data
				}
			}
		});
	}
	/* ************** Functions End ************** */

	function vezife_save(btn)
	{
		btn.unbind('click').click(function()
		{
			var t = $(this),
				tr = t.parent('td').parent('tr'),
				tr_id = tr.attr("tr_id"),
				option = tr.find("input.option").select2('val'),
				option_values = tr.children('td').eq(2).children('input').val(),
				document_types = tr.children('td').eq(3).children('input').val(),
				option_text = get_select2_text(tr.find("input.option").select2('data')),
				option_values_text = get_select2_text(tr.children('td').eq(2).children('input').select2('data')),
				document_types_text = get_select2_text(tr.children('td').eq(3).children('input').select2('data')),
				pattern_prefix = tr.children('td').eq(4).find('input.pattern_prefix').val(),
				pattern = tr.children('td').eq(4).find('input.pattern').val(),
				sayi = tr.children('td').eq(0).text();

			var direction = tr.closest('table').data('direction');
			if(pattern === "" || pattern_prefix === "" || option.trim()=="" || option_values.trim()=="" || document_types.trim()=="")
			{
				tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">$9956error1$</td></tr>');
				var closestTable = tr.closest('table');
				setTimeout(function(){closestTable.find('tbody tr[error]').remove();}, 3000);
			}
			else
			{
				$.post("prodoc/ajax/msk/nomrelenme.php", {direction: direction, 'pattern': pattern,'pattern_prefix': pattern_prefix,'option_id':option,'option_values':option_values,'document_types':document_types,'id':tr_id}).done(function(netic)
				{
					var netice = $.parseJSON(netic);
					if(netice.status==="error")
					{
						swals({
							title: "Səhv var!",
							text: netice.error_msg,
							type: "warning",
							closeOnConfirm: true
						}, function () {
							if (netice.duplications && _.isArray(netice.duplications)) {
								netice.duplications.forEach(function(id) {
									var trToSelect = $('table').find('tr[tr_id="' + id + '"]');
									trToSelect.addClass('selected');

									setTimeout(function () {
										trToSelect.removeClass('selected');
									}, 3000);
								});
							}
						});
					}
					else
					{
						tr.attr("tr_id",netice.id).html("\
							<td style='width:20px;text-align:center;'>"+sayi+"</td>\
							<td data-id='"+option+"'>"+option_text+"</td>\
							<td data-id='"+option_values+"'>"+option_values_text+"</td>\
							<td data-id='"+document_types+"'>"+document_types_text+"</td>\
							<td><span class='pattern_prefix'>" + pattern_prefix + "</span><span class='pattern'>" + pattern + "</span></td>\
							<td style='text-align:center;' ><a href=\"javascript:;\" class=\"btn default btn-xs green tenzimle\"><i class=\"fa fa-cogs\"></i> Tənzimlə</a></td>\
							<td style='text-align:center;'>\
								<a href='javascript:;' class='btn default btn-xs purple'>\
									<i class='fa fa-edit'> </i> $9956edit$\
								</a>\
							</td>\
							<td style='width:100px;text-align:center;'>\
								<a href='javascript:;' class='btn default btn-xs red'>\
									<i class='fa fa-trash'> </i> $9956remove$\
								</a>\
							</td>");
						vezife_edit(tr.find(".purple"));
						vezife_delete(tr.find(".red"));
					}
				});
			}
		});
	}

	function vezife_edit(btn)
	{
		btn.unbind('click').click(function()
		{
			var tr = $(this).closest('tr');
			var th = $(this).parent('td').parent('tr');

			var html = th.html(),
				option = th.find("td:eq(1)").attr('data-id'),
				option_values = th.find("td:eq(2)").attr('data-id'),
				document_types = th.find("td:eq(3)").attr('data-id'),
				option_text = th.find("td:eq(1)").text(),
				option_values_text = th.find("td:eq(2)").text(),
				document_types_text = th.find("td:eq(3)").text(),
				pattern = tr.find('span.pattern').text(),
				pattern_prefix = tr.find('span.pattern_prefix').text(),
				sayi = th.children('td').eq(0).text();
			var kateqoriyalar = "";

			th.html("\
				<td style='width:20px;text-align:center;'>"+sayi+"</td>\
				<td>\
					<input type='text' class='form-control option' style='height: 30px;'>\
				</td>\
				<td>\
					<input type='text' class='form-control option_values' style='height: 30px;'>\
				</td>\
				<td>\
					<input type='text' class='form-control document_types' style='height: 30px;'>\
				</td>\
				<td>\
					<input type='text' class='form-control pattern_prefix' style='height:30px;'>\
					<input type='text' class='form-control pattern' style='height:30px;'>\
				</td>\
				<td style='text-align:center;'><a href=\"javascript:;\" class=\"btn default btn-xs green tenzimle\"><i class=\"fa fa-cogs\"></i> Tənzimlə</a></td>\
				<td style='text-align:center;'>\
					<a href='javascript:;' class='btn default btn-xs blue'>\
						<i class='fa fa-save'></i> $9956save$\
					</a>\
				</td>\
				<td style='width:100px;text-align:center;'>\
					<a href='javascript:;' class='btn default btn-xs yellow'>\
						<i class='fa fa-trash'></i> $9956cancel$\
					</a>\
				</td>");

			select22(th.find('.option'), 'document_number_pattern_option_list', true, th);
			selectOptionValues(th.find('.option_values'), th);
			select22(th.find('.document_types'), 'document_types', true, th);

			th.find('input.option').select2('data',set_select2_data(option, option_text));
			th.find('input.option_values').select2('data',set_select2_data(option_values, option_values_text));
			th.find('input.document_types').select2('data',set_select2_data(document_types, document_types_text));

			tr.find('input.pattern').val(pattern);
			tr.find('input.pattern_prefix').val(pattern_prefix);

			th.find('.yellow').click(function()
			{
				th.html(html);
				vezife_edit(th.find(".purple"));
				vezife_delete(th.find(".red"));
			});
			vezife_save(th.find(".blue"));
		});
	}
	$('.vezife_table tbody .purple').each(function()
	{
		vezife_edit($(this));
	});

	function vezife_delete(btn)
	{
		btn.click(function()
		{
			var idsi = $(this).parent('td').parent('tr').attr("tr_id");
			var t = $(this);
			var closestTable = t.closest('table');

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
					$.post("prodoc/ajax/msk/nomrelenme.php", {'id':idsi,'sil':"sil"}).done(function(netice)
					{
						netice = JSON.parse(netice);
						if(netice["status"]=="error") errorModal(netice["error_msg"],2000,true);
						else
						{
							t.parent('td').parent('tr').remove();
							if(!closestTable.find('tbody').children('tr').children('td').length)
							{
								closestTable.find('tbody').html("<tr time='null'><td colspan='100%'>$9956empty$</td></tr>");
							}
						}
						swals("$9956silindi$", "$9956silindi$", "success");
					});
				});
		});
	}

	$('.vezife_table tbody .red').each(function()
	{
		vezife_delete($(this));
	});

	$(function () {
		var table = $(".vezife_table");

		table.on('click', '.tenzimle', function () {
			var id = +$(this).closest('[tr_id]').attr('tr_id');
			templateYukle("tenzimle","Tənzimlə",{"id": id}, 40,true,"green");
		});

		var client = new Clipboard(".copy");
	});

    $('.brond_muddet').on('keydown', _.debounce(function() {
        var deqiqe = $(this).val();

        $.post('prodoc/ajax/msk/brond_muddeti.php',{'deqiqe':deqiqe},function()
        {
            toastr['success']('Yadda saxlandı');
        });

    }, 1000));

</script>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
