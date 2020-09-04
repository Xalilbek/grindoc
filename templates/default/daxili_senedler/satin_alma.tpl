<style>
    .gizlet {
        display: none;
        margin-top: 10px;
    }
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


    .side_body_nav li:nth-child(2){
        display: none;
    }

    .whiteboard
    {
        max-width: none;
    }

    .tab_menu_shower
    {
        margin-right: 21px;
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

    <div class="modal-body form" style="padding: 0;" id="satin_alma">
        <form action="">
            <input type="hidden" name="id" value="0">
            <div class="whiteboard">
                <div class="col-md-12">
                    <div class="blockname">
                        <h3 class="text-success text-left">
                            <strong>$2616qeydiyyat_pencereleri_sifarish$</strong>
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
                                    $2616etrafli_sifarish_tarix$:
                                </label>
                                <div class="col-md-12">
                                    <input name="sifaris_tarixi" value="$current_date$" type="text" class="sened_tarix form-control" placeholder="Sənədin tarixi" readonly="readonly">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    $2616qeydiyyat_pencereleri_sifarishci$:
                                </label>
                                <div class="col-md-12">
                                    <input name="sifarisci" value="$userAdi$" type="text" class="form-control" readonly="readonly">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    $2616qeydiyyat_pencereleri_sifarishchi_shobesi$:
                                </label>
                                <div class="col-md-12">
                                    <input name="sifarisler" value="$userStruktur$" type="text" class="form-control" readonly="readonly">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-6">
                                    $2616etrafli_sifarish_tip$:
                                </label>
                                <div class="col-md-12">
                                    <input name="sifaris_tipi" class="form-control" data-plugin="select2-ajax" data-plugin-params='{"queryString": {"ne": "sifaris_tipi"}}'
                                           placeholder="Sifariş tipi">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-6">
                                    $2616etrafli_sened_tarix$:
                                </label>
                                <div class="col-md-12">
                                    <input name="senedin_tarixi" value="$current_date$" data-plugin="date" type="text" class="sened_tarix form-control" placeholder="Sənədin tarixi">
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
                            <strong>$2616qeydiyyat_pencereleri_sifarish_xidmet$</strong>
                        </h3>
                        <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>
                </div>

                <div class="accordion-block">
                    <div class="form-group">
                        <div class="col-md-12 table-responsive" style="padding-left: 10px; border-left: 0px; padding-right: 0px;">
                            <table class="table table-bordered table-advance table-hover" style="background: #FFF;">
                                <thead>
                                <tr>
                                    <th style="width: 10px">№</th>
                                    <th style="width: 100px">$2616qeydiyyat_pencereleri_mal_kodu$</th>
                                    <th style="min-width: 237px">$2616qeydiyyat_pencereleri_mal_ad$</th>
                                    <th style="width: 100px">$2616qeydiyyat_pencereleri_olcu_vahidi$</th>
                                    <th style="width: 200px">$2616qeydiyyat_pencereleri_miqdar$</th>
                                    <th style="width: 130px">$2616qeydiyyat_pencereleri_vahid_deyer$</th>
                                    <th style="width: 130px">$2616qeydiyyat_pencereleri_mebleq_cem$</th>
                                    <th style="width: 159px">$2616qeydiyyat_pencereleri_icra_muddeti$</th>
                                    <th style="width: 150px">$2616qeydiyyat_pencereleri_shekil$</th>
                                    <th>
                                        <button type="button" class="btn btn-sm green addNewLine">
                                            <i class="icon-plus"></i> Əlavə et
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="satishTable">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="whiteboard">
                <div class="col-md-12">
                    <div class="blockname">
                        <h3 class="text-success text-left">
                            <strong>$2616qeydiyyat_penceleri_senedin_mezmunu$</strong>
                        </h3>
                        <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>
                </div>
                <div class="accordion-block">
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

            <script type="text/template" id="product_template">
                <tr>
                    <td class="order"></td>
                    <td class="code">
                        <input type="text" class="form-control" name="malin_kodu[]" style="width: 70px;" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="12">
                    </td>
                    <td class="mal_adi">
                        <input type="text" class="form-control" name="mal_adi[]">
                    </td>
                    <td>
                        <input name="olcu_vahidi[]" class="form-control" data-plugin="select2-ajax" data-plugin-params='{"queryString": {"ne": "olcu_vahidi"}}'
                               placeholder="Ölcü vahidi" style="width: 90px">
                    </td>
                    <td class="price">
                        <div class="spinner2">
                            <div class="input-group">
                                <input type="text" style="width: 80px" class="spinner-input form-control" name="miqdar[]" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="12">
                            </div>
                        </div>
                    </td>
                    <td class="mebleq">
                        <div class="input-group">
                            <input style="width: 90px" type="text" class="spinner-input form-control" name="mebleq[]" disabled>
                        </div>
                    </td>
                    <td class="mebleqin_cemi">
                        <div class="input-group">
                            <input style="width: 90px" type="text" class="spinner-input form-control" name="mebleqin_cemi[]" disabled>
                        </div>
                    </td>
                    <td class="gun">
                        <div class="input-group">
                            <input style="width: 115px" type="text" class="spinner-input form-control" name="gun[]" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="12">
                        </div>
                    </td>
                    <td style="word-wrap: break-word;">
                        <div class="file-upload" data-name-pattern="document_%s[]">
                            <div class="add-file-btn">
                                <button type="button" class="btn btn-link font-dark" style="padding: 6px;">
                                    <span style="font-weight: 500;">$2616qeydiyyat_pencereleri_sened_elave$</span>
                                </button>
                            </div>
                            <div class="list-of-files">
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn red btn-sm delete_row"><i class="icon-trash"></i> sil</button>
                    </td>
                </tr>
            </script>

        </form>
    </div>

    <div class="modal-footer" style="border-top: 0;">
        <div style="float: left; color: red;" vezife="error"></div>
        <div style="float: right;">
            <button type="button" data-v="testiqle" class="btn green save btn-circle">$2616icraya_gonder$</button>
        </div>
    </div>
</div>
<script src="prodoc/modules/daxili_senedler/appendGeneralData.js"></script>
<script src="prodoc/asset/widget/fileUpload.js"></script>
<script type="text/javascript" src="prodoc/asset/js/underscore.string.min.js"></script>
<script type="text/javascript" src="prodoc/asset/js/underscore_mixin.js"></script>

<script>

    $(function () {
        $('#daxili_sened').removeClass('col-md-7').addClass('col-md-12');
        $('.tab_menu_shower').removeClass('col-md-3').addClass('col-md-5');

        var modal = $("#bosh_modal$MN$");
        Component.Plugin.PluginManager.init(modal);

        $(".addNewLine").on('click', function () {
            appendProduct();
        });

        function appendProduct() {

            var productTemplate = $("#product_template").html();
            modal.find('#satishTable').append(productTemplate);
            Component.Plugin.PluginManager.init(modal.find('#satishTable tr:last'));
            $('#satishTable tr:last').find('.file-upload').fileUpload({
                name: 'will_be_set'
            });

            order();
        }

        modal.find('#satishTable').on( 'click' , ".delete_row" , function() {
            $(this).closest('tr').remove();
        });

        function setFileInputIndexes()
        {
            modal.find('.list-of-files .file input[type="file"]').each(function() {
                var index = $(this).closest('#satishTable tr').index();
                var namePattern = $(this).closest('[data-name-pattern]').attr('data-name-pattern');

                $(this).attr('name', s.sprintf(namePattern, index));
            });
        }

        modal.find('.save').on('click', function() {
            input_change = false
            setFileInputIndexes();
            var sehv_var = false;

            if(modal.find('#satishTable tr').length == 0)
            {
                $(".addNewLine").trigger('click');
            }

            modal.find('#satishTable tr').each(function () {
                var malin_adi   = $(this).find('td').eq(2).find('input'),
                    olcu_vahidi = $(this).find('td').eq(3).find('input'),
                    miqdar      = $(this).find('td').eq(4).find('input');

                if(_.isEmpty(malin_adi.val()))
                {
                    sehv_var = true;
                    malin_adi.css("cssText", "border: red dashed 1px !important");
                }
                else
                {
                    malin_adi.css("border", "");
                }

                if(_.isEmpty(olcu_vahidi.select2('val')))
                {
                    sehv_var = true;
                    olcu_vahidi.prev('div').css("cssText", "border: red dashed 1px !important");
                }
                else
                {
                    olcu_vahidi.prev('div').css("border", "");
                }

                if(_.isEmpty(miqdar.val()))
                {
                    sehv_var = true;
                    miqdar.css("cssText", "border: red dashed 1px !important");
                }
                else
                {
                    miqdar.css("border", "");
                }

            });

            if(!sehv_var)
            {
                Component.Form.send({
                    url: 'prodoc/ajax/emeliyyatlar/satin_alma.php',
                    sendUncheckedCheckbox: true,
                    checkboxCheckedValue: 1,
                    checkboxUncheckedValue: 0,
                    useDefaultFormData: false,
                    form: modal,
                    success: function (res) {
                        var errorsListContainer = $('.errors-list-container');
                        var errorsList = $('.errors-list');
                        res = JSON.parse(res);

                        if (res.status === "error") {
                            errorsList.html('');
                            if (res.error_msg) {
                                res.errors = [res.error_msg];
                            }

                            res.errors.forEach(function (error) {
                                errorsList.append('<span>' + error + '</span><br>')
                            });
                            $('.scroll-to-top').trigger('click');
                            errorsListContainer.slideDown();
                            toastr.error('Səhv var!');
                        } else {
                            errorsListContainer.slideUp();
                            toastr.success('Sənəd uğurla yadda saxlanıldı!');

                            if (!res.id) {
                                return;
                            }

                            //index.php?module=prodoc_new&doc_type=' + doc_type
                            location.href = 'index.php?module=prodoc_new&filter=satin_alma&doc_type=satin_alma&id='+res.id+'&bolme=prodoc_sened_qeydiyyatdan_kecib';
                        }

                        // proccessResponse(res);
                    }
                });
            }

        });

        function order() {
            var count = 0;
            $('#satishTable tr').each(function () {
                $(this).find('td:eq(0)').text(++count);
            });
        }
    });

    $(function () {
        var inf = $inf$,
            bm = $('#satin_alma');

        console.log(inf);

        if (!_.isNull(inf)) {

            if (!_.isEmpty(inf['satishTable[]']))
            {
                _.each(inf["satishTable[]"],function (element) {
                    bm.find(".addNewLine").trigger('click');
                    var tr = bm.find('tbody tr').last();
                    tr.find('[name="malin_kodu[]"]').val(element.malin_kodu);
                    tr.find('[name="mal_adi[]"]').val(element.mal_adi);
                    tr.find('[name="olcu_vahidi[]"]').select2('data', element.olcu_vahidi);
                    tr.find('[name="miqdar[]"]').val(element.miqdar);
                    tr.find('[name="mebleq[]"]').val(element.mebleq);
                    tr.find('[name="gun[]"]').val(element.gun);
                });
            }
            Component.Form.setData(bm, inf);
        }

    });

</script>