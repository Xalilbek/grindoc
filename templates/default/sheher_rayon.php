<style>
    .probtn{
        background: #1C8F5F !important;
        border-color: #1C8F5F !important;
    }
</style>
<div class="modal-body form">
    <form class="form-horizontal" id="sheher_kend" style="display: none" >
        <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-4"> Ölkə seç:</label>
                <div class="col-md-6">
                    <input name="olke_id"
                           class="form-control rayonlar select"
                           vezife="olke" placeholder="Ölkə">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4"> Şəhər/Rayon:</label>
                <div class="col-md-6">
                    <input class="form-control" vezife="sheher_ad" placeholder="Şəhər/Rayon">
                </div>
            </div>
        </div>
    </form>
    <form class="form-horizontal" id="rayon" style="display: none" >
        <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-4"> Şəhər/Rayon seç:</label>
                <div class="col-md-6">
                    <input name="sheher_id"
                           class="form-control rayonlar select"

                           vezife="rayon" placeholder=" Şəhər/Rayon">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4"> Kənd/Qəsəbə/Ərazi:</label>
                <div class="col-md-6">
                    <input class="form-control" vezife="rayon_ad" placeholder="Kənd/Qəsəbə/Ərazi">
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <button type="button" class="btn green btn-circle probtn" vezife="sheher_rayon_tesdiq">Əlavə et</button>
        <button type="button" class="btn default btn-circle" data-dismiss="modal">Bağla</button>
    </div>
</div>
<script>
    var tip       = '<?= $tip ?>';

    $('#'+tip).show();
    var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);
    var sheherKend = $('input[name="sheher_id"]');
    axtarish(sheherKend, {
        allowClear: false,
        getAjaxData: function (t) {
            return {
                'ne': 'city'
            }
        }
    });

    var olke = $('input[name="olke_id"]');
    axtarish(olke, {
        allowClear: false,
        getAjaxData: function (t) {
            return {
                'ne': 'country'
            }
        }
    });

    $("button[vezife='sheher_rayon_tesdiq']").click(function()
    {
        var
            baqlaBtn = $(this).next("button"),
            sheher_id = $('input[name="sheher_id"]').val(),
            olke_id   = $('input[name="olke_id"]').val(),
            sheher_ad = $("input[vezife='sheher_ad']").val(),
            rayon_ad =  $("input[vezife='rayon_ad']").val();

        if(sheher_ad!=""||rayon_ad!="")
        {
            modal_loading(1,MN);
            $.post(proBundle + "includes/msk/daxil_olan_sened_msk.php",
                {
                    'sheher_ad'             : sheher_ad,
                    'rayon_ad'              : rayon_ad,
                    'tip'                   : tip,
                    'sheher_id'             : sheher_id,
                    'olke_id'               : olke_id,
                },
                function(netice)
                {
                    modal_loading(0,MN);
                    if(netice=="movcuddur") {
                        errorModal("Bu adda daha öncə yaradılıb!",3000,true);
                    } else {
                        $(bosh_modal + "").modal("hide");
                    }
                });
        }
        else
        {
            errorModal("Məlumatları düzgün daxil edin!",3000,true);
        }
    });
</script>
