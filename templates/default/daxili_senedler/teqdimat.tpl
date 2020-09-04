<style>
    .accordion-block{
        padding-bottom:15px;
    }
    textarea{
        resize: vertical;
    }
    .col-md-6,textarea{
        margin:5px 0;
    }

    #tapsirig_table thead th{
        vertical-align: middle;
        text-align: center;
    }
    .kimClass{
        padding-left: 0;
        padding-right: 0;
    }

</style>

<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="assets/css/balloon.css"/>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
<script src="prodoc/asset/js/underscore_mixin.js"></script>
<script src="prodoc/asset/widget/multiple.js"></script>
<script type="text/javascript" src="prodoc/asset/widget/hideShow.js"></script>
<script src="assets/scripts/tippy.all.min.js"></script>


<div id="bosh_modal$MN$">

    <div class="modal-body form" style="padding: 0;" id="tapsirig_emri">
        <form action="">
            <input type="hidden" name="id" value="0">
            <div class="whiteboard">
                <div class="col-md-12">
                    <div class="blockname">
                        <h3 class="text-success text-left">
                            <strong>$2616qeydiyyat_pencereleri_teqdimat_$</strong>
                        </h3>
                        <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>
                </div>
                <div class="accordion-block">
                        <div class="col-md-6">
                            <div class="row">
                                $doc_num_input_html$
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-md-12">$2616etrafli_sened_tarix$:</label>
                                <div class="col-md-12">
                                    <div class="input-group date datetime">
                                        <input type="text" size="16" class="form-control" vezife="" name="senedin_tarixi"
                                               placeholder="2616qeydiyyat_pencereleri_tesdiq_olunma_tarix" value="">
                                        <span class="input-group-btn">
                                          <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="col-md-12 kimClass">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-md-12">$2616rollar_kim$:</label>
                                <div class="col-md-12">
                                    <input class="form-control" name="kim"
                                           data-plugin-params='{"queryString": {"ne": "emekdash"}}'
                                           data-plugin="select2-ajax"
                                           placeholder="$2616rollar_kim$">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-md-12 ">$2616rollar_kime$:</label>
                                <div class="col-md-12">
                                    <input class="form-control" name="kime"
                                           data-plugin-params='{"queryString": {"ne": "emekdash"}}'
                                           data-plugin="select2-ajax"
                                           placeholder="$2616rollar_kime$">
                                </div>
                            </div>
                        </div>
                    </div>


                        <div class="col-md-6 mr-1">
                            <div class="row">
                                <label class="col-md-12">$2616etrafli_melumat_qisa_mezmun$:</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input style="max-width: 508px;" class="form-control" name="qisa_mezmun"
                                               data-plugin-params='{"queryString": {"ne": "qisa_mezmun"}}'
                                               data-plugin="select2-ajax"
                                               placeholder="$2616etrafli_melumat_qisa_mezmun$">

                                        <div $mektubun_qisa_derkenar_metni$ class="input-group-addon addIcon mektubun_qisa_mezmunu">
                                            <i class="fa fa-plus"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="row">
                                <label class="col-md-12">$2616etrafli_melumat_metn$:</label>
                                <div class="col-md-12">
                                    <textarea class="form-control" rows="3" placeholder="$2616etrafli_melumat_metn$"
                                              name="melumat_metni" maxlength="1000"></textarea>
                                </div>
                            </div>
                        </div>


                </div>
            </div>
        </form>
    </div>

    <div class="modal-footer" style="border-top: 0;">
        <div style="float: left; color: red;" vezife="error"></div>
        <div style="float: left;">
            <button type="button" data-v="testiqle" class="btn green save btn-circle">$2616icraya_gonder$</button>
        </div>
    </div>
</div>
<script src="prodoc/modules/daxili_senedler/appendGeneralData.js"></script>

<script>

    $('.save').on('click',function () {
        input_change = false;
    })
    $(".datetime").datepicker({
        autoclose: true,
        format: "dd-mm-yyyy",
    });
    $(function () {

        var modal = $("#bosh_modal$MN$");

        Component.Plugin.PluginManager.init(modal);

        modal.find('.save').on('click', function() {

            var fd = new FormData();
            getFiles(fd);

            Component.Form.send({
                url: 'prodoc/ajax/teqdimat/add_edit_controller.php',
                sendUncheckedCheckbox: true,
                existingFormData: fd,
                form: modal,
                success: function (res) {
                    proccessResponse(res);
                }
            });
        });

        var inf = $inf$;
        if (!_.isNull(inf)) {
            Component.Form.setData(modal, inf);
        }

        modal.on('click','.document-info-button',function () {
            etrafliSenedId = $(this).parent('td').find('input[name="related_document_id[]"]').select2('data')['id'];
            $("#sened-elaveler").find('a[href]:first').trigger('click');
        })

    })

    // Məktubun qısa məzmunu
    $('.mektubun_qisa_mezmunu').on('click', function(){
        showTemplate('.mektubun_qisa_mezmunu','mektubun_qisa_mezmunu', 'Məktubun qısa məzmunu', {'mektubun_qisa_mezmunu':1}, 0, true);
    });

</script>