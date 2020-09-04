<link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="asset/global/plugins/uniform/css/uniform.default.css?v1"/>

<div class="modal-body">
	<table class="table table-striped table-bordered table-advance table-hover">
		<thead>
			<tr>
				<th>№</th>
				<th></th>
                <th><?= dsAlt('2616qeydiyyat_pencereleri_muraciet_eden', "Müraciət edən")?></th>
				<th><?= dsAlt('2616etrafli_sv_nomre', "Şəxsiyyət vəsiqəsinin nömrəsi")?></th>
				<th><?= dsAlt('2616etrafli_sv_fin',  "Şəxsiyyət vəsiqəsinin fin kodu") ?></th>
				<th><?= dsAlt('2616nomre_sutun',  "Nömrə") ?></th>
				<th><?= dsAlt('2616umumi_hesabat_tarix',  "Tarix") ?></th>
				<th><?= dsAlt('2616status_dos',  "Status") ?></th>
				<th><?= dsAlt('2616qeydiyyat_pencereleri_tip',  "Sənədin tipi") ?></th>
				<th><?= dsAlt('2616qeydiyyat_penceleri_nezaret',  "Nəzarət") ?></th>
				<th><?= dsAlt('2616icrachi_dos',  "İcraçı") ?></th>
			</tr>
		</thead>
		<tbody>
        	<?php $i = 0; foreach ($docs as $murEdenId => $docOuter): $i++; ?>
				<tr style="background-color: #e2e2e2 !important;">
					<td><?php print $i; ?></td>
					<td><input value="<?=$murEdenId?>" type="checkbox" data-plugin="uniform" class="muraciet_eden"></td>
					<td colspan="1000">
						<?= htmlspecialchars($docOuter[0]['muraciet_eden_ad']) ?>
						(<?= is_null($docOuter[0]['id']) ? 0 : count($docOuter); ?>)
					</td>
				</tr>
				<?php if (!is_null($docOuter[0]['id'])): ?>
					<?php foreach ($docOuter as $doc): ?>
						<tr>
							<td></td>
							<td style="width: 15px;"><?php //print $i; ?></td>
							<td><?php //print htmlspecialchars($doc['muraciet_eden_ad']); ?></td>
							<td><?php print htmlspecialchars($doc['shexsiyyet_vesiqesi_seria']); ?></td>
							<td><?php print htmlspecialchars($doc['shexsiyyet_vesiqesi_pin_kod']); ?></td>
							<td><?php print htmlspecialchars($doc['document_number']); ?></td>
							<td><?php print tarixCapEt($doc['senedin_daxil_olma_tarixi'], 'd/m/Y'); ?></td>
							<td><?php print htmlspecialchars($doc['status']); ?></td>
							<td><?php print htmlspecialchars($doc['mektubun_tipi_ad']); ?></td>
							<td><?php //print htmlspecialchars($doc['nezaret']); ?></td>
							<td><?php print htmlspecialchars($doc['icrachi']); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
            <?php endforeach; ?>
		</tbody>
	</table>
</div>

<div class="modal-footer" style="border: 0;">
    <div style="float: right;">
        <button type="button" data-dismiss="modal" class="btn btn-circle green-meadow save">
			<i class="fa fa-plus"></i> <?= dsAlt('2616yeni',  "Yeni") ?>
        </button>
    </div>
</div>

<script type="text/javascript">

	$(function() {
		var bm = $("#bosh_modal<?=$MN?>");

		Component.Plugin.PluginManager.init(bm);

		bm.find("input:checkbox").on('click', function() {
			var $box = $(this);
			if ($box.is(":checked")) {
				var group = "input.muraciet_eden";
				$(group).prop("checked", false).trigger('change');
				$box.prop("checked", true).trigger('change');
			} else {
				$box.prop("checked", false).trigger('change');
			}

			if (!$box.is(':checked')) {
				bm.find('.save').html('<i class="fa fa-plus"></i> Yeni');
			} else {
				bm.find('.save').html('<i class="fa fa-check"></i> Eyni');
			}

			$.uniform.update($(group));
		});

		bm.find('.save').on('click', function () {
			var checkedME = bm.find('.muraciet_eden:checked');

			if (checkedME.length > 0) {
				$('[name=muraciet_eden_id]').val(+checkedME.val());
			} else {
				$('[name=muraciet_eden_id]').val(-1);
			}
		});
	});

</script>