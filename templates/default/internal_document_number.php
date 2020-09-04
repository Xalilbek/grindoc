<?php ob_start() ?>
<?php if (!$withoutLabel): ?>
<label class="col-md-12"><?= dsAlt('2616etrafli_sened_nomre', "Sənədin nömrəsi"); ?>:</label>
<?php endif; ?>
<div class="col-md-12">
    <?php
    	$number = FALSE;
		if ((int)$documentId) {
			require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
			$tableName = InternalDocument::getTableNameByType($documentType);
			$documentId = (int)$documentId;

			$sql = "
						SELECT document_number
						FROM tb_prodoc_document_number
						WHERE id = (
							SELECT document_number_id
							FROM tb_daxil_olan_senedler
							WHERE id = (
								SELECT document_id
								FROM $tableName
								WHERE id = $documentId
							)
						)
					";
			$number = DB::fetchColumn($sql);
		}
    ?>

    <?php if (FALSE !== $number): ?>
    	<h4><?= $number; ?></h4>
	<?php else: ?>
		<span class="editable-with-select-container" style="display: none;">
        <?= dsAlt('2616qeydiyyat_pencereleri_arxa_tarix', "Arxa tarixlə yaz") ?>
        <input type="checkbox" name="editable_with_select" data-plugin="uniform">
		</span>
			<div class="document-number-list-and-letter-container"  style="display: none">
				<input style="width: 72%" name="document_number_id"
					   type="text" class="form-control" data-plugin="select2-ajax"
					   data-plugin-params='{"queryString": {"ne": "document_number", "direction": "internal", "type": "<?php echo $documentType ?>"}}'
					   placeholder="Sənədin daxil olma №-si">
				<select style="width: 25%" name="document_number_letter" data-plugin="select2">
					<?php foreach (range('A', 'Z') as $letter): ?>
						<option value="<?=$letter?>"><?=$letter?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="document-number-container">
				<div class="input-icon right">
					<i class="document-number-loading-icon fa fa-spinner fa-spin font-custom"></i>
					<input name="document_number"
						   type="text"
						   class="form-control"
						   readonly="readonly"
						   placeholder="Sənədin daxil olma №-si">
				</div>
			</div>
	<?php endif; ?>
</div>
<?php if (FALSE === $number): ?>
	<script type="text/javascript" src="prodoc/asset/js/underscore_mixin.js"></script>
	<script src="prodoc/asset/widget/hideShow.js"></script>
	<script>
		$(function () {
			function getInternalDocumentNumber(container, extraId)
			{
				Component.Plugin.Plugin.select2AjaxPlugin.settings.url = "prodoc/includes/plugins/axtarish.php";
				Component.Plugin.PluginManager.init(container);

				var documentNumberInput = container.find('[name="document_number"]');
				var documentMumberLoadingIcon = container.find('.document-number-loading-icon');
				var editableWithSelectContainer = container.find('.editable-with-select-container');

				var data = {};
				data.direction = 'internal';
				data.extraId = extraId;

				documentMumberLoadingIcon.fadeIn();
				$.post('prodoc/ajax/get_document_number.php', data, function(docNumber) {
					if ('error' === docNumber.status) {
						toastr["error"](docNumber.error_msg);
						return;
					}

					if (Boolean(+docNumber.setting.editable_with_select)) {
						editableWithSelectContainer.slideDown();
					} else {
						editableWithSelectContainer.slideUp();
						container.find(".document-number-list-and-letter-container").hide();
						container.find(".document-number-container").show();
					}

					if (0 === +docNumber.setting.editable) {
						documentNumberInput.val(docNumber.number);
					}

					documentNumberInput.prop('readonly', !Boolean(+docNumber.setting.editable));
					documentMumberLoadingIcon.fadeOut();
				}, 'json');
			}

			var container = $("#bosh_modal<?php echo $MN; ?>");

			getInternalDocumentNumber(container, '<?php echo $documentType ?>');
			container.find('[name="editable_with_select"]').hideShow({
				showWhenChecked: container.find(".document-number-list-and-letter-container"),
				hideWhenChecked: container.find(".document-number-container"),
				onHide: function () {
					getInternalDocumentNumber(container, '<?php echo $documentType ?>');
				},
				onShow: function () {
					container.find('[name="document_number_id"]').trigger('change');
				}
			});
			container.on('change', "[name=document_number_id], [name=document_number_letter]", function() {
				var closestFormGroup = $(this).closest('div.form-group');
				var documentNumberText   = _.getIfNotNull(closestFormGroup.find('[name=document_number_id]').select2('data'), 'text');
				var documentNumberLetter = _.getIfNotNull(closestFormGroup.find('[name=document_number_letter]').select2('data'), 'text');

				closestFormGroup.find('[name="document_number"]').val(documentNumberText + ' / ' + documentNumberLetter);
			});
		});
	</script>
<?php endif; ?>
<script>
	var documentNumber = {};
	documentNumber.appendDataToForm = function (formData) {
		var container = $("#bosh_modal<?php echo $MN; ?>");
		formData.append("editable_with_select",+container.find('[name=editable_with_select]').is(':checked'));
		formData.append("document_number",container.find('[name=document_number]').val());
	};
</script>
<?php $content = ob_get_contents(); ob_end_clean(); return $content; ?>
