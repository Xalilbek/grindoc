<div class="modal-body form" style="padding: 0;">
	<form action="" class="form-horizontal form-bordered form-row-stripped">
		<div class="form-body">
			<div style="display: none" class="form-group yonlendirme">
				<label class="control-label col-md-4">Yönləndir:</label>
				<div class="col-md-5">
					<input class="form-control"
						   data-plugin="select2-ajax"
						   data-plugin-params='{"queryString": {"ne": "rey_muellifleri"}}'
						   vezife="rey_muellifi" placeholder="Yönləndir"
					>
				</div>
			</div>
			<div class="form-group file-upload">
				<label class="control-label col-md-4">İmtinanın səbəbi:</label>
				<div class="col-md-7">
					<textarea class="form-control" vezife="qeyd" placeholder="İmtinanın səbəbi" style="height: 70px;resize:vertical;"></textarea>
				</div>
				<div class="add-file-btn">
					<i class="fa fa-paperclip font-green-meadow" style="margin-left: 64px;"></i>
					<button type="button" class="btn btn-link font-dark" style="padding: 6px;">
						<span style="font-weight: 500;">Sənəd əlavə et</span>
					</button>
				</div>
				<div class="list-of-files" style="margin-left: 64px;">
				</div>

			</div>
		</div>
	</form>
</div>
<div class="modal-footer" style="border-top: 0;">
	<div style="float: left; color: red;" vezife="error"></div>
	<div style="float: right;">
		<button type="button" data-v="testiqle" class="btn red btn-circle">İmtina et</button>
		<button type="button" data-dismiss="modal" class="btn default btn-circle">Bağla</button>
	</div>
</div>
<script type="text/javascript" src="prodoc/settings.js"></script>

<script type="text/javascript">

	$("#bosh_modal$MN$").find('.file-upload').fileUpload({
		name: 'sened'
	});

	$(function() {
		var bm = $("#bosh_modal$MN$");

		Component.Plugin.PluginManager.init(bm);

		bm.find('[data-v="testiqle"]').unbind('click').on('click', function() {

			var fd = Component.Form.collectData({form: bm});
			var sehv_var = false;
			var yeni_rey_muelifi = +bm.find('[vezife="rey_muellifi"]').val();

			fd.append('id', '$sid$');
			fd.append('yeni_rey_muelifi', yeni_rey_muelifi);
			fd.append('sebeb', $('[vezife="qeyd"]').val());

            if (bm.find('textarea[vezife="qeyd"]').val() == "")
            {
                sehv_var = true;
                bm.find('textarea[vezife="qeyd"]').css("cssText", "border: red dashed 1px !important");
            }
            else
            {
                bm.find('textarea[vezife="qeyd"]').css("border", "");
            }

            if(!sehv_var){

				Component.Form.send({
					form: fd,
					url: 'prodoc/ajax/change_status.php?action=rey_muelifi_imtina',
					success: function () {
						$('#senedler-tbody').find('tr.selected').click();
						$('.nav li.active a').trigger('click');
						toastr["error"]('Imtina olundu');
						bm.modal('hide');
					}
				});

            }


		});
	});

</script>