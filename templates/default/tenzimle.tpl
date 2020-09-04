<link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="asset/global/plugins/uniform/css/uniform.default.css?v1"/>

<div class="modal-body form">
	<form class="form-horizontal form-bordered form-row-stripped" id="tenzimle_form">
		<div class="form-body">
			<input type="hidden" class="form-control" name="id" value="$id$">

			<div class="form-group">
				<label class="col-md-5 control-label">Seriya başlama nömrəsi:</label>
				<div class="col-md-3" style="line-height: 33px;">
					<input type="number" class="form-control" name="initial_number">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-5 control-label">Növbəti illərdə seriya başlama nömrəsi:</label>
				<div class="col-md-3" style="line-height: 33px;">
					<input type="number" class="form-control" name="initial_number_for_next_years">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-5 control-label">Nömrələnmə hansı tarixdən başlasın:</label>
				<div class="col-md-4" style="line-height: 33px;">
					<input class="form-control" name="active_from" data-plugin="datepickerJUI">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-5 control-label">Nömrə əlnən yazılsın:</label>
				<div class="col-md-4" style="line-height: 33px;">
					<input type="checkbox" class=".date-picker form-control tenzimle" name="editable" data-plugin="uniform">
				</div>
			</div>

			<div class="form-group" id="tarix_arxa">
				<label class="col-md-5 control-label">Arxa tarixlə yazmaq imkanı:</label>
				<div class="col-md-4" style="line-height: 33px;">
					<input type="checkbox" class="form-control tenzimle" name="editable_with_select" data-plugin="uniform">
				</div>
			</div>

			<div class="form-group" $repeat_appeal_style$>
				<label class="col-md-5 control-label">Təkrar vətəndaş müraciəti:</label>
				<div class="col-md-4" style="line-height: 33px;">
					<input type="checkbox" class="form-control tenzimle" name="repeat_appeal" data-plugin="uniform">
				</div>
			</div>

			<div class="form-group" $set_number_after_approval_style$>
				<label class="col-md-5 control-label">Nömrə təsdiqdən sonra verilsin:</label>
				<div class="col-md-4" style="line-height: 33px;">
					<input type="checkbox" class="form-control" name="set_number_after_approval" data-plugin="uniform">
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
			<i class="fa fa-check"></i> Yadda saxla
		</button>
		<button type="button" data-dismiss="modal" class="btn default">
			<i class="fa fa-close"></i> Bağla
		</button>
	</div>
</div>

<script type="text/javascript" src="assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="asset/global/plugins/uniform/jquery.uniform.min.js?v=1"></script>
<script type="text/javascript" src="prodoc/settings.js"></script>
<script type="text/javascript">

	$(function() {

		var direction = "$direction$";
		if(direction === "outgoing"){
			$('#tarix_arxa').css('display',"none")
		}

		var bm = $("#bosh_modal$MN$");
		$('.date-picker').datepicker({
			autoclose: true,
			format: "dd-mm-yyyy"
		});
		Component.Plugin.PluginManager.init(bm);

		bm.find('[vezife="testiqle"]').on('click', function() {

			Component.Form.send({
				form: bm,
				url: 'prodoc/ajax/msk/nomrelenme_tenzimle.php',
				success: function (res) {
					res = JSON.parse(res);
					if (res.status === "error") {
						Component.Form.showErrors(bm, res.errors);
					} else {
						bm.find(".errors_list").slideUp();
						toastr.success('Yadda saxlandı!');
						bm.modal('hide');
					}
				}
			});

		});

		var inf = $inf$;
		if (!_.isNull(inf)) {
			Component.Form.setData(bm, inf);
		}
	});

    $(function() {
        $("#tenzimle_form input:checkbox").on('click', function() {
            var $box = $(this);
            if ($box.is(":checked")) {
                var group = "input.tenzimle";
                $(group).prop("checked", false).trigger('change');
                $box.prop("checked", true).trigger('change');
            } else {
                $box.prop("checked", false).trigger('change');
            }

            $.uniform.update($(group));
        });
    });

</script>