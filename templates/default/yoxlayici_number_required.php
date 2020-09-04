<style>
	.probtn{
		background: #1C8F5F !important;
		border-color: #1C8F5F !important;
	}
</style>

<div class="modal-body form" style="padding: 0;">
	<form class="form-horizontal form-bordered form-row-stripped">
		<div class="form-body">
			<div class="form-group editable-with-select-container" style="display:none;">
				<div class="col-md-6">
					<div class="row">
						<label class="col-md-12">Arxa tarixlə yaz:</label>
						<div class="col-md-12">
							<input type="checkbox" data-plugin="uniform" name="editable_with_select">
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<div class="row">
						<label class=" col-md-12">Sənədin №-si:</label>
						<div class="col-md-12 document-number-list-and-letter-container"
							 style="display: none">
							<input style="width: 72%" name="document_number_id"
								   type="text" class="form-control" data-plugin="select2-ajax"
								   data-plugin-params='{"queryString": {"ne": "document_number", "direction": "incoming"}}'
								   placeholder="Sənədin daxil olma №-si">
							<select style="width: 25%" name="document_number_letter" data-plugin="select2">
                                <?php foreach (range('A', 'Z') as $letter): ?>
									<option value="<?= $letter ?>"><?= $letter ?></option>
                                <?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-12 document-number-container">
							<div class="input-icon right">
								<i class="document-number-loading-icon fa fa-spinner fa-spin font-custom"></i>
								<input name="document_number"
									   type="text"
									   class="form-control"
									   readonly="readonly"
									   placeholder="Sənədin №-si">
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" value="<?= $sid ?>" name="id">

			</div>
			<div class="form-group errors-list-container" style="display: none;">
				<div class="col-xs-12">
					<div class="alert alert-danger" style="width:auto; margin: 15px auto;">
						<strong>Səhv var:</strong>
						<div class="errors-list">

						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal-footer" style="border-top: 0;">
	<div style="float: left; color: red;" vezife="error"></div>
	<div style="float: right;">
		<button type="button" data-v="testiqle" class="btn red save">Yadda saxla</button>
		<button type="button" data-dismiss="modal" class="btn default">Bağla</button>
	</div>
</div>

<script type="text/javascript" src="prodoc/asset/js/underscore_mixin.js"></script>
<script type="text/javascript" src="prodoc/asset/widget/hideShow.js"></script>
<script src="prodoc/asset/widget/multiple.js"></script>
<script src="prodoc/asset/widget/fileUpload.js"></script>
<script type="text/javascript" src="prodoc/settings.js"></script>

<script>
	$(function () {
		var bm = $("#bosh_modal<?=$MN?>");
		var fiziki_qeydiyyat = bm;

		var documentMumberLoadingIcon = fiziki_qeydiyyat.find('.document-number-loading-icon');
		var documentNumberInput = fiziki_qeydiyyat.find('[name=document_number]');
		var editableWithSelectContainer = fiziki_qeydiyyat.find('.editable-with-select-container');

		var getDocumentNumber = _.debounce(function () {
			documentMumberLoadingIcon.fadeIn();
			$.post('<?=$docNumURL?>', {id: <?= $sid ?>}, function (docNumber) {
				var documentNumber = docNumber.number;

				documentNumberInput.val(documentNumber);

				if (Boolean(+docNumber.setting.editable_with_select)) {
					editableWithSelectContainer.slideDown();
				} else {
					editableWithSelectContainer.slideUp();
					fiziki_qeydiyyat.find(".document-number-list-and-letter-container").hide();
					fiziki_qeydiyyat.find(".document-number-container").show();
				}

				documentNumberInput.prop('readonly', !Boolean(+docNumber.setting.editable));
				documentMumberLoadingIcon.fadeOut();
			}, 'json');
		}, 500);

		getDocumentNumber();

		fiziki_qeydiyyat.find('[name=editable_with_select]').hideShow({
			showWhenChecked: fiziki_qeydiyyat.find(".document-number-list-and-letter-container"),
			hideWhenChecked: fiziki_qeydiyyat.find(".document-number-container"),
			onHide: function () {
				getDocumentNumber();
			},
			onShow: function () {
				fiziki_qeydiyyat.find("[name=document_number_id]").trigger('change');
			}
		});

		$("[name=document_number_id], [name=document_number_letter]").on('change', function () {
			var documentNumberText = _.getIfNotNull(fiziki_qeydiyyat.find('[name=document_number_id]').select2('data'), 'text');
			var documentNumberLetter = _.getIfNotNull(fiziki_qeydiyyat.find('[name=document_number_letter]').select2('data'), 'text');

			documentNumberInput.val(documentNumberText + ' / ' + documentNumberLetter);
		});

		bm.on('click', '.save', function() {
			Component.Form.send({
				url: '<?=$formURL?>',
				form: bm,
				success: function (res) {
					debugger
					var dn = bm.find('[name=document_number]').val();
					$("#senedler-tbody")
						.find('tr.selected td.document_number')
						.text(dn);

					proccessResponse(res);
				}
			});
		});

		Component.Plugin.PluginManager.init(bm);

		var proccessResponse;
		var errorsListContainer = $('.errors-list-container');
		var errorsList = $('.errors-list');
		proccessResponse = function (res) {
			res = JSON.parse(res);
			if (res.status === "error") {
				errorsList.html('');
				if (res.error_msg) {
					res.errors = [res.error_msg];
				}

				res.errors.forEach(function (error) {
					errorsList.append('<span>' + error + '</span><br>')
				});
				$('.scroll-to-top').trigger('click');
				errorsListContainer.slideDown();
				toastr.error('Səhv var!');
			} else {
				bm.modal('hide');
			}
		};

	});
</script>
