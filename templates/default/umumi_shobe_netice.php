<style>
    .probtn{
        background: #1C8F5F !important;
        border-color: #1C8F5F !important;
    }

    .form .form-bordered .form-group .control-label {
        padding-top: 5px;
    }

    #bosh_modal1 .modal-content  {
        -webkit-border-radius: 15px !important;
        -moz-border-radius: 15px !important;
        border-radius: 15px !important;
    }

    .modal-header{
        -webkit-border-radius: 13px 13px 0 0 !important;
        -moz-border-radius: 13px 13px 0 0 !important;
        border-radius: 13px 13px 0 0 !important;

    }
</style>

<div class="modal-body form" style="padding: 0;">
    <form class="form-horizontal form-bordered form-row-stripped">
        <div class="form-body">
            <div class="form-group">
                <div class="col-md-12">
                    <div class="row">
                        <label class="control-label col-md-3">Nəticə:</label>
                        <div class="col-md-5 document-number-container">
                            <div class="input-icon right">
                                <input name="netice"
                                       type="text"
									   data-plugin="select2-ajax"
									   data-plugin-params='{"queryString": {"ne": "prodoc_neticeler"}}'
                                       class="form-control"
                                       placeholder="Nəticə">
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" value="<?= $sid ?>" name="id">
            </div>
			<div class="form-group">
				<div class="col-md-12">
					<div class="row">
						<label class="control-label col-md-3">Qeyd:</label>
						<div class="col-md-6">
							<textarea class="form-control" name="note" style="height: 90px;"></textarea>
						</div>
					</div>
				</div>
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
        <button type="button" data-v="testiqle" class="btn red btn-circle save">Yadda saxla</button>
        <button type="button" data-dismiss="modal" class="btn default btn-circle">Bağla</button>
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


		bm.on('click', '.save', function() {
			Component.Form.send({
				url: <?php if ($from_daxili): ?> 'prodoc/ajax/testiqleme/testiq_et_daxili.php' <?php else: ?> 'prodoc/ajax/testiqleme/testiq_et.php'  <?php endif; ?>,
				form: bm,
				success: function (res) {
					proccessResponse(res);
				}
			});
		});

		Component.Plugin.PluginManager.init(bm);
		var netice = <?php echo $resultOfDocument ?>;

		if (netice.id!=null){
            bm.find('[name="netice"]').select2('data', <?php echo $resultOfDocument ?>).trigger('change');
        }

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
				refreshActiveDocument();
			}
		};

	});
</script>
