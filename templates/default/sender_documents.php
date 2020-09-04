<link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="asset/global/plugins/uniform/css/uniform.default.css?v1"/>

<div class="modal-body">
	<table class="table table-striped table-bordered table-advance table-hover">
		<thead>
			<tr>
				<th>№</th>
				<th>Müraciət edən</th>
				<th>Nömrə</th>
				<th>Tarix</th>
				<th>Status</th>
				<th>Sənədin tipi</th>
				<th>İcra müddəti</th>
				<th>İcraçı</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0; foreach ($docs as $doc): $i++; ?>
				<tr>
					<td><?php print $i; ?></td>
					<td><?php print htmlspecialchars($doc['muraciet_eden_ad']); ?></td>
					<td><?php print htmlspecialchars($doc['document_number']); ?></td>
					<td><?php print tarixCapEt($doc['senedin_daxil_olma_tarixi'], 'd/m/Y'); ?></td>
					<td><?php print htmlspecialchars($doc['status']); ?></td>
					<td><?php print htmlspecialchars($doc['mektubun_tipi_ad']); ?></td>
					<td><?php print htmlspecialchars($doc['nezaret']); ?></td>
					<td><?php print htmlspecialchars($doc['icrachi']); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<div class="modal-footer" style="border: 0;">
    <div style="float: right;">
        <button type="button" data-dismiss="modal" class="btn default btn-circle">
            Bağla
        </button>
    </div>
</div>

<script type="text/javascript">

	$(function() {

	});

</script>