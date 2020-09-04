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
<script src="prodoc/app.js"></script>

<div id="bosh_modal$MN$">

    <div class="row">
        <div class="col-md-12">
            <div class="modal-body form" style="padding: 0;" id="tapsirig_emri">
                <form action="">
                    <input type="hidden" name="id" value="0">
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
                                            $2616etrafli_sened_tip$:
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
                                            $2616sened_tipi_dos$:
                                        </label>
                                        <div class="col-md-12">
                                            <input name="document_id" style="width: 100%"
                                                   data-plugin-params='{"queryString": {"ne": "icra_muddeti_document_number"}}'
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
                                    <strong>$2616etrafli_sened_nov$</strong>
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
                                        <label for="" class="col-md-6">
                                            $2616etrafli_cari_icra$:
                                        </label>
                                        <div class="col-md-12">
                                                    <input  type="text"
                                                            name="cari_icra_tarixi"
                                                            class="form-control cari_icra_tarixi"
                                                            placeholder="$2616etrafli_cari_icra$"
                                                            disabled
                                                    >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-md-6"> $2616etrafli_yeni_icra$:</label>
                                        <div class="col-md-12 row">
                                            <div class="col-md-9">
                                                <input  type="text"
                                                        class="form-control deadline-datetime"
                                                        name="icra_muddeti_muraciet_olunan_tarix"
                                                        placeholder="$2616etrafli_yeni_icra$"
                                                        data-plugin="date"
                                                >
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-control deadline-days" placeholder="Gün" style="width: 80px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    $(function() {

                                        var deadlineDatetime = $('.deadline-datetime');
                                        var deadlineDays     = $('.deadline-days');

                                        $('.deadline-days').on('keypress', _.debounce(function() {
                                            var days = $(this).val();
                                            var executionStartDate = $('[name=senedin_daxil_olma_tarixi]').val();
                                            if (!days) $(this).val('0');
                                            deadlineDatetime.closest('div').find('.loading').show();
                                            $.post('prodoc/ajax/last_execution.php', {executionStartDate: executionStartDate, executionDaysNum: days}, function (response) {
                                                $('.deadline-datetime').val(response.lastExecutionDate);
                                                deadlineDatetime.closest('div').find('.loading').hide();

                                                if (0 === (+response.nonWorkingDaysNum)) {
                                                    $('.non-working-days-num-container').hide();
                                                } else {
                                                    $('.non-working-days-num').text(response.nonWorkingDaysNum);
                                                    $('.non-working-days-num-container').show();
                                                }
                                            }, 'json');
                                        }, 500));

                                        $('.deadline-datetime').on('change', function() {
                                            var deadlineDatetime = $(this).val();
                                            var executionStartDate = $('[name=senedin_daxil_olma_tarixi]').val();

                                            if (!deadlineDatetime) return;

                                            deadlineDays.closest('div').find('.loading').show();
                                            $.post('prodoc/ajax/last_execution.php', {executionStartDate: executionStartDate, executionDate: deadlineDatetime}, function (response) {
                                                $('.deadline-days').val(response.remainingDaysNum);
                                                deadlineDays.closest('div').find('.loading').hide();


                                                if (0 === (+response.nonWorkingDaysNum)) {
                                                    $('.non-working-days-num-container').hide();
                                                } else {
                                                    $('.non-working-days-num').text(response.nonWorkingDaysNum);
                                                    $('.non-working-days-num-container').show();
                                                }
                                            }, 'json');
                                        });
                                    })
                                </script>
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
                                                   placeholder="Məktubun qısa məzmunu">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-12">$qeyd$</label>
                                        <div class="col-md-12">
                                    <textarea class="form-control" rows="3" placeholder="$qeyd$"
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
                            <strong> $2616qeydiyyat_pencereleri_razilashdirma$ </strong>
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
                                <label class="col-md-12"> $2616rollar_viza$</label>
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

    <script>
        var icra_sexs_deyis = '#tapsirig_emri',
            modal = $("#bosh_modal$MN$");

        $(function () {
            $("#bosh_modal$MN$ .sened_tarix").datepicker({
                autoclose: true,
                format: "dd-mm-yyyy"
            });

            Component.Plugin.PluginManager.init(modal);
            var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

            var docNumber = $(icra_sexs_deyis).find('input[name="document_id"]');

            axtarish(docNumber, {
                getAjaxData: function (t) {
                    var related_document_type = modal.find('[name="related_document_menu"]').val();

                    return {
                        'ne': 'icra_muddeti_document_number',
                        'direction': related_document_type
                    }
                }
            });

            initIcrayaGonder(modal);
        });


        $(icra_sexs_deyis).find('[name="document_id"]').change(function () {
            if(!$(this).select2('data'))
            {
                return;
            }

            var select = $(this);
            var data = select.select2('data');

            var incomingDateTime = data['icra_muddeti'];
            console.log(select);


            modal.find('[name=icra_muddeti_muraciet_olunan_tarix]').datetimepicker('setStartDate', incomingDateTime);
            modal.find('.cari_icra_tarixi').val(incomingDateTime);
            $('#sened-elaveler li:nth-child(1) a').click();
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
            });
        }

        $("#sened-elaveler").find('a[href]').click( function() {

            if(!$(icra_sexs_deyis).find('[name="document_id"]').select2('data'))
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
                'sened_id' : $(icra_sexs_deyis).find('input[name="document_id"]').val(),
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
            input_change = false;

            var fd = new FormData(),
                sehv_var = false;

            $("input[name='sened_fayl[]']").each(function (e,key) {
                fd.append("sened_fayl[]", $(key).val());
            })
            $("input[name='qoshma_fayl[]']").each(function (e,key) {
                fd.append("qoshma_fayl[]", $(key).val());
            })

            if(!sehv_var) {
                Component.Form.send({
                    url: 'prodoc/ajax/icra_muddeti_deyisdirilmesi/add_edit_controller.php',
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
                inf["yoxlayanShexs[]"].forEach(function (yoxlayanShex, index) {
                    if (index > 0) {
                        modal.find("#yoxlayanShexsInput [data-function=action-add]").trigger('click');
                    }

                    var lastIshtrakci = modal.find('[name="yoxlayanShexs[]"]:last');

                    if (yoxlayanShex.text != null) lastIshtrakci.select2('data', yoxlayanShex);
                });

                Component.Form.setData(bm, inf, {
                    ignore: ["yoxlayanShexs[]"]
                });

               $('.deadline-datetime').change();
               $('#sened-elaveler li:nth-child(1) a').click();
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
                    var lastInput = $('[name="document_id"]');
                    Component.Plugin.Plugin.select2AjaxPlugin.setValue(lastInput, senedNomre, {
                        triggerChange: true
                    });
                    $('.whiteboard').eq(0).hide();
                    $('#tapsirig_emri').parents('div').eq(0).addClass('col-md-12').removeClass('col-md-7');
                }
            }
        );





    </script>