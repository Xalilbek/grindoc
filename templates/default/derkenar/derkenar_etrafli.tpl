<div class="modal-body form">
	<form class="form-horizontal form-bordered form-row-stripped">
		<div class="form-body">

			<div class="form-group">
				<label class="control-label col-md-4">Kuratorlar:</label>
				<div class="col-md-6" style="line-height: 33px;">
					<?php
						Template::loopAndPrint(array_filter($elave_shexsler, function($elave_shexs) {
							return $elave_shexs['tip'] === 'kurator';
						}), 'user_ad_qisa');
					?>
				</div>
			</div>

			<input type="hidden" name="daxil_olan_sened_id" value="$sid$">

			<div class="form-group">
				<label class="control-label col-md-4">İcraçı:</label>
				<div class="col-md-6" style="line-height: 33px;">
					<?php cap($derkenar['mesul_shexs_ad']); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-4">Həm icraçı:</label>
				<div class="col-md-6" style="line-height: 33px;">
					<?php
						Template::loopAndPrint(array_filter($elave_shexsler, function($elave_shexs) {
							return $elave_shexs['tip'] === 'ishtrakchi';
						}), 'user_ad_qisa');
					?>
				</div>
			</div>
			<div class="form-group" id="nezaretdeSaxlanilsin">
				<label class="col-md-4 control-label">Nəzarətdə saxlanılsın:</label>
				<div class="col-md-2" style="line-height: 33px;">
					<?php Template::showCheckbox($derkenar['nezaretde_saxlanilsin']); ?>
				</div>
			</div>
			<div class="form-group" id="sonTarixx">
				<label class="col-md-4 control-label">Son icra tarixi:</label>
				<div class="col-md-4" style="line-height: 33px;">
					<?php print tarixeCevir($derkenar['son_icra_tarixi'], 'd M Y'); ?>
				</div>
			</div>

			<!-- derkenar metni -->
			<div class="form-group">
				<label class="control-label col-md-4"> Dərkənarın mətni:</label>
				<div class="col-md-6" style="line-height: 33px;">
					<?php print $derkenar['derkenar_metn_ad']; ?>
				</div>
			</div>

			<div class="errors_list alert alert-danger errorMsg"
				 style="display: none; width: 70%; margin: 20px auto auto;">

			</div>
		</div>
	</form>
</div>

<div class="modal-footer" style="border: 0;">
	<div style="float: left; color: red;" vezife="error"></div>
	<div style="float: right;">
		<?php if ($derkenarObject->canEdit()): ?>
			<button type="button" vezife="testiqle" class="btn green">
					<a href="index.php?module=prodoc_derkenar&derkenar_id=<?php print $id ?>" style="color:white; text-decoration: none;" >Düzəliş et</a>
			</button>
			<div class="btn-group">
			</div>
		<?php endif; ?>
		<button type="button" data-dismiss="modal" class="btn default">
			<i class="fa fa-close"></i> Bağla
		</button>
	</div>
</div>
<script type="text/javascript" src="prodoc/settings.js"></script>
<script type="text/javascript">

</script>