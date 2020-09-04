
<table style="width: 500px;" class="table table-striped table-bordered table-advance table-hover filterliCedvel">
	<thead>
	<tr>
		<th></th>
		<th>Aktiv</th>
		<th>Seçimlər</th>
		$theadElements$
		<th>Gün</th>
		<th>Vacib sahə</th>
	</tr>
	</thead>
	<tbody tr_tip = 'huquqi'>
		$table$
		$son_icra_table$
	</tbody>
</table>

<table style="width: 500px;" class="table table-striped table-bordered table-advance table-hover filterliCedvel">
	<thead>
	<tr>
		<th></th>
		<th>Aktiv</th>
		<th>Seçimlər</th>
		$theadElements$
		<th>Gün</th>
		<th>Vacib sahə</th>
	</tr>
	</thead>
	<tbody tr_tip="fiziki">
	$tableFiziki$
	$son_icra_table_fiziki$
	</tbody>
</table>

<table style="width: 500px;" class="table table-striped table-bordered table-advance table-hover filterliCedvel">
	<thead>
	<tr>
		<th></th>
		<th>Aktiv</th>
		<th>Seçimlər</th>
		$theadElements$
		<th>Gün</th>
		<th>Vacib sahə</th>
	</tr>
	</thead>
	<tbody tr_tip="daxili">
	$tableDaxili$
	$son_icra_table_daxili$
	</tbody>
</table>

<script type="text/javascript">

    $('.filterliCedvel tbody input[type="checkbox"]').uniform();

    $(".activ_nezaret_muddeti").on('change', function() {
        var v = $(this).is(':checked') ? 1 : 0,
        	tip = getTip($(this));

        $.post('prodoc/ajax/msk/istehsalat_teqvimi.php',{'activ':v, 'tip': tip},function()
        {
            toastr['success']('Yadda saxlandı');
        });
    });

    $(".nezaret_muddeti_vacib_sahe").on('change', function() {
        var v = $(this).is(':checked') ? 1 : 0,
            tip = getTip($(this));

        $.post('prodoc/ajax/msk/istehsalat_teqvimi.php',{'vacib_sahe':v, 'tip': tip},function()
        {
            toastr['success']('Yadda saxlandı');
        });
    });

    $(".activ_son_icra_tarixi").on('change', function() {
        var v = $(this).is(':checked') ? 1 : 0,
        	tip = getTip($(this));

        $.post('prodoc/ajax/msk/istehsalat_teqvimi.php',{'activ_son_tarix':v, 'tip': tip},function()
        {
            toastr['success']('Yadda saxlandı');
        });
    });

    $(".nezaret_muddeti").on('change', function() {
        var v = $(this).val(),
            tip = getTip($(this));


        $.post('prodoc/ajax/msk/istehsalat_teqvimi.php',{'nezaret_muddeti':v, 'tip': tip},function()
        {
            toastr['success']('Yadda saxlandı');
        });
    });

    $(".son_icra_tarixi").on('change', function() {
        var v = $(this).val(),
        	tip = getTip($(this));

        $.post('prodoc/ajax/msk/istehsalat_teqvimi.php',{'son_icra_tarixi':v, 'tip': tip},function()
        {
            toastr['success']('Yadda saxlandı');
        });
    });

    $('.nezaret_muddeti_gun').on('keydown', _.debounce(function() {
        var gun = $(this).val(),
        	tip = getTip($(this));

        $.post('prodoc/ajax/msk/istehsalat_teqvimi.php',{'nezaret_muddeti_gun':gun, 'tip': tip},function()
        {
            toastr['success']('Yadda saxlandı');
        });

	}, 1000));

    $('.son_icra_tarixi_gun').on('keydown', _.debounce(function() {
        var gun = $(this).val(),
        	tip = getTip($(this));

        $.post('prodoc/ajax/msk/istehsalat_teqvimi.php',{'son_icra_tarixi_gun':gun, 'tip': tip},function()
        {
            toastr['success']('Yadda saxlandı');
        });

    }, 1000));

    function getTip(tr) {
        var tip = tr.closest('tbody').attr('tr_tip');

        return tip;
    }

</script>