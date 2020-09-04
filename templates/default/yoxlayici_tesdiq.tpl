<div class="modal-body form" style="padding: 0;">
    <form class="form-horizontal form-bordered form-row-stripped">
        <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-4">Yönləndir:</label>
                <div class="col-md-5">
                    <input class="form-control"
                           data-plugin="select2-ajax"

                           vezife="yoxlayici" placeholder="Yönləndir"
                    >
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">İmtinanın səbəbi:</label>
                <div class="col-md-7">
                    <textarea class="form-control" vezife="qeyd" placeholder="İmtinanın səbəbi" style="height: 70px;resize:vertical;"></textarea>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="float: left; color: red;" vezife="error"></div>
    <div style="float: right;">
        <button type="button" data-v="testiqle" class="btn red">İmtina et</button>
        <button type="button" data-dismiss="modal" class="btn default">Bağla</button>
    </div>
</div>
<script type="text/javascript" src="prodoc/settings.js"></script>

<script type="text/javascript">



</script>