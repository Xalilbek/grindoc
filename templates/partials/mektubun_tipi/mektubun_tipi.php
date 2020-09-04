<?php
    $mektubun_tipi_first  = \Service\Option\Option::getOrCreateValue('mektubun_tipi_first_table', '2616etrafli_mektub_tip');
    $mektubun_tipi_second = \Service\Option\Option::getOrCreateValue('mektubun_tipi_second_table', '2616etrafli_alt');
    $mektubun_tipi_third  = \Service\Option\Option::getOrCreateValue('mektubun_tipi_third_table', '2616qeydiyyat_pencereleri_alt_movzu');
    $mektubun_tipi_last   = \Service\Option\Option::getOrCreateValue('mektubun_tipi_last_table', '2616qeydiyyat_penceleri_mektub_mezmun');
?>
<style>
    .select2-chosen{
        max-width: 250px;
    }
</style>
<div id="mektubun_tipi">
    <div class="form-group hide-when-binding-to-outgoing-document">
        <div class="<?= $input_tip ?>">
            <div class="row">
                <label class="col-md-12">
                    <?= dsAlt($mektubun_tipi_first, "Məktubun tipi")?>
                </label>
                <div class="col-md-12">
                    <div class="input-group">
                        <input name="mektubun_tipi" class="form-control" data-plugin="select2-ajax"
                               data-plugin-params='{"queryString": {"ne": "prodoc_mektubun_tipi", "tip": "<?= $tip ?>"}}'
                               onchange="deyer('mektubun_alt_tipi', true);"
                               placeholder="<?= dsAlt($mektubun_tipi_first, "Məktubun tipi"); ?>">
                        <div class="input-group-addon addIcon <?= $qeydiyat ?> mektubun_tipi_fiziki">
                            <i class="fa fa-plus"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="<?= $input_tip ?>">
            <div class="row">
                <label class="col-md-12">
                    <?= dsAlt($mektubun_tipi_second, "Alt tipi") ?>
                </label>
                <div class="col-md-12">
                    <div class="input-group">
                        <input data-plugin="select2-ajax" name="mektubun_alt_tipi" class="form-control"
                               placeholder="<?=  dsAlt($mektubun_tipi_second, "Alt tipi") ?>">
                        <div class="input-group-addon addIcon <?= $qeydiyat ?> mektubun_alt_tipi_fiziki">
                            <i class="fa fa-plus"></i></div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="form-group hide-when-binding-to-outgoing-document">
        <div class="<?= $input_tip ?>">
            <div class="row" style="display: none">
                <label class="col-md-12">
                    <?= dsAlt($mektubun_tipi_third, "Alt mövzu") ?>
                </label>
                <div class="col-md-12">
                    <input name="mektubun_tipi_third"
                           data-plugin="select2-ajax"
                           class="form-control"
                           placeholder="<?= dsAlt($mektubun_tipi_third, "Alt mövzu") ?>">
                </div>
            </div>
        </div>
        <div class="<?= $input_tip ?>">
            <div class="row" style="display: none">
                <label class="col-md-12"><?= dsAlt('2616dos_tibb', "Tibb müəssisəsi"); ?>:</label>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <select class="form-control" id="tibbMuessisesiDovletOzel" vezife="tibbMuessisesiDovletOzel" placeholder="Təyinatı" style="margin-left: -13px; width: 104px;">
                            <option value="dovlet">Dövlət</option>
                            <option value="ozel">Özəl</option>
                        </select>
                    </div>

                    <div class="col-md-8" style="padding-right: 1px;">
                        <input name="tibb_muessisesi"
                               data-plugin="select2-ajax"
                               class="form-control"
                               placeholder="Tibb müəssisəsi">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group hide-when-binding-to-outgoing-document">
        <div class="<?= $input_tip ?>">
            <div class="row" style="display: none">
                <label class="col-md-12"><?= dsAlt('2616dos_nazalogiya', "Nazalogiya"); ?>:</label>
                <div class="col-md-12">
                    <input name="nazalogiya"
                           data-plugin="select2-ajax"
                           class="form-control"
                           placeholder="Nazalogiya">
                </div>
            </div>
        </div>
        <div class="<?= $input_tip ?>">
            <div class="row" style="display: none">
                <label class="col-md-12"><?= dsAlt($mektubun_tipi_last, "Məktubun məzmunu") ?></label>
                <div class="col-md-12">
                    <input name="mektubun_mezmunu"
                           data-plugin="select2-ajax"
                           class="form-control"
                           placeholder="<?= dsAlt($mektubun_tipi_last, "Məktubun məzmunu") ?>">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function initMektubunTipi(container, editing) {
        $( document ).ready(function() {

            var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

            axtarish($("input[name=mektubun_alt_tipi]"), {
                getAjaxData: function () {
                    return {
                        'ne': 'prodoc_mektubun_alt_tipi',
                        'parent_id': $("input[name=mektubun_tipi]").val()
                    }
                }
            });

            axtarish($('[name="mektubun_tipi_third"]'), {
                getAjaxData: function () {
                    return {
                        'ne': 'mektubun_tipi',
                        'tip': 'last_two',
                        'parent_id' : $("input[name=mektubun_alt_tipi]").val()
                    }
                }
            });

            axtarish($('[name="tibb_muessisesi"]'), {
                getAjaxData: function () {
                    return {
                        'ne': 'tibb_muessisesi',
                        'tip': $('#tibbMuessisesiDovletOzel').val()
                    }
                }
            });

            axtarish($('[name="mektubun_mezmunu"]'), {
                getAjaxData: function () {
                    return {
                        'ne': 'mektubun_mezmunu',
                        'parent_id' :  $("input[name=mektubun_tipi_third]").val()
                    }
                }
            });

            axtarish($('[name="nazalogiya"]'), {
                getAjaxData: function () {
                    return {
                        'ne': 'nazalogiya'
                    }
                }
            });

            container.find('[name=mektubun_alt_tipi]').on('change', function () {
                var id = $(this).val();

                $.post('prodoc/includes/mektubun_tipi.php', {'id': id, 'tip': 'last_two'}, function(res) {
                    if(res > 0)
                    {
                        mektubunInputShow('mektubun_tipi_third');
                    }
                    else
                    {
                        mektubunInputSil('mektubun_tipi_third');
                        mektubunInputSil('nazalogiya');
                        mektubunInputSil('tibb_muessisesi');
                        mektubunInputSil('mektubun_mezmunu');
                    }
                });
            });

            function mektubunInputSil(name) {
                $('[name="'+ name +'"]').closest('div.row').hide();
                $('[name="'+ name +'"]').select2("val", "");
            }

            function mektubunInputShow(name) {
                $('[name="'+ name +'"]').closest('div.row').show();
            }

            container.find('[name=mektubun_tipi_third]').on('click', function () {
                mektubunInputSil('nazalogiya');
                mektubunInputSil('tibb_muessisesi');
                mektubunInputSil('mektubun_mezmunu');
            });

            container.find('[name=mektubun_tipi_third]').on('change', function () {
                var id = $(this).val();

                $.post('prodoc/includes/mektubun_tipi.php', {'id': id, 'tip': 'last'}, function(res) {

                    if(_.isEmpty(res))
                    {
                        mektubunInputSil('nazalogiya');
                        mektubunInputSil('tibb_muessisesi');
                        mektubunInputSil('mektubun_mezmunu');
                        return false;
                    }

                    res = JSON.parse(res);

                    if(res['nozalogiyaTibMuessisesi'] == 1)
                    {
                        mektubunInputShow('nazalogiya');
                    }

                    if(res['nozalogiyaTibMuessisesi'] == 2)
                    {
                        mektubunInputShow('tibb_muessisesi');
                    }

                    if(res['nozalogiyaTibMuessisesi'] == 3)
                    {
                        mektubunInputShow('nazalogiya');
                        mektubunInputShow('tibb_muessisesi');
                    }

                    if(res['mektubunMezmunu'] > 0)
                    {
                        mektubunInputShow('mektubun_mezmunu');
                    }
                });
            });
        });
    }

</script>
