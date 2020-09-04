<style>
    .nav-tabs.nav-justified>.active>a,
    .nav-tabs.nav-justified>.active>a:hover,
    .nav-tabs.nav-justified>.active>a:focus {
        border: none !important;
    }
    #sened-elaveler-body p {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: bold;
        font-size: 14px;
    }

    #sened-elaveler-body p span {
        font-weight: normal;
    }

    #sened-elaveler-body > dl:nth-child(1) {
        margin-top: -10px;
    }

    .accordion-block {
        padding-bottom: 15px;
    }

    textarea {
        resize: vertical;
    }

    .col-md-6, textarea {
        margin: 5px 0;
    }

    #tapsirig_table thead th {
        vertical-align: middle;
        text-align: center;
    }

</style>
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="assets/css/balloon.css"/>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
<script src="prodoc/asset/js/underscore_mixin.js"></script>
<script src="prodoc/asset/widget/multiple.js"></script>
<script type="text/javascript" src="prodoc/asset/widget/hideShow.js"></script>
<script src="assets/scripts/tippy.all.min.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>

<div id="bosh_modal$MN$">

    <div class="row">
        <div class="col-md-12">
            <div class="modal-body form" style="padding: 0;" id="tapsirig_emri">
             <form action="">
                    <input type="hidden" name="id" value="0">
                    <input type="hidden" name="related_doc_id" value="0">
                     <input type="hidden" name="poa_user_id" value="$executor$">

                 <div class="whiteboard">
                        <div class="col-md-12">
                            <div class="blockname">
                                <h3 class="text-success text-left">
                                    <strong>$2616qeydiyyat_pencereleri_baglanti$</strong>
                                </h3>
                                <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                            </div>
                        </div>
                        <div class="accordion-block">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="col-md-12">
                                            $2616sened_tipi_dos$:
                                        </label>
                                        <div class="col-md-12">
                                            <select name="related_document_menu" data-plugin="select2"
                                                    class="form-control">
                                                <option value="incoming">$2616daxil_olan_own_tab$</option>
                                                <option value="internal">$2616esas_filter_daxili$</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" style="margin-top: 0px;">
                                    <div class="form-group">
                                        <label for="" class="col-md-6">
                                            $2616etrafli_sened_nomre$:
                                        </label>
                                        <div class="col-md-12">
                                            <input style="width: 100%;" name="related_document_id" class="form-control"
                                                   data-plugin-params='{"queryString": {"ne": "icra_eden_sexs_document_number"}}'
                                                   data-plugin="select2-ajax" placeholder="$2616nomre_sutun$">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="whiteboard">
                        <div class="col-md-12">
                            <div class="blockname">
                                <h3 class="text-success text-left">
                                    <strong>$2616sened_novu_dos$</strong>
                                </h3>
                                <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                            </div>
                        </div>
                        <div class="accordion-block">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        $doc_num_input_html$
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="col-md-12">
                                            $2616etrafli_sened_tarix$:
                                        </label>
                                        <div class="col-md-12">
                                            <input name="senedin_tarixi" value="$current_date$" type="text"
                                                   class="sened_tarix form-control" placeholder="Sənədin tarixi">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="col-md-8">
                                            $2616rollar_yeni_icra_eden$:
                                        </label>
                                        <div class="col-md-12">
                                            <input name="yeni_icra_eden_sexs" class="form-control"
                                                   data-plugin="select2-ajax"
                                                   data-plugin-params='{"queryString": {"ne": "emekdash"}}'
                                                   placeholder="$2616rollar_yeni_icra_eden$">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-md-8"> $2616etrafli_yeni_hemicraci_shexsler$:</label>
                                        <div class="hemIcraciShexs">
                                            <div class="col-md-12" id="hemIcraciShexsInput" data-function="container">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row evvelki_sexsler" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="col-md-6">
                                            Əvvəlki icra edən şəxs:
                                        </label>
                                        <div class="col-md-12 icra_eden">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-md-12"> Əvvəlki həm icraçı şəxslər:</label>
                                        <div class="col-md-12 hem_icra_eden">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="whiteboard">
                        <div class="col-md-12">
                            <div class="blockname">
                                <h3 class="text-success text-left">
                                    <strong>$2616qeydiyyat_pencereleri_mezmun$</strong>
                                </h3>
                                <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                            </div>
                        </div>
                        <div class="accordion-block">

                            <div class="form-group">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-md-12">$2616etrafli_mektub_qm$</label>
                                        <div class="col-md-12">
                                            <input class="form-control" name="qisa_mezmun"
                                                   data-plugin-params='{"queryString": {"ne": "qisa_mezmun"}}'
                                                   data-plugin="select2-ajax"
                                                   placeholder="$2616etrafli_mektub_qm$">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-12">$2616daxil_olan_qisa_mezmun$</label>
                                        <div class="col-md-12">
                                    <textarea class="form-control" rows="3" placeholder="$2616daxil_olan_qisa_mezmun$"
                                              name="qeyd" maxlength="1000"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="whiteboard icraya_gonder">
                <div class="col-md-12">
                    <div class="blockname">
                        <h3 class="text-success text-left">
                            <strong> $2616qeydiyyat_pencereleri_razilashdirma$</strong>
                        </h3>
                        <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>
                </div>
                <div class="accordion-block">
                    <div class="form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-md-12"> $2616rollar_viza$ </label>
                                <div class="yoxlayanShexs">
                                    <div class="col-md-12" id="yoxlayanShexsInput" data-function="container">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/x-underscore-template" id="yoxlayanShexs-id-select">
                <div class="input-group" data-function="item">
                    <input name="yoxlayanShexs[]"
                           class="form-control select"

                           vezife="yoxlayanShexs" placeholder="$2616rollar_viza$">
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

            <script type="text/x-underscore-template" id="hemIcraciShexs-id-select">
                <div class="input-group" data-function="item">
                    <input name="hemIcraciShexs[]"
                           class="form-control select"

                           vezife="hemIcraciShexs" placeholder="$2616etrafli_yeni_hemicraci_shexsler$">
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
            </form>

            <div class="modal-footer" style="border-top: 0;">
                <div style="float: left; color: red;" vezife="error"></div>
                <div style="float: right;">
                    <button type="button" data-v="testiqle" class="btn green save btn-circle">$2616icraya_gonder$</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="prodoc/asset/js/underscore.string.min.js"></script>
    <script type="text/javascript" src="prodoc/asset/js/underscore_mixin.js"></script>

    <script src="prodoc/modules/daxili_senedler/appendGeneralData.js"></script>
    <script src="prodoc/asset/widget/fileUpload.js"></script>
    <script src="prodoc/asset/widget/hideShow.js"></script>
    <script src="prodoc/asset/widget/multiple.js"></script>
    <script src="prodoc/app.js"></script>


    <script>
        var icra_sexs_deyis = '#tapsirig_emri',
            modal = $("#bosh_modal$MN$");

        $('#daxili_sened').removeClass('col-md-12').addClass('col-md-7');

        $(function () {
            $("#bosh_modal$MN$ .sened_tarix").datepicker({
                autoclose: true,
                format: "dd-mm-yyyy"
            });

            Component.Plugin.PluginManager.init(modal);
            var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

            var docNumber = modal.find('input[name="related_document_id"]');

            axtarish(docNumber, {
                getAjaxData: function (t) {
                    var related_document_type = modal.find('[name="related_document_menu"]').val();

                    return {
                        'ne': 'icra_eden_sexs_document_number',
                        'direction': related_document_type
                    }
                }
            });

            initIcrayaGonder(modal);
        });

        function EvvelkiSexslerHtml() {
            modal.find(".icra_eden").html('');
            modal.find(".hem_icra_eden").html('');
        }

        modal.find('[name="related_document_id"]').change(function () {
            if ($(this).val() != "") {

                modal.find('[name="related_doc_id"]').val($(this).select2('data')['sened_id']);

                EvvelkiSexslerHtml();
                var id = $(this).val();
                $.post('prodoc/ajax/icra_eden_sexsin_deyisdirilmesi_hem_sexsler.php', {'tasks_id': id}, function (netice) {
                    if (netice != "") {
                        netice = JSON.parse(netice);

                        modal.find('.evvelki_sexsler').show();

                        if (!_.isEmpty(netice['mesul_shexs'])) {
                            modal.find(".icra_eden").append('<strong>' + netice['mesul_shexs'] + '</strong><br>');
                        }

                        _.each(netice['istirakchi'], function (item) {
                            modal.find(".hem_icra_eden").append('<strong>' + item['user_ad'] + '</strong><br>');
                        });
                    }
                });
                $('#sened-elaveler li:nth-child(1) a').click();
            }
            else {
                modal.find('.evvelki_sexsler').hide();
                EvvelkiSexslerHtml();
            }
        });

        function initIcrayaGonder(container) {
            $(document).ready(function () {

                container.find(".yoxlayanShexs").multiple({
                    itemTemplateId: 'yoxlayanShexs-id-select',
                    initialItem: true,
                    prepend: true,
                    beforeAppend: function (item, e, extra) {
                        return CheckForEmptyInput(".yoxlayanShexs", "yoxlayanShexs") || extra.isFirst;
                    },
                    afterAppend: function (item) {
                        var kurator = item.find('input[name="yoxlayanShexs[]"]');
                        var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

                        axtarish(kurator, {
                            allowClear: false,
                            getAjaxData: function (t) {
                                return {
                                    'ne': 'yoxlayan_shexsler',
                                    'tip': 'daxili',
                                    'extra_emekdash': collectValues()
                                }
                            }
                        });
                        getMultiSelectValues("#yoxlayanShexsInput")
                    }
                });

                container.find(".hemIcraciShexs").multiple({
                    itemTemplateId: 'hemIcraciShexs-id-select',
                    initialItem: true,
                    prepend: true,
                    beforeAppend: function (item, e, extra) {
                        return CheckForEmptyInput(".hemIcraciShexs", "hemIcraciShexs") || extra.isFirst;
                    },
                    afterAppend: function (item) {
                        var kurator = item.find('input[name="hemIcraciShexs[]"]');
                        var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

                        axtarish(kurator, {
                            allowClear: false,
                            getAjaxData: function (t) {
                                return {
                                    'ne': 'emekdash',
                                    'extra_emekdash': collectValues()
                                }
                            }
                        });
                        getMultiSelectValues("#hemIcraciShexsInput")
                    }
                });
            });
        }


        $("#sened-elaveler").find('a[href]').click( function() {
            if(!modal.find('input[name="related_document_id"]').select2('data'))
            {
                return;
            }

            var tip = 'daxil_olan_sened';

            var data_tip_fayl = {
            };

            var file_name = !(tip in data_tip_fayl) ? 'umumi' : data_tip_fayl[tip];

            if ("daxil_olan_sened" === tip) {
                $("#derkenar_tab").show();
            } else {
                $("#derkenar_tab").hide();
            }

            var sened_elaveler_inf = {
                'sened-etrafli' : 'prodoc/ajax/dashboard/etrafli/' + file_name + '.php',
                'sened-tarixce' : 'prodoc/ajax/dashboard/tarixce/' + file_name + '.php',
                'sened-senedler' : 'prodoc/ajax/dashboard/senedler/' + file_name + '.php',
                'sened-derkenarlar' : 'prodoc/ajax/dashboard/derkenarlar/derkenarlar.php'
            };


            var path = sened_elaveler_inf[$(this).attr('href').slice(1)];

            // Remove downloaded gallery div from DOM -- sened-senedler
            $('.ug-gallery-wrapper.ug-lightbox').remove();

            $.post( path, {
                'sened_id' : modal.find('input[name="related_document_id"]').select2('data')['sened_id'],
                'tip' : tip
            }, function (response) {
                response = JSON.parse(response);
                $("#sened-elaveler-body").html(response.html);;

                if ('incoming' === response.type) {
                    modal.find('.document_type').text('Daxil olan');
                } else {
                    modal.find('.document_type').text('Daxili');
                }
            });
        });

        $('.save').on('click', function () {
            input_change = false

            var fd   = new FormData(),
            sehv_var = false;

            $("input[name='sened_fayl[]']").each(function (e,key) {
                fd.append("sened_fayl[]", $(key).val());
            })
            $("input[name='qoshma_fayl[]']").each(function (e,key) {
                fd.append("qoshma_fayl[]", $(key).val());
            })

            if(!sehv_var) {
                Component.Form.send({
                    url: 'prodoc/ajax/icra_sexsin_deyisdirilmesi/add_edit_controller.php',
                    sendUncheckedCheckbox: true,
                    existingFormData: fd,
                    form: modal,
                    success: function (res) {
                        proccessResponse(res);
                    }
                });
            }
        });

        $(function () {
            var inf = $inf$,
                bm = $('#tapsirig_emri');

            if (!_.isNull(inf)) {

                inf["hemIcraciShexs[]"].forEach(function (hemIcraciShexs, index) {
                    if (index > 0) {
                        bm.find("#hemIcraciShexsInput [data-function=action-add]").trigger('click');
                    }

                    var lastIshtrakci = bm.find('[name="hemIcraciShexs[]"]:last');

                    if (hemIcraciShexs.text != null) lastIshtrakci.select2('data', hemIcraciShexs);
                });

                inf["yoxlayanShexs[]"].forEach(function (yoxlayanShex, index) {
                    if (index > 0) {
                        modal.find("#yoxlayanShexsInput [data-function=action-add]").trigger('click');
                    }

                    var lastIshtrakci = modal.find('[name="yoxlayanShexs[]"]:last');

                    if (yoxlayanShex.text != null) lastIshtrakci.select2('data', yoxlayanShex);
                });
                Component.Form.setData(bm, inf, {
                    ignore: ["hemIcraciShexs[]", "yoxlayanShexs[]"]
                });
            }
        });

        function collectValues() {
            var arr = [];
            $('.select').each(function () {
                if ($(this).val() != '') {
                    arr.push($(this).val());
                }
            })
            return arr;
        }

        setTimeout( function () {
                var senedHazirla  = $('[name=sened_hazirladan]').val();

                if(senedHazirla)
                {
                    $('[name="related_document_menu"]').val("internal").change();
                    var senedNomre = $('input[name="related_document_id[]"]').val();
                    var lastInput = $('[name="related_document_id"]');
                    Component.Plugin.Plugin.select2AjaxPlugin.setValue(lastInput, senedNomre, {
                        triggerChange: true
                    });
                    $('.whiteboard').eq(0).hide();
                    $('#tapsirig_emri').parents('div').eq(0).addClass('col-md-12').removeClass('col-md-7');
                }
            }
        );

    </script>