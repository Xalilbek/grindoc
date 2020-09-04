<div class="modal-body form" style="padding: 0;">
    <form>
        <input type="hidden" name="id" value="<?php print $id ?>">
        <div id="emeliyyat_body" style="padding: 8px;">
            <div class="ishe-tikilsin">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="form-group">
                            <label>Qeyd:</label>
                            <textarea class="form-control" name="note" placeholder="Qeyd" style="height: 70px;resize:vertical;"></textarea>
                        </div>
                        <div class="form-group file-upload">
                            <div class="add-file-btn">
                                <i class="fa fa-paperclip font-green-meadow"></i>

                                <button type="button" class="btn btn-link font-dark" style="padding: 6px;">
                                    <span style="font-weight: 500;">Sənəd əlavə et</span>
                                </button>
                            </div>
                            <div class="list-of-files">
                            </div>
                        </div>
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
			Göndər
		</span>
    </button>
</div>
<script src="prodoc/asset/widget/multiple.js"></script>
<script type="text/javascript" src="prodoc/settings.js"></script>
<script src="prodoc/asset/widget/fileUpload.js"></script>

<script type="text/javascript">

    $(function() {
        var bm = $("#bosh_modal<?php print $GLOBALS['MN'] ?>");
        Component.Plugin.PluginManager.init(bm);

        var modal = bm;

        modal.find('.file-upload').fileUpload({
            name: 'sened'
        });

        var isheTikilsin = bm.find('.ishe-tikilsin');

        modal.find('.modal-footer .save').unbind('click').on('click', function() {
            var note = $("[name=\"note\"]");

            if (note.val().length == 0) {
                isheTikilsin.find("[name=\"note\"]").attr("style","border:1px dashed red !important");
                return;
            } else {
                isheTikilsin.find("[name=\"note\"]").attr("style","")
            }

            Component.Form.send({
                form: isheTikilsin.closest('form'),
                url: 'prodoc/ajax/testiqleme/testiq_et.php',
                success: function(res) {
                    res = JSON.parse(res);
                    if (res.status === "error") {
                        Component.Form.showErrors(isheTikilsin, [res.error_msg]);
                    } else {
                        toastr["success"]('Yerinə yetirildi!');

                        isheTikilsin.closest('.modal').modal('hide');
                        refreshActiveDocument();
                    }
                }
            });
        });
    });
</script>