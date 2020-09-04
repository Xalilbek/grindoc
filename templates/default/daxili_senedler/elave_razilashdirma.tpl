<style>
	.accordion-block{
		padding-bottom:15px;
	}
	textarea{
		resize: vertical;
	}
	.col-md-6,textarea{
		margin:5px 0;
	}

	#tapsirig_table thead th{
		vertical-align: middle;
		text-align: center;
	}

</style>

<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="assets/css/balloon.css"/>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
<script src="prodoc/asset/js/underscore_mixin.js"></script>
<script src="prodoc/asset/widget/multiple.js"></script>
<script type="text/javascript" src="prodoc/asset/widget/hideShow.js"></script>
<script src="assets/scripts/tippy.all.min.js"></script>


<div id="bosh_modal$MN$">

	<div class="modal-body form" style="padding: 0;" id="tapsirig_emri">
		<form action="">
			<input type="hidden" name="id" value="0">
			<div class="whiteboard">
				<div class="col-md-12">
					<div class="blockname">
						<h3 class="text-success text-left">
							<strong>Sənədin nömrəsi</strong>
						</h3>
						<span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
					</div>
				</div>
				<div class="accordion-block">
					<div class="row">
						<div class="col-md-6 hidden">
							<div class="form-group">
								<label for="" class="col-md-12">
									Sənəd tipi:
								</label>
								<div class="col-md-12">
									<select data-plugin="select2" class="form-control" name="sened_tip">
										<option value="1">Məlumat üçün</option>
										<option value="2">İcra üçün</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								$doc_num_input_html$
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-md-6">
									Sənədin tarixi:
								</label>
								<div class="col-md-12">
									<input name="senedin_tarixi" value="$current_date$" type="text" class="sened_tarix form-control" placeholder="Sənədin tarixi">
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="whiteboard">
				<div class="col-md-12">
					<div class="blockname">
						<h3 class="text-success text-left">
							<strong>Razılaşdırma</strong>
						</h3>
						<span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
					</div>
				</div>
				<div class="accordion-block">

					<div class="form-group">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Əməkdaş</label>
								<div class="col-md-12">
									<input class="form-control" name="emekdash"
										   data-plugin-params='{"queryString": {"ne": "employee", "tip": "ishleyenler"}}'
										   data-plugin="select2-ajax"
										   placeholder="Əməkdaş">
								</div>
							</div>
						</div>
						<script>
							$(function () {
								$('#bosh_modal$MN$ [name="emekdash"]').on('change', function() {
									if (!$(this).select2("data"))
										return;

									$("#bosh_modal$MN$ .emekdash_vezife").val("" + ($(this).select2("data").posName || "-"));
								});
							});
						</script>
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Vəzifə</label>
								<div class="col-md-12">
									<input class="form-control emekdash_vezife" disabled placeholder="Vəzifə">
								</div>
							</div>
						</div>
					</div>











					<div class="form-group">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Müqavilə:</label>
								<div class="col-md-12">
									<input class="form-control" name="muqavile" placeholder="Müqavilə">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Əlavə Razılaşdırma №-si:</label>
								<div class="col-md-12">
									<input class="form-control" placeholder="Əlavə Razılaşdırma №-si" name="elave_razilashdirnama_nomresi">
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Əməkhaqqına əlavə:</label>
								<div class="col-md-12">
									<input type="number" class="form-control" name="emeqhaqqina_elave" placeholder="Əməkhaqqına əlavə">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Valyuta:</label>
								<div class="col-md-12">
									<select data-plugin="select2" class="form-control valyuta" placeholder="Valyuta" vezife="valyuta" name="emeqhaqqina_elave_valyuta">
										$valyutalar$
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Ezamiyyət müddəti:</label>
								<div class="col-md-12">
									<input class="form-control" name="ezamiyyet_muddeti" placeholder="Ezamiyyət müddəti">
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Şəxsiyyət vəsiqəsinin pin kodu:</label>
								<div class="col-md-12">
									<input class="form-control" name="sv_pin_kodu" placeholder="Şəxsiyyət vəsiqəsinin pin kodu">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Şəxsiyyət vəsiqəsinin nömrəsi:</label>
								<div class="col-md-12">
									<input class="form-control" name="sv_nomresi" placeholder="Şəxsiyyət vəsiqəsinin nömrəsi">
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Vəsiqəni təqdim edən orqan:</label>
								<div class="col-md-12">
									<input class="form-control" name="sv_teqdim_eden_orqan" placeholder="Vəsiqəni təqdim edən orqan">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Ünvan:</label>
								<div class="col-md-12">
									<input class="form-control" name="unvan" placeholder="Ünvan">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="whiteboard">
				<div class="col-md-12">
					<div class="blockname">
						<h3 class="text-success text-left">
							<strong>Sənədin məzmunu</strong>
						</h3>
						<span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
					</div>
				</div>
				<div class="accordion-block">

					<div class="form-group">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-12">Məktubun qısa məzmunu</label>
								<div class="col-md-12">
									<input class="form-control" name="qisa_mezmun"
										   data-plugin-params='{"queryString": {"ne": "qisa_mezmun"}}'
										   data-plugin="select2-ajax"
										   placeholder="Məktubun qısa məzmunu">
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<label class="col-md-12">Qeyd</label>
								<div class="col-md-12">
                                    <textarea class="form-control" rows="3" placeholder="Qeyd"
											  name="qeyd" maxlength="1000"></textarea>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</form>
	</div>

	<div class="modal-footer" style="border-top: 0;">
		<div style="float: left; color: red;" vezife="error"></div>
		<div style="float: left;">
			<button type="button" data-v="testiqle" class="btn green save btn-circle">İcraya göndər</button>
		</div>
	</div>
</div>
<script src="prodoc/modules/daxili_senedler/appendGeneralData.js"></script>

<script>

	$(function () {

		$("#bosh_modal$MN$ .sened_tarix").datepicker({
			autoclose: true,
			format: "dd-mm-yyyy"
		});

		var modal = $("#bosh_modal$MN$");

		Component.Plugin.PluginManager.init(modal);

		modal.find('.save').on('click', function() {

            var fd = new FormData();
            getFiles(fd);

			Component.Form.send({
				url: 'prodoc/ajax/elave_razilashdirma/add_edit_controller.php',
				sendUncheckedCheckbox: true,
                existingFormData: fd,
				form: modal,
				success: function (res) {
					proccessResponse(res);
				}
			});

		});

		var inf = $inf$;
		if (!_.isNull(inf)) {
			Component.Form.setData(modal, inf);
		}
	})


</script>