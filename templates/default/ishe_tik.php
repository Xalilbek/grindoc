<script type="text/javascript" src="assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="assets/plugins/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="assets/plugins/unitegallery/js/unitegallery.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="asset/global/plugins/uniform/jquery.uniform.min.js?v=1"></script>
<script type="text/javascript" src="prodoc/asset/js/underscore_mixin.js"></script>
<script type="text/javascript" src="prodoc/asset/widget/hideShow.js"></script>
<script src="prodoc/asset/widget/multiple.js"></script>
<script type="text/javascript" src="asset/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.js"></script>
<script type="text/javascript" src="prodoc/asset/js/underscore.string.min.js"></script>
<style>
    #icraya_gonder .select2-search-choice-close,
    #icraya_gotur .select2-search-choice-close {
        display: none !important;
    }



    .input-group-addon {
        color: #fff;
        background-color: #449d44;
        border-color: #398439;
    }

    .input-group-addon > i {
        color: #fff !important;
    }

    .input-group-addon:hover {
        cursor: pointer;
        background-color: #398439;
    }



    div[data-function=item] {
        margin: 3px 0;
    }

    div[data-function=action-add], div[data-function=action-add]:hover, .addIcon, .addIcon:hover, .gizlet .input-group-addon, .gizlet .input-group-addon:hover {
        background: #1c8f5f;
        border-color: #1c8f5f;
    }

    div[data-function=item] .select2-container .select2-choice abbr {
        display: none;
    }


    .huquqi_qeydiyyat_form_yeni_sened {
        padding: 0;
        color: black;
        text-align: right;
        text-decoration: none;
    }

    .huquqi_qeydiyyat_form_yeni_sened_plus {
        margin-right: 5px;
        border-radius: 50%;
        color: lightgrey;
    }

</style>
<div class="modal-body form" style="padding: 0;">
	<form>
		<input type="hidden" name="daxil_olan_sened_id" value="<?php print $GLOBALS['daxil_olan_sened_id'] ?>">
		<input type="hidden" name="daxil_olan_sened_id" value="<?php print $GLOBALS['daxil_olan_sened_id'] ?>">
		<input type="hidden" name="taskId" value="<?php print $GLOBALS['taskId'] ?>">
		<input type="hidden" name="poa_user_id" value="<?php print $poa_user_id ?>">


		<div id="emeliyyat_body" style="padding: 8px;">
			<div class="ishe-tikilsin">
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
                        <?php if($all_operation): ?>
                            <div class=" icra_uchun ishe_tik_sened_icraya_gonder">
                                <div class="">
                                    <label class="">Sənədlər:</label>
                                    <div class="ishe_tik_senedShexs">
                                        <div class="" id="ishe_tik_senedInput" data-function="container">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

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
							<label>Visa/Razılaşdıran şəxs</label>
							<input class="form-control"
								name="confirming_users"
								data-plugin-params='{"multiple": true, "queryString": {"ne": "all_users"}}'
								data-plugin="select2-ajax"
								placeholder="Visa/Razılaşdıran şəxs"
							>
						</div>

						<div class="form-group">
							<label>Qeyd:</label>
							<textarea class="form-control" name="qeyd" placeholder="Qeyd" style="height: 70px;resize:vertical;"></textarea>
						</div>
					</div>
				</div>
                <div class="col-md-6 file-upload" data-name-pattern="document_%s[]">
                    <div class="add-file-btn">
                        <!--												<i class="fa fa-paperclip font-green-meadow"></i>-->
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>

                        <button type="button" class="btn btn-link font-dark" style="padding: 6px;">
                            <span style="font-weight: 500;">Sənəd əlavə et</span>
                        </button>
                    </div>
                    <div class="list-of-files">
                    </div>
                </div>
				<div class="row">
					<div class="col-md-12">
						<div class="errors_list alert alert-danger errorMsg"
							 style="display: none; width: 98%;; margin: 20px auto auto;">

						</div>
					</div>
				</div>

			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-circle green-meadow save">
		<i class="fa fa-check"></i> <span class="save-btn-text">
			<?php if (getProjectName() === TS): ?>
				Şərhlə bağla
			<?php else: ?>
				İşə tik
			<?php endif; ?>
		</span>
	</button>
	<button type="button" data-dismiss="modal" class="btn btn-circle default">
		İmtina et
	</button>
</div>
<script type="text/javascript" src="prodoc/settings.js"></script>
<script src="prodoc/asset/widget/fileUpload.js"></script>

<script type="text/x-underscore-template" id="ishe_tik_sened-id-select">
    <div class="input-group" data-function="item">
        <input name="ishe_tik_sened[]"
               class="form-control select"

               vezife="ishe_tik_sened" placeholder="Sənədlər">
        <%
        if(isFirst){
        %>
        <div class="input-group-addon" data-function="action-add">
            <i class="fa fa-plus"></i>
        </div>
        <% } else { %>

        <a data-function="action-remove" href="javascript:;" class="input-group-addon delete-icon">
            <i class="fa fa-minus"></i>
        </a>
        <% } %>
    </div>
</script>

<script type="text/javascript">


	$(function() {
		var bm = $("#bosh_modal<?php print $GLOBALS['MN'] ?>");
		Component.Plugin.PluginManager.init(bm);
		var modal = bm;
		var isheTikilsin = bm.find('.ishe-tikilsin');

		modal.find('.modal-footer .save').unbind('click').on('click', function() {

			var netice = $("[name=\"netice\"]");
			var qeyd = $("[name=\"qeyd\"]");
			var ishe_tik = $("[vezife=\"ishe_tik_sened\"]");
            var hasMistake = false;
			if (netice.length > 0 && +netice.val() === 0) {
				isheTikilsin.find("[name=\"netice\"]").prev("div").attr("style","border:1px dashed red !important");
                hasMistake =true;
			} else {
				isheTikilsin.find("[name=\"netice\"]").prev("div").attr("style","");
			}

			if (qeyd.val().length == 0) {
				isheTikilsin.find("[name=\"qeyd\"]").attr("style","border:1px dashed red !important");
                hasMistake =true;
			} else {
				isheTikilsin.find("[name=\"qeyd\"]").attr("style","")
			}


            <?php if($all_operation): ?>
            if (ishe_tik.val().length == 0) {
                isheTikilsin.find("[vezife='ishe_tik_sened']").prev('div').attr("style","border:1px dashed red !important");
                hasMistake =true;;
            } else {
                isheTikilsin.find("[vezife='ishe_tik_sened']").prev('div').attr("style","")
            }
            if(!hasMistake){
                Component.Form.send({
                    form: isheTikilsin.closest('form'),
                    url: 'prodoc/ajax/emeliyyatlar/ishe_tikilsin_multi.php',
                    success: function(res) {
                        console.log('asdasdas');
                        res = JSON.parse(res);
                        if (res.status === "error") {
                            Component.Form.showErrors(isheTikilsin, [res.error_msg]);

                        } else {
                            <?php if (getProjectName() === TS): ?>
                            toastr["success"]('Sənəd şərhlə bağlandı!');
                            console.log('ts');
                            <?php else: ?>
                            console.log('general');
                            toastr["success"]('Sənəd işə tikildi!');
                            <?php endif; ?>
                            console.log('aaa');
                            isheTikilsin.closest('.modal').modal('hide');
                            refreshActiveDocument();
                            $(".ishetik").css("display","none","important");

                        }
                    }
                });
            }

            <?php else: ?>
            if (!hasMistake){
                Component.Form.send({
                    form: isheTikilsin.closest('form'),
                    url: 'prodoc/ajax/emeliyyatlar/ishe_tikilsin.php',
                    success: function(res) {
                        res = JSON.parse(res);
                        if (res.status === "error") {
                            Component.Form.showErrors(isheTikilsin, [res.error_msg]);

                        } else {
                            <?php if (getProjectName() === TS): ?>
                            toastr["success"]('Sənəd şərhlə bağlandı!');
                            <?php else: ?>
                            toastr["success"]('Sənəd işə tikildi!');
                            <?php endif; ?>

                            isheTikilsin.closest('.modal').modal('hide');
                            refreshActiveDocument();
                            $(".ishetik").css("display","none","important");

                        }
                    }
                });
            }

			<?php endif; ?>
		});

            modal.find(".ishe_tik_senedShexs").multiple({
                itemTemplateId: 'ishe_tik_sened-id-select',
                initialItem: true,
                prepend: true,
                beforeAppend: function (item, e, extra) {

                    return CheckForEmptyInput(".ishe_tik_senedShexs", "ishe_tik_sened") || extra.isFirst;
                },
                afterAppend: function (item) {
                    Component.Plugin.PluginManager.init(item);
                    var ishe_tik_sened = item.find('input[name="ishe_tik_sened[]"]');
                    var sened_novu = '2';
                    var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);
                    axtarish(ishe_tik_sened, {
                        allowClear: false,
                        getAjaxData: function (t) {
                            return {
                                'ne': 'ishe_tik_senedler',
                                'extra_emekdash': collectValues()
                            }
                        }
                    });
                    getValues("#ishe_tik_senedInput")
                }
        });

        $('.file-upload').fileUpload({
            name: 'sened_fayl'
        });

        function getValues(el) {
            el = $(el);
            var firstInput = el.find('[data-function="item"]:first input');
            var lastInput = el.find('[data-function="item"]:last input.select');

            if (lastInput.select2('data')) {
                firstInput.select2('data', lastInput.select2('data'));
                lastInput.select2('data', null);
            }

            el.find('[data-function="item"]').find(".select2-container .select2-choice abbr").css("display", "none")
        }
        function CheckForEmptyInput(container, vezife) {
            return Boolean(+$(container).find('[data-function=item]:last:visible input[vezife=' + vezife + ']').val())
        }
        function collectValues() {
            var arr = [];
            $('.select').each(function () {
                var uid = +$(this).val();
                if (uid) {
                    arr.push(uid)
                }
            });

            return arr;
        }
	});


</script>