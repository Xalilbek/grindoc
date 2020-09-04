<style>
    .probtn{
        background: #1C8F5F !important;
        border-color: #1C8F5F !important;
    }
</style>
<div class="modal-body form">
    <form class="form-horizontal">
        <div class="form-body">
            <div <?php print $gonder ?> class="form-group">
                <label class="control-label col-md-4"> Hara göndərilir</label>
                <div class="col-md-6">
                    <input class="form-control" vezife="ad" placeholder="Hara göndərilir">
                </div>
            </div>
            <div <?php print $shexs ?> class="form-group">
                <label class="control-label col-md-4"> Şəxs</label>
                <div class="col-md-6">
                    <input class="form-control select" name="elaqeli_shexs" vezife="elaqeli_shexs" placeholder="Əlaqəli şəxs">
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <button type="button" class="btn green btn-circle probtn" vezife="testiqle">Əlavə et</button>
        <button type="button" class="btn default btn-circle" data-dismiss="modal">Bağla</button>
    </div>
</div>
<script>

    $("input[name='elaqeli_shexs']").select2({
        allowClear: true,
        multiple:true,
        ajax: {
            url: "includes/plugins/axtarish.php",
            type: 'POST',
            dataType: 'json',
            data: function (soz)
            {
                return {
                    'a': soz,
                    'ne': 'elaqeli_shexs',
                };
            },
            results: function(data,a)
            {
                return data
            },
            cache: true
        }
    });

    var tip       = '<?= $tip ?>';
    var parent_id = '<?= $parent_id ?>';

    $("button[vezife='testiqle']").click(function()
    {
        if(tip == 'gonder')
        {
            var baqlaBtn = $(this).next("button"),
                ad = $(bosh_modal + " input[vezife='ad']").val().trim();

            if(ad!="")
            {
                modal_loading(1,MN);
                $.post(proBundle + "includes/msk/sened_hazirla_aidiyyat_orqan.php",
                    {
                        'ad'            : ad,
                        'tip'           : tip,
                        'parent_id'     : parent_id
                    },
                    function(netice)
                    {
                        if(netice=="movcuddur") {
                            errorModal("Bu adda sənəd növü daha öncə yaradılıb!",3000,true);
                        } else {
                            $(bosh_modal + "").modal("hide");
                        }
                    });
            }
            else
            {
                errorModal("Başlığı daxil etmədiz!",3000,true);
            }
        }
        else if(tip == 'shexs')
        {
            var elaqeli_sexs = $('input[name="elaqeli_shexs"]').val();

            $.post(proBundle + "includes/msk/sened_hazirla_aidiyyat_orqan.php",
                {
                    'parent_id'     : parent_id,
                    'tip'           : tip,
                    'elaqeli_sexs'  : elaqeli_sexs
                },
                function(netice)
                {
                    if(netice=="movcuddur") {
                        errorModal("Bu adda sənəd növü daha öncə yaradılıb!",3000,true);
                    } else {
                        $(bosh_modal + "").modal("hide");
                    }
                });
        }
    });
</script>
