<style>
    .gizlet {
        display: none;
        margin-top: 15px;
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
     .col-md-6 .fa-user{
         margin-top: 32px;
     }


</style>
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="assets/css/balloon.css"/>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
<script src="prodoc/asset/js/underscore_mixin.js"></script>
<script src="prodoc/asset/widget/multiple.js"></script>
<script src="prodoc/app.js"></script>
<script type="text/javascript" src="prodoc/asset/widget/hideShow.js"></script>
<script src="assets/scripts/tippy.all.min.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>

<div id="bosh_modal$MN$">

    <div class="modal-body form" style="padding: 0;" id="tapsirig_emri">
        <form action="">
            <input type="hidden" name="id" value="0">
            <input type="hidden" name="poa_user_id" value="$executor$">

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
                                <label for="" class="col-md-12">
                                    $2616sened_tipi_dos$:
                                </label>
                                <div class="col-md-12">
                                    <select data-plugin="select2" class="form-control" name="sened_tip">
                                        <option value="1">$2616qeydiyyat_pencereleri_melumat$</option>
                                        <option value="2">$2616qeydiyyat_pencereleri_icra$</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                $doc_num_input_html$
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-6">
                                    $2616senedin_novu$:
                                </label>
                                <div class="col-md-12">
                                    <input name="sened_novu" class="form-control" data-plugin="select2-ajax" data-plugin-params='{"queryString": {"ne": "senedin_novu"}}'
                                           placeholder="$2616senedin_novu$">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-6">
                                    $2616etrafli_sened_tarix$:
                                </label>
                                <div class="col-md-12">
                                    <input name="senedin_tarixi" data-plugin="date" value="$current_date$" type="text" class="sened_tarix form-control" placeholder="Sənədin tarixi">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row gizlet">
                        <div class="col-md-6" $activSonIcraTarixi$>
                            <div class="form-group">
                                <label for="" class="col-md-6">
                                    $2616etrafli_son_icra_tarix$:
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group input-icon right" style="width: 220px">
                                        <i class="loading fa fa-spinner fa-spin font-custom " style="right: 45px; display: none"></i>
                                        <input type="text" class="form-control deadline-datetime-tarix text-center"
                                               vezife="son_icra_tarixi" name="son_icra_tarixi"
                                               placeholder="Son tarix"
                                               data-plugin="datetime"
                                               autocomplete="off"
                                               $activSonIcraTarixi$>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-icon text-center">
                                        <i class="loading fa fa-spinner fa-spin font-custom" style="display: none"></i>
                                        <input type="text" class="form-control deadline-days-tarix" $activSonIcraTarixi$>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <span class="non-working-days-num-container-last" style="display: none">Qeyri iş günü: <span class="non-working-days-num-last"></span> gün</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" $activTelebOlunanTarixi$>
                            <div class="form-group">
                                <label for="" class="col-md-6">
                                    $TELEB_OLUNAN_TARIX_AD$:
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group input-icon right" style="width: 220px">
                                        <i class="loading fa fa-spinner fa-spin font-custom " style="right: 45px; display: none"></i>
                                        <input type="text" class="form-control deadline-datetime text-center"
                                               vezife="icra_edilme_tarixi" name="icra_edilme_tarixi"
                                               placeholder="Son tarix"
                                               data-plugin="date"
                                               autocomplete="off"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-icon text-center">
                                        <i class="loading fa fa-spinner fa-spin font-custom" style="display: none"></i>
                                        <input type="text" class="form-control deadline-days">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <span class="non-working-days-num-container" style="display: none">Qeyri iş günü: <span class="non-working-days-num"></span> gün</span>
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
                                    <div class="input-group">
                                        <input style="max-width: 508px;" class="form-control" name="qisa_mezmun"
                                               data-plugin-params='{"queryString": {"ne": "qisa_mezmun"}}'
                                               data-plugin="select2-ajax"
                                               placeholder="$2616etrafli_mektub_qm$">

                                        <div $mektubun_qisa_derkenar_metni$ class="input-group-addon addIcon mektubun_qisa_mezmunu">
                                            <i class="fa fa-plus"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="row">
                                <label class="col-md-12">$2616etrafli_qeyd$</label>
                                <div class="col-md-12">
                                    <textarea class="form-control" rows="3" placeholder="$2616etrafli_qeyd$"
                                              name="qeyd" maxlength="1000"></textarea>
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
                            <strong> $2616icraya_gonder$</strong>
                        </h3>
                        <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>
                </div>
                <div class="accordion-block">
                    <div class="form-group xidmeti_mektub">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-md-12" id="rey"> $2616rey_muellifi_dos$ </label>
                                <div class="col-md-12" id="reyInput">
                                    <input name="rey_muellifi"
                                           class="form-control select"
                                           vezife="rey_muellifi" placeholder="Rəy müəllifi">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 icra_uchun clearfix kurator_shexs">
                            <div class="row">
                                <label class="col-md-12"> $kurator$</label>
                                <div class="kuratorShexs">
                                    <div style="top: 11px;" class="col-md-12" id="kuratorInput" data-function="container">
                                        <div onclick="templateYukle('axtarishmodali', 'Əməkdaş seçimi', {'tip': 'kurator_shexs','sened_novu':3,'modal_tipi':'emekdash','class':'kurator_shexs','id':'kuratorInput','name':'kurator[]','ne':'derkenar_icrachilari','extra_emekdash': collectValues(),'cari_emekdash': inputValues('kurator[]')}, 0, true);" class="selectall" style="color:#1c8f5f;height: 18px;width: 13px;margin-left: 101%;margin-top: -29px;">
                                            <i style="margin-top: 29px;" class="fa fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group xidmeti_mektub clearfix">
                        <div class="col-md-6 icra_uchun icraci_shexs" style="float: right">
                            <div class="row">
                                <label style="top: 10px;" class="col-md-12"> $mesul_shexs$</label>
                                <div class="incoming-document">
                                    <div style="top: 20px" class="col-md-12" id="mesul_shexsInput" data-function="container">
                                        <div onclick="templateYukle('axtarishmodali', 'Əməkdaş seçimi', {'tip': 'icraci_shexs','sened_novu':3,'modal_tipi':'emekdash','class':'icraci_shexs','id':'mesul_shexsInput','name':'mesul_shexs[]','ne':'derkenar_icrachilari','extra_emekdash': collectValues(),'cari_emekdash': inputValues('mesul_shexs[]')}, 0, true);" class="selectall" style="color:#1c8f5f;height: 18px;width: 13px;margin-left: 101%;margin-top: -29px;">
                                            <i style="margin-top: 29px;" class="fa fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 icra_uchun ishtirakchi_shexs clearfix">
                            <div class="row">
                                <label class="col-md-12"> $ishtirakchi$</label>
                                <div class="ishtirakchi">
                                    <div style="top: 9px;" class="col-md-12" id="ishtirakchiInput" data-function="container">
                                        <div onclick="templateYukle('axtarishmodali', 'Əməkdaş seçimi', {'tip': 'ishtirakchi_shexs','sened_novu':3,'modal_tipi':'group','class':'ishtirakchi_shexs','id':'ishtirakchiInput','name':'ishtirakchi[]','ne':'ishtirakchi_shexsler','extra_emekdash': collectValues(),'cari_emekdash': inputValues('ishtirakchi[]')}, 0, true);" class="selectall" style="color:#1c8f5f;height: 18px;width: 13px;margin-left: 101%;margin-top: -29px;">
                                            <i style="margin-top: 29px;" class="fa fa-user"></i>
                                        </div>
                                    </div>
                                </div>`
                            </div>
                        </div>
                        <div class="col-md-6 melumat_uchun">
                            <div class="row">
                                <label class="col-md-12"> $2616qeydiyyat_penceleri_melumatlandirlan$:</label>
                                <div class="melumat">
                                    <div class="col-md-12" id="melumatInput" style="margin-top: 11px" data-function="container">
                                        <div onclick="templateYukle('axtarishmodali', 'Əməkdaş seçimi', {'tip': 'ishtirakchi_shexs','sened_novu':3,'modal_tipi':'group','class':'melumat','id':'melumatInput','name':'melumat[]','ne':'melumat_shexsler','extra_emekdash': collectValues(),'cari_emekdash': inputValues('melumat[]')}, 0, true);" class="selectall" style="color:#1c8f5f;height: 18px;width: 13px;margin-left: 101%;margin-top: -29px;">
                                            <i class="fa fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="form-group clearfix">
                        <div class="col-md-6 kime" style="display: none;">
                            <div class="row">
                                <label class="col-md-12"> $2616rollar_kime$</label>
                                <div class="col-md-12">
                                    <input name="kime"
                                           data-plugin-params='{"queryString": {"ne": "emekdash"}}'
                                           data-plugin="select2-ajax"
                                           class="form-control"
                                           vezife="kime" placeholder="$2616rollar_kime$">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="margin-left: 1px;">
                            <div class="row">
                                <label class="col-md-12"> $2616rollar_viza$</label>
                                <div class="yoxlayanShexs">
                                    <div class="col-md-12" id="yoxlayanShexsInput" data-function="container">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 xidmeti_mektub" style="margin-left: -1px;">
                            <div class="row">
                                <label class="col-md-12">$2616etrafli_derkenar_metn$</label>
                                <div class="col-md-12">
                                    <div class="input-group" style="width: 100% !important;">
                                        <input style="max-width: 508px;" name="derkenar_metn_id" id="derkenar_metn_slct1"
                                               class="form-control derkenar_metn_select"
                                               data-plugin="select2-ajax"
                                               data-plugin-params='{"queryString": {"ne": "derkenar_metnler"}}'
                                               placeholder="$2616etrafli_derkenar_metn$ ...">
                                        <div $mektubun_qisa_derkenar_metni$ class="input-group-addon addIcon qeydiyyat_derkenar derkenar_metni">
                                            <i class="fa fa-plus"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-md-12">$2616qeydiyyat_pencereleri_imzali$</label>
                                <div class="col-md-1" style="padding-top: 7px;">
                                    <input type="checkbox" data-plugin="uniform" name="imzali">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/x-underscore-template" id="kurator-id-select">
                <div class="input-group" data-function="item">
                    <input name="kurator[]"
                           class="form-control select"

                           vezife="kurator" placeholder="$kurator$">
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

            <script type="text/x-underscore-template" id="ishtirakchi-id-select">
                <div class="input-group" data-function="item">
                    <input name="ishtirakchi[]"
                           class="form-control select"

                           vezife="ishtirakchi" placeholder="$ishtirakchi$">
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
            <script type="text/x-underscore-template" id="melumat-id-select">
                <div class="input-group" data-function="item">
                    <input name="melumat[]"
                           class="form-control select"

                           vezife="melumat" placeholder="$2616qeydiyyat_penceleri_melumatlandirlan$">
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

            <script type="text/x-underscore-template" id="mesul_shexs-id-select">
                <div class="input-group" data-function="item">
                    <input name="mesul_shexs[]"
                           class="form-control select"

                           vezife="mesul_shexs" placeholder="$mesul_shexs$">
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

            <script type="text/x-underscore-template" id="yoxlayanShexs-id-select">
                <div class="input-group" data-function="item">
                    <input name="yoxlayanShexs[]"
                           class="form-control select selectViza"

                           vezife="yoxlayanShexs" placeholder="Visa/Razılaşdıran şəxs">
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
    </div>

    <div class="modal-footer" style="border-top: 0;">
        <div style="float: left; color: red;" vezife="error"></div>
        <div>
            <button type="button" data-v="testiqle" class="btn green save btn-circle">$2616icraya_gonder$</button>
        </div>
    </div>
</div>

<script type="text/javascript" src="prodoc/asset/js/underscore.string.min.js"></script>
<script type="text/javascript" src="prodoc/asset/js/underscore_mixin.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="prodoc/modules/daxili_senedler/appendGeneralData.js"></script>
<script src="prodoc/asset/widget/fileUpload.js"></script>
<script src="prodoc/app.js"></script>

<script>


    $(function () {

        $('#daxili_sened').removeClass('col-md-12').addClass('col-md-7');

        var modal = $("#bosh_modal$MN$");

        Component.Plugin.PluginManager.init(modal);
        var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

        modal.find('.save').on('click', function() {
            input_change = false;

            var fd   = new FormData(),
            sehv_var = false;

            $("input[name='sened_fayl[]']").each(function (e,key) {
                fd.append("sened_fayl[]", $(key).val());
            })
            $("input[name='qoshma_fayl[]']").each(function (e,key) {
                fd.append("qoshma_fayl[]", $(key).val());
            })

            if (
                modal.find('[name="sened_tip"]').val() == 2  &&
                modal.find('[name=icra_edilme_tarixi]').is(':visible') &&
                modal.find('[name=icra_edilme_tarixi]').val() == "" &&
                +'$gunTelebOlunanTarixiVacibSahe$'
                )
            {
                sehv_var = true;
                modal.find('input[name=icra_edilme_tarixi]').css("cssText", "border: red dashed 1px !important");
            }
            else
            {
                modal.find('input[name=icra_edilme_tarixi]').css("border", "");
            }

            if(!sehv_var) {
                Component.Form.send({
                    url: 'prodoc/ajax/umumi_forma/add_edit_controller.php',
                    sendUncheckedCheckbox: true,
                    existingFormData: fd,
                    form: modal,
                    success: function (res) {
                        proccessResponse(res);
                    }
                });
            }
        });

        modal.on('click','.document-info-button',function () {
            etrafliSenedId = $(this).parent('td').find('input[name="related_document_id[]"]').select2('data')['id'];
            $("#sened-elaveler").find('a[href]:first').trigger('click');
        })

        initIcrayaGonder(modal);

        axtarish($('[name="rey_muellifi"]'), {
            getAjaxData: function () {
                return {
                    'ne': 'rey_muellifleri',
                    'tip': 'daxili',
                    'extra_emekdash': collectValues()
                }

            }
        });

        var deadlineSonIcraTarixi = modal.find('.deadline-datetime-tarix'),
            deadlineTelebOlunanTarix = modal.find('.deadline-datetime'),
            deadlineDays     = modal.find('.deadline-days');

        var SonIcraTarixiDayContainer    = modal.find('.non-working-days-num-container'),
            SonIcraTarixiDay             = modal.find('.non-working-days-num'),
            TelebOlunanTarixDayContainer = modal.find('.non-working-days-num-container-last'),
            TelebOlunanTarixDay          = modal.find('.non-working-days-num-last');

        modal.find('[name=son_icra_tarixi]').on('change', function() {
            if (!$(this).is(':checked')) {
                return;
            }

            modal.find('.deadline-days').val(15).trigger('keypress');
        });

        function deadlineDataTime(name, time, container, day) {
            modal.find(name).on('keydown', _.debounce(function () {
                var days = $(this).val();

                var executionStartDate = modal.find('[name=senedin_tarixi]').val();

                if (!days) {
                    $(this).val('');
                    $('[name=icra_edilme_tarixi]').val('');
                    $('.non-working-days-num-container').text('');
                    return;
                }

                time.closest('div').find('.loading').show();
                $.post('prodoc/ajax/last_execution.php', {executionStartDate: executionStartDate, executionDaysNum: days, tip: 3}, function (response) {
                    modal.find(time).val(response.lastExecutionDate);
                    time.closest('div').find('.loading').hide();

                    if (0 === (+response.nonWorkingDaysNum)) {
                        container.hide();
                    } else {
                        day.text(response.nonWorkingDaysNum);
                        container.show();
                    }
                }, 'json');
            }, 500));
        }

        deadlineDataTime('.deadline-days', deadlineTelebOlunanTarix, SonIcraTarixiDayContainer, SonIcraTarixiDay);
        deadlineDataTime('.deadline-days-tarix', deadlineSonIcraTarixi, TelebOlunanTarixDayContainer, TelebOlunanTarixDay);

        deadlineDays.inputmask('999', {"placeholder": ""});

        function deadlineDataDay(name, time, container, day) {
            modal.find(name).on('change', function () {
                var deadlineDatetime = $(this).val();
                var executionStartDate = modal.find('[name=senedin_tarixi]').val();

                if (!deadlineDatetime) return;

                deadlineDays.closest('div').find('.loading').show();
                $.post('prodoc/ajax/last_execution.php', {executionStartDate: executionStartDate, executionDate: deadlineDatetime, tip: 3}, function (response) {
                    modal.find(time).val(response.remainingDaysNum);
                    deadlineDays.closest('div').find('.loading').hide();


                    if (0 === (+response.nonWorkingDaysNum)) {
                        container.hide();
                    } else {
                        day.text(response.nonWorkingDaysNum);
                        container.show();
                    }
                }, 'json');
            });
        }

        deadlineDataDay(deadlineTelebOlunanTarix, '.deadline-days', SonIcraTarixiDayContainer, SonIcraTarixiDay);
        deadlineDataDay(deadlineSonIcraTarixi, '.deadline-days-tarix', TelebOlunanTarixDayContainer, TelebOlunanTarixDay);

        modal.find('[name=senedin_tarixi]').on('change', function() {
            var incomingDateTime = $(this).data('datepicker').getDate();
            var deadlineInput = modal.find('[name=icra_edilme_tarixi]');
            deadlineInput.datetimepicker('setStartDate', incomingDateTime);

            var deadlineInputSonIcraTarixi = modal.find('[name=son_icra_tarixi]');
            deadlineInputSonIcraTarixi.datetimepicker('setStartDate', incomingDateTime);

            var deadlineDays = modal.find('.deadline-days').val();
            var gunTelebOlunanTarixi = '$gunTelebOlunanTarixi$';
            var gunSonIcraTarixi = '$gunSonIcraTarixi$';

            if(deadlineDays == 0)
            {
                deadlineDays = gunTelebOlunanTarixi;
            }

            modal.find('.deadline-days').val(deadlineDays).trigger('keydown');
            modal.find('.deadline-days-tarix').val(gunSonIcraTarixi).trigger('keydown');
        });

        function showXidmetMektub() {
            modal.find(".xidmeti_mektub").show();
            modal.find(".kime").hide();
            modal.find('[name="sened_tip"]').removeAttr("disabled");
        }

        modal.find("input[name=sened_novu]").change(function () {

            if(!$(this).select2("data")){
                showXidmetMektub();
                return;
            }

            var xidmetiMektub = $(this).select2("data")['key'];
            if(xidmetiMektub == 'xidmeti_mektub')
            {
                modal.find(".xidmeti_mektub").hide();
                modal.find(".kime").show();
                modal.find('[name="sened_tip"]').val("1").change().attr("disabled", "disabled");
            }
            else
            {
                showXidmetMektub();
            }
        });

    });


    function initIcrayaGonder(container) {
        $( document ).ready(function() {
            function showAbbrClose(el) {
                if ($(el).find('[data-function=item]').length < 2) {
                    $(el).find(".select2-container .select2-choice abbr").css("display", "inline-block")
                    $(el).find(".select2-container .select2-choice abbr").on("click",function(){
                        $(this).css("display", "none")
                    })
                    $(el).find(".select").on("change",function(){
                        $(el).find(".select2-container .select2-choice abbr").css("display", "inline-block")
                    })
                }
            }

            function hideAbbrClose(el) {
                $(el).find(".select2-container .select2-choice abbr").css("display", "none")
            }

            container.find(".kuratorShexs").multiple({
                itemTemplateId: 'kurator-id-select',
                initialItem: true,
                prepend: true,
                beforeAppend: function (item, e, extra) {

                    return CheckForEmptyInput(".kuratorShexs", "kurator") || extra.isFirst;
                },
                afterAppend: function (item) {
                    Component.Plugin.PluginManager.init(item);
                    var kurator = item.find('input[name="kurator[]"]');
                    var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);
                    axtarish(kurator, {
                        allowClear: false,
                        getAjaxData: function (t) {
                            return {
                                'ne': 'derkenar_icrachilari',
                                'sened_tipi': 'kurator_shexs',
                                'sened_novu': 3,
                                'extra_emekdash': collectValues()
                            }
                        }
                    });
                    getMultiSelectValues("#kuratorInput")
                }
            });

            container.find(".ishtirakchi").multiple({
                itemTemplateId: 'ishtirakchi-id-select',
                initialItem: true,
                prepend: true,
                beforeAppend: function (item, e, extra) {
                    return CheckForEmptyInput(".ishtirakchi", "ishtirakchi") || extra.isFirst;
                },
                afterAppend: function (item) {
                    var kurator = item.find('input[name="ishtirakchi[]"]');
                    var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

                    axtarish(kurator, {
                        allowClear: false,
                        getAjaxData: function (t) {
                            return {
                                'ne': 'ishtirakchi_shexsler',
                                'sened_tipi': 'ishtirakchi_shexs',
                                'sened_novu': 3,
                                'extra_emekdash': collectValues()
                            }
                        }
                    });
                    getMultiSelectValues("#ishtirakchiInput")

                }
            });
            container.find(".melumat").multiple({
                itemTemplateId: 'melumat-id-select',
                initialItem: true,
                prepend: true,
                beforeAppend: function (item, e, extra) {
                    return CheckForEmptyInput(".melumat", "melumat") || extra.isFirst;
                },
                afterAppend: function (item) {
                    var kurator = item.find('input[name="melumat[]"]');
                    var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

                    axtarish(kurator, {
                        allowClear: false,
                        getAjaxData: function (t) {
                            return {
                                'ne': 'melumat_shexsler',
                                'sened_novu': 3,
                                'extra_emekdash': collectValues()
                            }
                        }
                    });
                    getMultiSelectValues("#melumatInput")

                }
            });

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
                                'extra_emekdash': collectValues('selectViza')
                            }
                        }
                    });
                    getMultiSelectValues("#yoxlayanShexsInput")
                }
            });

            container.find(".incoming-document").multiple({
                itemTemplateId: 'mesul_shexs-id-select',
                initialItem: true,
                prepend: true,
                beforeAppend: function (item, e, extra) {
                    return CheckForEmptyInput(".incoming-document", "mesul_shexs") || extra.isFirst;
                },
                afterAppend: function (item) {
                    Component.Plugin.PluginManager.init(item);
                    var kurator = item.find('input[name="mesul_shexs[]"]');
                    var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);
                    axtarish(kurator, {
                        allowClear: false,
                        getAjaxData: function (t) {
                            return {
                                'ne': 'derkenar_icrachilari',
                                'sened_tipi': 'icraci_shexs',
                                'sened_novu': 3,
                                'extra_emekdash': collectValues()
                            }
                        }
                    });
                    getMultiSelectValues("#mesul_shexsInput")

                }
            });
        });
    }



    function fadeInOut(this_e, e) {
        if (this_e.is(':checked')) {
            e.fadeIn(300);
        } else {
            e.fadeOut(300);
        }
    }
    console.log($inf$);
    $(function () {
        var inf = $inf$,
            bm = $('#tapsirig_emri');


        if (!_.isNull(inf)) {


            $(function () {
                if(!_.isEmpty(inf.sened_nov_xidmeti_mektub))
                {
                    bm.find(".xidmeti_mektub").hide();
                    bm.find(".kime").show();
                    bm.find('[name="sened_tip"]').val("1").change().attr("disabled", "disabled");

                    inf["kime"].forEach(function (kimeler) {
                        var lastKurator = bm.find('[name="kime"]');
                        if(kimeler.text != null) lastKurator.select2('data', kimeler);
                    });
                }
            });
            if ("kurator[]" in inf) {
                inf["kurator[]"].forEach(function (kuratorlar, index) {

                    if (index > 0) {
                        bm.find("#kuratorInput [data-function=action-add]").trigger('click');
                    }

                    var lastKurator = bm.find('[name="kurator[]"]:last');

                    if (kuratorlar.text != null) lastKurator.select2('data', kuratorlar);
                });
            }
            if ("ishtirakchi[]" in inf) {
                inf["ishtirakchi[]"].forEach(function (ishrakchilar, index) {
                    if (index > 0) {
                        bm.find("#ishtirakchiInput [data-function=action-add]").trigger('click');
                    }

                    var lastIshtrakci = bm.find('[name="ishtirakchi[]"]:last');

                    if (ishrakchilar.text != null) lastIshtrakci.select2('data', ishrakchilar);
                });
            }
            if ("melumat[]" in inf) {

                inf["melumat[]"].forEach(function (ishrakchilar, index) {
                    if (index > 0) {
                        bm.find("#melumatInput  [data-function=action-add]").trigger('click');
                    }

                    var lastIshtrakci = bm.find('[name="melumat[]"]:last');

                    if (ishrakchilar.text != null) lastIshtrakci.select2('data', ishrakchilar);
                });
            }
            if ("mesul_shexs[]" in inf) {
                inf["mesul_shexs[]"].forEach(function (mesulshexler, index) {
                    if (index > 0) {
                        bm.find("#mesul_shexsInput [data-function=action-add]").trigger('click');
                    }

                    var lastKurator = bm.find('[name="mesul_shexs[]"]:last');
                    if (mesulshexler.text != null) lastKurator.select2('data', mesulshexler);
                });
            }
            if ("yoxlayanShexs[]" in inf) {
                inf["yoxlayanShexs[]"].forEach(function (yoxlayanShexsler, index) {
                    if (index > 0) {
                        bm.find("#yoxlayanShexsInput [data-function=action-add]").trigger('click');
                    }

                    var lastKurator = bm.find('[name="yoxlayanShexs[]"]:last');
                    if (yoxlayanShexsler.text != null) lastKurator.select2('data', yoxlayanShexsler);
                });
            }
            if ("rey_muellifi" in inf) {
                inf["rey_muellifi"].forEach(function (rey_muellifiler) {
                    var lastKurator = bm.find('[name="rey_muellifi"]');
                    if (rey_muellifiler.text != null) lastKurator.select2('data', rey_muellifiler);
                });
            }

            if(!_.isEmpty(inf.icra_edilme_tarixi))
            {
                $('input[name="son_icra_tarixi"]').trigger('click');
                bm.find('.deadline-datetime').val(inf.icra_edilme_tarixi);
                bm.find('.deadline-datetime').change();
            }

            if(!_.isEmpty(inf.son_icra_tarixi))
            {
                bm.find('.deadline-datetime-tarix').val(inf.son_icra_tarixi);
                bm.find('.deadline-datetime-tarix').change();
            }

            bm.find('[name="sened_tip"]').val(inf.sened_tipi).change();

            Component.Form.setData(bm, inf, {
                ignore: ["kurator[]", "ishtirakchi[]", "rey_muellifi", "yoxlayanShexs[]", "mesul_shexs[]","melumat[]"]
            });
        }
        else
        {
            if (!_.isEmpty('$gunSonIcraTarixi$')) {
                $('.deadline-days-tarix').val('$gunSonIcraTarixi$').trigger('keydown');
            }

            if (!_.isEmpty('$gunTelebOlunanTarixi$')) {
                $('.deadline-days').val('$gunTelebOlunanTarixi$').trigger('keydown');
            }
        }
    })
    fadeinOuter($('[name="sened_tip"]'));
    $('[name="sened_tip"]').on("change", function () {
        fadeinOuter($(this));
    });
    function fadeinOuter(value) {

        var senedin_tipi = value.val();
        if (senedin_tipi==2){
            $('.gizlet').fadeIn(300);
            $('.icra_uchun').fadeIn(300);
            $('.melumat_uchun').fadeOut(300);

        }
        else{
            $('.gizlet').fadeOut(300);
            $('.icra_uchun').fadeOut(300);
            $('.melumat_uchun').fadeIn(300);

        }


    }

    function collectValues(name = 'select:not(.selectViza)') {
        var arr = [];
        $('.'+name).each(function () {
            var value =$(this).val();
            var myarr = value.split("_");
            if (myarr[0]+"_"+myarr[1]==value){
                value=myarr[0];
            }
            if (value != '') {
                arr.push(value)
            }

        })
        return arr;
    }

    var executor = JSON.parse('$executor$');

    if(executor.length == 1)
    {
        $('input[name="rey_muellifi"]').select2('data', executor[0]);
        $('input[name="rey_muellifi"]').select2('readonly', true);
    }

    var incomingDateTime = $('[name=senedin_tarixi]').data('datepicker').getDate();
    var deadlineInput = $('[name=icra_edilme_tarixi]');
    deadlineInput.datetimepicker('setStartDate', incomingDateTime);


    // Modal fast click opens two modals problem solution with unbinding click then binding click again
    // after some time like .5 milliseconds
    function showTemplate(selector,template,bashliq,parametrler={},uzunluq=0,target_new=true) {
        $(selector).unbind('click');

        templateYukle(template,bashliq,parametrler,uzunluq,target_new);

        setTimeout(function() {
            $(selector).on('click', function(){
                showTemplate(selector,template,bashliq,parametrler,uzunluq,target_new);
            });
        }, 500);
    }
    // Məktubun qısa məzmunu
    $('.mektubun_qisa_mezmunu').on('click', function(){
        showTemplate('.mektubun_qisa_mezmunu','mektubun_qisa_mezmunu', 'Məktubun qısa məzmunu', {'mektubun_qisa_mezmunu':1}, 0, true);
    });
    // Dərkənarın mətni
    $('.derkenar_metni').on('click', function(){
        showTemplate('.derkenar_metni','derkenar_metni', 'Dərkənarın mətni', {'tip': 'derkenar_metni'}, 0, true);
    });
</script>