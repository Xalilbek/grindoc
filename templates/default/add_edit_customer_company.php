<style>
    .modal-footer{
        border-top:0;
    }
</style>
<div class="modal-body form">
    <form class="form-horizontal">
        <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-4"> <?= dil::soz("11shirketAdi"); ?></label>
                <div class="col-md-6">
                    <input class="form-control" vezife="compName" placeholder='<?= dil::soz("11shirketAdi"); ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4"> <?= dil::soz("11shirketNovu"); ?></label>
                <div class="col-md-6">
                    <input class="form-control" data-plugin="select2-ajax"
                           data-plugin-params='{"queryString": {"ne": "company_types"}}' vezife="compType">
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <div style="float: left; color: red;" vezife="error"></div>
    <div style="float: right;">
        <button style="background: #1C8F5F;border-color: #1C8F5F" type="button" class="btn green btn-circle" vezife="testiqle"><?= dil::soz("11elaveEt"); ?></button>
        <button style="" type="button" class="btn default btn-circle" data-dismiss="modal"><?= dil::soz("11bagla"); ?></button>
    </div>
</div>

<script>
    var gonderen_teshkilat = '<?= $gonderen_teshkilat ?>',
        gonderen_teshkilat_ad = '<?= $gonderen_teshkilat_ad ?>',
        gonderen_teshkilat_tipi = '<?= $gonderen_teshkilat_tipi ?>',
        gonderen_teshkilat_tipi_ad = '<?= $gonderen_teshkilat_tipi_ad ?>';

    $(function () {

        var bosh_modal_element = $(bosh_modal);
        Component.Plugin.PluginManager.init(bosh_modal_element);

        if (parseInt(gonderen_teshkilat) > 0) {
            $(bosh_modal + ' input[vezife=compName]').val(gonderen_teshkilat_ad);
            $(bosh_modal + ' input[vezife=compType]').select2('data', {id: gonderen_teshkilat_tipi, text: gonderen_teshkilat_tipi_ad});
            $(bosh_modal + " button[vezife='testiqle']").text('Düzəliş et');
        }

        var executor = <?php print json_encode($CompanyCount); ?>;

        if (executor.length == 1) {
            $(bosh_modal + ' input[vezife=compType]').select2('data', executor[0]);
            $(bosh_modal + ' input[vezife=compType]').select2('readonly', true);
        }

        $(bosh_modal + " button[vezife='testiqle']").click(function () {

            var baqlaBtn = $(this).next("button"),
                compName = $(bosh_modal + " input[vezife='compName']").val(),
                compType = $(bosh_modal + " input[vezife='compType']").val();
                $(".gonderen_teskilat div a").html(compName);

            if (compName != "" && compType > 0) {
                $(bosh_modal + " div[vezife='loading']").show();
                $.post(proBundle + "includes/customer/add_edit_customer_company.php",
                    {
                        'gonderen_teshkilat': gonderen_teshkilat,
                        'compName': compName,
                        'compType': compType
                    },
                    function (result) {
                        if (result == "daxil_olmayib") {
                            daxilOlmayibModal();
                            return false;
                        }
                        $(bosh_modal + " div[vezife='loading']").hide();
                        if (result == "tekrar_olmaz") {
                            $(bosh_modal + " div[vezife='error']").hide().text("Bu adda şirkət daha öncə əlavə edilmişdir.").fadeIn(300);
                        }
                        else {
                            $(bosh_modal + " div[vezife='error']").hide();
                            baqlaBtn.click();
                            // shirketSelectChange();
                        }
                    });
            }
            else {
                $(bosh_modal + " div[vezife='error']").text("<?= dil::soz('11shirketAdiTipiErr'); ?>");
            }
        });

    });

</script>
