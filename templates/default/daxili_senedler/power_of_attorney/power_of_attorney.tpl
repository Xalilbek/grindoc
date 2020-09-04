<div id="bosh_modal$MN$">
	<div class="modal-body form" style="padding: 0;">
		<style>
			#bosh_modal$MN$ .work_year
			{
				width: 200px;
				display: inline-block;
				vertical-align: top;
			}
			#bosh_modal$MN$ .work_year_div
			{
				margin-bottom: 5px;
			}
			#bosh_modal$MN$ .work_year_day
			{
				width: 70px;
				display: inline-block;
				vertical-align: top;
			}
			#bosh_modal$MN$ .work_year_add
			{
				padding: 10px;
				cursor: pointer;
				color: #26C281;
			}
			#bosh_modal$MN$ .work_year_delete
			{
				padding: 10px;
				cursor: pointer;
				color: #e43a45;
			}
			.document_number
			{
				display: flex;
				margin-left: 39px;
			}

			input[name=document_number] {
				width: 209px;
				margin-left: -70px !important;
			}

		</style>
		<form class="form-horizontal form-bordered form-row-stripped">
			<div class="form-body">

				<div class="form-group">
					<div class="col-md-7 document_number">
						$doc_num_input_html$
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3">$2616qeydiyyat_pencereleri_shexsler$:</label>
					<div class="col-md-4">
						<input
							class="form-control select2me employe"
							placeholder="$2616rollar_kim$"
							name="from_user_id"
							data-plugin="select2-ajax"
							data-plugin-params='{"queryString": {"ne": "employee", "tip": "ishleyenler"}}'
						>
						<span class="help-block"></span>
						<span class="help-block"></span>
					</div>
					<div class="col-md-4">
						<input
							class="form-control select2me employe"
							placeholder="$2616rollar_kime$"
							name="to_user_id"
							data-plugin="select2-ajax"
							data-plugin-params='{"queryString": {"ne": "employee", "tip": "ishleyenler"}}'
						>
						<span class="help-block"></span>
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group poa-list" style="display: none;">
					<label class="control-label col-md-3">Əlavə edəcəyin şəxsin adında olan vəkalətnamələr:</label>
					<div class="col-md-9 users-list ">

					</div>
				</div>
				<hr>

				<input type="hidden" name="id" value="0">
				<input type="hidden" name="type" value="1">
				<div class="form-group">
					<div class="col-md-9 col-md-offset-3">
						<button type="button" class="btn btn-default vezife-uzre vezife-xidmeti">
							$2616qeydiyyat_pencereleri_vezife_uzre$
						</button>

						<button type="button" class="btn btn-default xidmeti-uzre vezife-xidmeti">
							$2616qeydiyyat_pencereleri_xidmeti_uzre$
						</button>
					</div>
				</div>

			<!--	<input type="hidden" name="bind_to_document" value="1"> -->
				<input type="hidden" name="related_document_menu[]" value="internal">
				<div class="form-group ts_hide">
					<label class="col-md-3 control-label">$2616qeydiyyat_pencereleri_sebeb$:</label>
					<div class="col-md-4">
						<input
							class="form-control select2me employe"
							name="related_document_type[]"
							type="text"
							data-plugin="select2-ajax"
							placeholder="$2616qeydiyyat_pencereleri_sebeb$"
						>
					</div>
				</div>

				<div class="form-group ts_hide">
					<label class="col-md-3 control-label">$2616etrafli_sened_nomre$:</label>
					<div class="col-md-4">
						<input
							class="form-control select2me employe"
							name="related_document_id[]"
							type="text"
							data-plugin="select2-ajax"
							placeholder="$2616etrafli_sened_nomre$"
						>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3">$2616qeydiyyat_pencereleri_dovr$:</label>
					<div class="col-md-5">
						<div class="input-group date-picker input-daterange" data-date-format="mm/dd/yyyy" data-plugin="datepicker">
							<input class="form-control" name="start_date" style="text-align:center;" type="text" placeholder="$2616etrafli_ne_vaxtdan$">
							<span class="input-group-addon">-</span>
							<input class="form-control" name="end_date" style="text-align:center;" type="text" placeholder="$2616etrafli_ne_vaxta$">
						</div>
						<div class="no-end-date-container" style="float: right">
							$2616qeydiyyat_pencereleri_birdefelik$
							<input type="checkbox" name="no_end_date" class="form-control" data-plugin="uniform">
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3">$2616etrafli_senedler$:</label>
					<div class="col-md-4">
						<input
							name="allowed_docs"
							class="form-control"
							data-plugin="select2-ajax"
							data-plugin-params='{"multiple": true, "queryString": {"ne": "comission_allowed_docs", "multiple": true}}'
							placeholder="$2616etrafli_senedler$"
						>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label">$2616etrafli_paralel$:</label>
					<div class="col-md-4" style="line-height: 33px;">
						<input type="checkbox" class="form-control tenzimle" name="parallelism" data-plugin="uniform">
					</div>
				</div>

				<!--<div class="form-group">
					<label class="col-md-3 control-label">Tabeliyinde olanların sənədləri:</label>
					<div class="col-md-4" style="line-height: 33px;">
						<input type="checkbox" class="form-control tenzimle" name="allowed_to_work_with_subordinate_users_docs" data-plugin="uniform">
					</div>
				</div>-->

				<div class="form-group">
					<label class="col-md-3 control-label">$2616qeydiyyat_pencereleri_sened_nov$:</label>
					<div class="col-md-4" style="line-height: 33px;">
						<select data-plugin="select2" class="disabled" style="width: 100%">
							<option>$2616qeydiyyat_pencereleri_hamisi$</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label">$2616emeliyyat$:</label>
					<div class="col-md-4" style="line-height: 33px;">
						<select data-plugin="select2" class="disabled" style="width: 100%">
							<option>$2616umumi_hesabat_hamisi$</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label">$2616qeydiyyat_pencereleri_icra_huququ$:</label>
					<div class="col-md-4" style="line-height: 33px;">
						<select data-plugin="select2" class="disabled" style="width: 100%">
							<option>$2616qeydiyyat_pencereleri_hamisi$</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3">$2616etrafli_qeyd$:</label>
					<div class="col-md-7">
						<textarea class="form-control" name="note"></textarea>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="modal-footer" style="border-top: 0;">
		<div style="float: left; color: red;" vezife="error"></div>
		<div style="float: left;">
			<button type="button" data-v="testiqle" class="btn green save">$2616icraya_gonder$</button>
			<button type="button" data-dismiss="modal" class="btn default">$47imtina$</button>
		</div>
	</div>
</div>
<script src="prodoc/modules/daxili_senedler/appendGeneralData.js"></script>
<script>
	$(function () {
		var modal = $("#bosh_modal$MN$");

		Component.Plugin.PluginManager.init(modal);



		modal.find('.save').on('click', function() {

			var type = modal.find('.vezife-xidmeti.active').hasClass('vezife-uzre') ? 1 : 2;

            var fd = new FormData();
            getFiles(fd);

			modal.find('[name=type]').val(type);
			Component.Form.send({
				url: 'prodoc/ajax/power_of_attorney/add_edit_controller.php',
				sendUncheckedCheckbox: true,
                existingFormData: fd,
				form: modal.find('form'),
				success: function (res) {
					proccessResponse(res);
				}
			});

		});

		modal.find('.vezife-xidmeti').on('click', function() {
			modal.find('[name="related_document_type[]"]').select2('val', '');
			modal.find('[name="related_document_id[]"]').select2('val', '');
		});

		modal.find('[name="related_document_type[]"]').on('change', function() {
			modal.find('[name="related_document_id[]"]').select2('val', '');
		});

		var noEndDateInput = modal.find('[name=no_end_date]');
		var endDateInput = modal.find('[name=end_date]');

		noEndDateInput.on('change', function() {
			if ($(this).is(':checked')) {
				endDateInput.val('').prop('disabled', true);
			} else {
				endDateInput.prop('disabled', false);
			}
		});

		modal.find('.vezife-uzre').on('click', function() {
			noEndDateInput.closest('div.no-end-date-container').show();
			endDateInput.prop('disabled', true);

			noEndDateInput
				.prop('checked', true)
				.trigger('change')
				.uniform('refresh');

			modal.find('.vezife-xidmeti.active').removeClass('active');
			$(this).addClass('active');
		});

		modal.find('.xidmeti-uzre').on('click', function() {
			noEndDateInput.closest('div.no-end-date-container').hide();
			endDateInput.prop('disabled', false);

			modal.find('.vezife-xidmeti.active').removeClass('active');
			$(this).addClass('active');
		});


		var inf = $inf$;
		if (!_.isNull(inf)) {
			if (+inf.type === 2) {
				modal.find('.xidmeti-uzre').trigger('click');
			} else {
				modal.find('.vezife-uzre').trigger('click');
			}

			Component.Form.setData(modal, inf);
		} else {
			modal.find('.vezife-uzre').trigger('click');
		}

		modal.find('select.disabled').each(function(i, e) {
			$(e).select2('disable');
		});

		var docNumber = modal.find('input[name="related_document_id[]"]');
		var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

		axtarish(docNumber, {
			getAjaxData: function (t) {
				var related_document_type = modal.find('[name="related_document_type[]"]').val();

				return {
					'ne': 'document_number',
					'direction': related_document_type,
					'show_all': 0
				}
			}
		});

		var relatedDocumentType = modal.find('input[name="related_document_type[]"]');
		axtarish(relatedDocumentType, {
			getAjaxData: function (t) {
				var type = modal.find('.vezife-xidmeti.active').hasClass('vezife-uzre') ? 1 : 2;

				return {
					'ne': 'document_type_menu',
					'related_document_menu': 'internal',
					'poa_type': type
				}
			}
		});
		if('$gid$'>0){
			$('[name="from_user_id"]').select2('disable');
			$('[name="to_user_id"]').select2('disable');
		}

		if('$TS$'>0){
			$('.ts_hide').hide();
		}
		
		$('[name="from_user_id"]').on('change',function () {
			var userId = $(this).val();

			$.post("prodoc/ajax/power_of_attorney/check_poa_existence.php", {  'userId' : userId}, function(response){
				response = JSON.parse(response);
				console.log(response);
				if(response.status=='ok'){

					var poaListHtml = '';

					response.poas.forEach(function (element) {
						element['end_date']= element['end_date']==null ? 'Birdəfəlik': element['end_date']
						poaListHtml += '<input type="checkbox" value="'+element['id']+'"  ' +
								'data-plugin="uniform" name="poa_ids[]"> '+element['user_name']+'('+element['start_date']+' / '+element['end_date']+') - '+element['document_number']+'<br>'
					})

					$('.users-list').html(poaListHtml);
					$('.poa-list').fadeIn(200);

				}else {
					$('.poa-list').fadeOut(200);
					$('.users-list').html('');
				}

			})
		})
	});
</script>