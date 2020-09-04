<div class="modal-body form" style="padding: 0;">
	<form>
		<input type="hidden" name="daxil_olan_sened_id" value="$daxil_olan_sened_id$">
		<input type="hidden" name="derkenar_id" value="$derkenar_id$">

		<div id="emeliyyat_body" style="padding: 8px;">
			<div class="ishe-tikilsin">
				<div class="row">
					<div class="col-md-6">
						<?php if ($netice_selectinin_goster) : ?>
						<div class="form-group">
							<label>Daxil olan sənədin nəticəsi:</label>
							<input class="form-control"
								   data-plugin="select2-ajax"
								   data-plugin-params='{"queryString": {"ne": "prodoc_neticeler"}}'
								   name="netice" placeholder="Nəticə"
							>
						</div>
						<?php endif; ?>
						<div class="form-group">
							<label>Qeyd:</label>
							<textarea class="form-control" name="qeyd" placeholder="Qeyd" style="height: 70px;resize:vertical;"></textarea>
						</div>
					</div>
				</div>

				<div style="float: left; color: red;" vezife="error"></div>
			</div>
		</div>
	</form>
</div>
<div class="modal-footer" style="border-top: 0; display: none">
	<div class="modal-footer" style="text-align: left">
		<button type="button" class="btn btn-circle green-meadow save">
			<i class="fa fa-check"></i> <span class="save-btn-text">İşə tik</span>
		</button>
		<button type="button" data-dismiss="modal" class="btn btn-circle default">
			İmtina et
		</button>
	</div>
</div>
<script type="text/javascript" src="prodoc/settings.js"></script>

<script type="text/javascript">
	$(function() {
		var bm = $("#bosh_modal$MN$");
		Component.Plugin.PluginManager.init(modal);
		var modal = bm;
		var isheTikilsin = bm.find('.ishe-tikilsin');

		modal.find('.modal-footer .save').unbind('click').on('click', function() {

			var netice = $("[name=\"netice\"]");
			var qeyd = $("[name=\"qeyd\"]");

			if (netice.length > 0 && +netice.val() === 0) {
				isheTikilsin.find("[name=\"netice\"]").prev("div").attr("style","border:1px dashed red !important");
				return;
			} else {
				isheTikilsin.find("[name=\"netice\"]").prev("div").attr("style","");
			}

			if (qeyd.val().length == 0) {
				isheTikilsin.find("[name=\"qeyd\"]").attr("style","border:1px dashed red !important");
				return;
			} else {
				isheTikilsin.find("[name=\"qeyd\"]").attr("style","")
			}

			Component.Form.send({
				form: isheTikilsin.closest('form'),
				url: 'prodoc/ajax/emeliyyatlar/ishe_tikilsin.php',
				success: function() {
					debugger;
					toastr["success"]('Sənəd işə tikildi!');
					isheTikilsin.closest('.modal').modal('hide');
					refreshActiveDocument();
				}
			});
		});
	});
</script>