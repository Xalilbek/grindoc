<div class="modal-body form">
	<form class="form-horizontal form-bordered form-row-stripped">
		<div class="form-body">
			<span derkenar_input_append style="$kurasiyaStyle$">
				<div not_this class="form-group" style="border: 0;">
					<label class="control-label col-md-4">Kurasiyaya göndər:</label>
					<div class="col-md-6">
					<p style="margin: 7px 0 20px;">Kuratorları seçin: </p>
					<input name="kuratorlar[]" class="form-control kurator select" data-plugin="select2-ajax"

						   style="float:left;width:85%;" placeholder="Kurator...">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4"></div>
					<div class="col-md-6" style="padding-top: 0;">
						<span>
							<a class="add_derkenar_viza_input"
								 href="javascript:;">
								<i class="fa fa-plus-circle"></i> Əlavə et
							</a>
						</span>
					</div>
				</div>
			</span>

			<input type="hidden" name="daxil_olan_sened_id" value="$daxil_olan_sened_id$">
			<input type="hidden" name="derkenar_id" value="$derkenar_id$">
			<input type="hidden" name="parentTaskId" value="$parentTaskId$">
			<input type="hidden" name="specifiesResult" value="">

			<div class="form-group">
				<span derkenar_mesul_shexsler_append>
					<div not_this class="form-group" style="border: 0;">
						<label class="control-label col-md-4">Məsul şəxslər:</label>
						<div class="col-md-6">
							<input name="mesul_shexsler[]" class="form-control mesul_shexsler select"
							   style="float:left;width:85%;" placeholder="Məsul şəxslər...">
						</div>
						<div class="col-md-1 first_mesul_shexs_checkbox" style="display: none; border-left: 0px;margin-top: 7px;">
							<input type="checkbox" data-plugin="uniform">
						</div>
					</div>
					<div class="form-group add_mesul_shexsler_input_container">
						<div class="col-md-4"></div>
						<div class="col-md-6" style="padding-top: 0;">
							<span>
								<a class="add_mesul_shexsler_input"
								   href="javascript:;">
									<i class="fa fa-plus-circle"></i> Əlavə et
								</a>
							</span>
						</div>
					</div>
				</span>
			</div>

			<div class="form-group">
				<label class="control-label col-md-4">Həm icraçı:</label>
				<div class="col-md-6" style="line-height: 33px;">
					<input name="ishtrakchi_shexsler[]" class="form-control select"
						   placeholder="Həm icraçı...">
				</div>
			</div>
			<div class="form-group" id="nezaretdeSaxlanilsin">
				<label class="col-md-4 control-label">Nəzarətdə saxlanılsın:</label>
				<div class="col-md-2" style="line-height: 33px;">
					<input data-plugin="uniform" type="checkbox" name="nezaretde_saxlanilsin">
				</div>
			</div>
			<div class="form-group" id="sonTarixx">
				<label class="col-md-4 control-label">Son icra tarixi:</label>
				<div class="col-md-2" style="line-height: 33px;">
					<input data-plugin="uniform" type="checkbox" name="son_icra_tarixi_var"
						   onchange="">
				</div>
				<div class="col-md-4" style="display:none;">
					<input placeholder="Son icra tarixi"
						   class="form-control date-picker" style="text-align: center;" name="son_icra_tarixi">
				</div>
				<div class="col-md-2" style="display:none;">
					<input class="form-control" placeholder="gün" style="width: 50px;" name="son_tarix_gun">
				</div>
			</div>

			<!-- derkenar metni -->
			<div class="form-group">
				<label class="control-label col-md-4"> Dərkənarın mətni:</label>
				<div class="col-md-6" style="line-height: 33px;">
					<input name="derkenar_metn_id" id="derkenar_metn_slct1" class="form-control derkenar_metn_select" data-plugin="select2-ajax"
						   data-plugin-params='{"queryString": {"ne": "derkenar_metnler"}}'
						   placeholder="Dərkənarın mətni...">

					<textarea class="form-control" id="metn_diger" name="derkenar_metn"
							  placeholder="Mətni buraya daxil edə bilərsiniz"
							  style="display: none;"></textarea>
					<label><input class="diger" data-plugin="uniform" id="derkenar_metn_diger_chck3" type="checkbox">
						Digər</label>
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
		<button type="button" vezife="testiqle" class="btn green">
			<i class="fa fa-check"></i> Dərkənar yaz
		</button>
		<button type="button" data-dismiss="modal" class="btn default">
			<i class="fa fa-close"></i> İmtina
		</button>
	</div>
</div>
<script type="text/javascript" src="prodoc/settings.js"></script>
<script type="text/javascript">

	$(function() {

		var bm = $("#bosh_modal$MN$");
		if ('$mesulShexsMultiple$' === 'false') {
			bm.find('.add_mesul_shexsler_input_container').remove();
            bm.find('[name="mesul_shexsler[]"]').attr('name', 'mesul_shexsler');
		}

		if ('$showCheckboxes$' === '0') {
			bm.find('.first_mesul_shexs_checkbox').remove();
		}

		function addDerkenarVizaInput(x) {

			var html = '';
			html += '<div class="form-group" style="border: 0;">';
			html += '<label class="control-label col-md-4" style="padding-top:0px;"></label>';
			html += '<div class="col-md-6" style="padding-top:0px;">';

			if (x === 'span[derkenar_input_append]')
				html += '<input name="kuratorlar[]" class="form-control kurator select" data-plugin="select2-ajax" data-plugin-params=\'{"queryString": {"ne": "emekdash"}}\' style="float:left;width:85%;" placeholder="Kurator...">';
			else
				html += '<input name="mesul_shexsler[]" class="form-control mesul_shexsler select" data-plugin="select2-ajax" data-plugin-params=\'{"queryString": {"ne": "emekdash"}}\' style="float:left;width:85%;" placeholder="Məsul şəxslər...">';

			html += '<div style="float: left; width: 15%; padding-left: 11px;" class="btn btn-danger"><i class="fa fa-trash"></i></div>';
			html += '</div>';
			if ('$showCheckboxes$' === '1') {
				html += '<div class="col-md-1" style="border-left: 0px;margin-top: -7px;">' +
							'<input type="checkbox" data-plugin="uniform">' +
						'</div>';

				bm.find('.first_mesul_shexs_checkbox').show();
			}


			html += '</div>';

			var cHTML = $(html);
			$(x).children('div:nth-last-child(2)').after(cHTML);
			Component.Plugin.PluginManager.init(cHTML);

		}

		Component.Plugin.PluginManager.init(bm);

		bm.find("input[name='son_icra_tarixi']").change(function () {
			var tarix = $(this).val().replace(/([0-9]+)\-([0-9]+)\-([0-9]+)/, "$2/$1/$3");
			var ferq = Math.floor((new Date(tarix) - new Date()) / 1000 / 60 / 60 / 24);
			bm.find("input[name='son_tarix_gun']").val(ferq);
		});

		bm.find("input[name='son_tarix_gun']").on("change keyup", function () {
			var t = new Date(new Date().getTime() + parseInt($(this).val()) * 60 * 60 * 24 * 1000);
			bm.find("input[name='son_icra_tarixi']").val(sifirSal(t.getDate()) + "-" + sifirSal(parseInt(t.getMonth()) + 1) + "-" + t.getFullYear());
		});

		bm.find('.add_derkenar_viza_input').on('click', function() {
			addDerkenarVizaInput('span[derkenar_input_append]');
		});

		bm.find('.add_mesul_shexsler_input').on('click', function() {
			addDerkenarVizaInput('span[derkenar_mesul_shexsler_append]');
		});

		bm.on('click', '.btn.btn-danger', function() {
			$(this).parents('.form-group').eq(0).remove();
			var l = bm.find('[derkenar_mesul_shexsler_append] .form-group').length;

			if (l < 3) {
				bm.find('.first_mesul_shexs_checkbox').hide();
			}
		});

		bm.find('[derkenar_mesul_shexsler_append]').on('click', 'input[type=checkbox]', function () {
			bm.find('[derkenar_mesul_shexsler_append]').find('input[type=checkbox]:checked').not($(this)).attr('checked', false);
			$.uniform.update();
		});

		bm.find('[name=son_icra_tarixi_var]').on('change', function() {
			if ($(this).is(':checked')) {
				$(this).parents('.col-md-2').eq(0).next('div').show().next('div').show();
			} else {
				$(this).parents('.col-md-2').eq(0).next('div').hide().next('div').hide();
			}
		});

		bm.find('[vezife="testiqle"]').on('click', function() {

			if (!bm.find('.diger').is(":checked")) {
				var derkenar_metn_select = bm.find('.derkenar_metn_select');
				if (!_.isNull(derkenar_metn_select.select2('data'))) {
					bm.find('[name="derkenar_metn"]').val(derkenar_metn_select.select2('data').text);
				} else {
					bm.find('[name="derkenar_metn"]').val('');
				}
			}

			var specifiesResult = +bm
				.find('[derkenar_mesul_shexsler_append] [type=checkbox]:checked')
				.closest('.input-group')
				.find('[name="mesul_shexsler[]"]')
				.select2('val')
			;

			bm.find('[name="specifiesResult"]').val(specifiesResult);

			Component.Form.send({
				form: bm,
				url: 'prodoc/ajax/derkenar/yarat.php',
				success: function (res) {
					res = JSON.parse(res);
					if (res.status === "error") {
						Component.Form.showErrors(bm, res.errors);
					} else {
//						bm.find(".errors_list").slideUp();
//						toastr.success('Dərkənar uğurla yazıldı!');
//						refreshActiveDocument();
//						bm.modal('hide');
					}
				}
			});

		});

		$("#derkenar_metn_diger_chck3").on('change', function () {
			if ($(this).is(':checked')) {
				$('#metn_diger').slideDown(150);
				$('#derkenar_metn_slct1').prev('div').hide();
			} else {
				$('#metn_diger').slideUp(150);
				$('#derkenar_metn_slct1').prev('div').show();
			}
		});
        function collectValues(){
            var arr= [];

            $('.select').each(function () {
                if($(this).val()!=''){

                    arr.push($(this).val());
                    console.log($(this).val());
                }

            });
            return arr;
        }
		$('.date-picker').datepicker({
			autoclose: true,
			format: "dd-mm-yyyy"
		});
        var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);
        axtarish($('[name="kuratorlar[]"]'), {
            getAjaxData: function () {
                return {
                    'ne': 'emekdash',
                    'extra_emekdash':collectValues()
                }
            }
        });
        axtarish($('[name="mesul_shexsler[]"]'), {
            getAjaxData: function () {
                return {
                    'ne': 'emekdash',
                    'extra_emekdash':collectValues()
                }
            }
        });
		axtarish($('[name="mesul_shexsler"]'), {
			getAjaxData: function () {
				return {
					'ne': 'emekdash',
					'extra_emekdash':collectValues()
				}
			}
		});
        axtarish($('[name="ishtrakchi_shexsler[]"]'), {
            getAjaxData: function () {
                return {
                    'ne': 'emekdash',
                    'extra_emekdash':collectValues()
                }
            }
        });

		var derkenarInf = $derkenarInf$;


		if (!_.isNull(derkenarInf)) {
			derkenarInf.son_icra_tarixi_var = !_.isEmpty(derkenarInf.son_icra_tarixi);

			derkenarInf["kuratorlar[]"].forEach(function(kurator, index) {
				if (index > 0) {
					bm.find('.add_derkenar_viza_input').trigger('click');
				}

				var lastKurator = bm.find('[name="kuratorlar[]"]:last');
				lastKurator.select2('data', kurator);
			});

            if ('$mesulShexsMultiple$' === 'true') {
                derkenarInf["mesul_shexsler[]"].forEach(function(kurator, index) {
                    if (index > 0) {
                        bm.find('.add_mesul_shexsler_input').trigger('click');
                    }

                    var lastKurator = bm.find('[name="mesul_shexsler[]"]:last');
                    lastKurator.select2('data', kurator);
                });
            }

			Component.Form.setData(bm, derkenarInf, {
				ignore: ["kuratorlar[]", "mesul_shexsler[]"]
			});
		}

	});

</script>