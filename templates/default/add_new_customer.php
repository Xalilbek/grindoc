<link rel="stylesheet" type="text/css" href="asset/global/plugins/bootstrap-datepicker1/css/bootstrap-datepicker.min.css?v=2">
<style>
    .probtn{
        background: #1C8F5F !important;
        border-color: #1C8F5F !important;
    }
    .form .form-bordered .form-group,
    .form .form-bordered .form-group > div{
        border: 0;
    }
    .modal-header{
        height:50px;
    }
    .modal-dialog{
        overflow: hidden;
        border-radius:25px !important;
    }
</style>


<div class="modal-body" style="padding: 0">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue" id="form_wizard_1" style="margin: 0;margin-top: 15px;">
                <!--                <div class="portlet-title" style="background: #5bc0de;">-->
                <!--                    <div class="caption">-->
                <!--                        <span head="er"></span><span class="step-title">--><?//= dil::soz( "11step12" ); ?><!--</span>-->
                <!--                    </div>-->
                <!--                </div>-->
                <div class="portlet-body form">
                    <form action="#" class="form-horizontal form-bordered" id="submit_form">
                        <div class="form-wizard" id="user_creat">
                            <div class="form-body">
                                <ul style="display: none" class="nav nav-pills nav-justified steps">
                                    <li>
                                        <a href="#tab1" data-toggle="tab" class="step active">
                                            <span class="number">1</span>
                                            <span class="desc"><i class="fa fa-check"></i> <?= dil::soz( "11informationSetup" ); ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab4" data-toggle="tab" class="step">
                                            <span class="number">2</span>
                                            <span class="desc"><i class="fa fa-check"></i> <?= dil::soz( "11Confirm" ); ?></span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content" style="border-top:1px solid #EFEFEF;">
                                    <div class="alert alert-danger display-none">
                                        <button class="close" data-dismiss="alert"></button>
                                        <?= dil::soz( "11error1" ); ?>
                                    </div>
                                    <div class="alert alert-success display-none">
                                        <button class="close" data-dismiss="alert"></button>
                                        <?= dil::soz( "11error2" ); ?>
                                    </div>

                                    <div class="tab-pane" id="tab1">
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?= dil::soz( "11adi" ); ?>
                                                <span class="required">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="firstname"/>
                                                <span class="help-block"><?= dil::soz( "11adiError" ); ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?= dil::soz( "11soyadi" ); ?>
                                                <span class="required">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="lastname"/>
                                                <span class="help-block"><?= dil::soz( "11soyadiError" ); ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?= dil::soz( "11ataadi" ); ?></label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="fathername"/>
                                            </div>
                                        </div>
                                        <div style="display: none" class="form-group">
                                            <label class="control-label col-md-3"></label>
                                            <div class="col-md-8">
                                                <div class="radio-list">
                                                    <label>
                                                        <input type="radio" name="gender" data-plugin="uniform" value="M" data-title="Male"/>
                                                        <?= dil::soz( "11Male" ); ?>
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="gender" data-plugin="uniform" value="F" data-title="Female"/>
                                                        <?= dil::soz( "11Female" ); ?>
                                                    </label>
                                                </div>
                                                <div id="form_gender_error"></div>
                                            </div>
                                        </div>
                                        <div style="display: none" class="form-group">
                                            <label class="control-label col-md-3"><?= dil::soz( "11mushteriTipi" ); ?></label>
                                            <div class="col-md-8">
                                                <div class="radio-list">
                                                    <label><input type="radio" name="mTipi" value="1" checked/> <?= dil::soz( "11fizikiShexs" ); ?>
                                                    </label>
                                                    <label><input type="radio" name="mTipi" value="2"/> <?= dil::soz( "11elaqeliShexs" ); ?>
                                                    </label>
                                                </div>
                                                <div id="form_gender_error"></div>
                                            </div>
                                        </div>
                                        <div  style="display: none;" class="form-group" vezife="shirketDiv">
                                            <label class="control-label col-md-3"><?= dil::soz( "11shirket" ); ?></label>
                                            <div class="col-md-8 ">
                                                <input class="form-control shexs_elave_shirket" id="shirket_id" data-plugin="select2-ajax" data-plugin-params='{"queryString": {"ne": "gonderen_teshkilatlar"}}'>
                                            </div>
                                        </div>
                                        <div style="display: none" class="row">
                                            <div class="col-md-12" style="padding: 10px; padding-left: 40px;">
                                                <label class="col-md-4 control-label"><input type="checkbox" id="tamMelumatGoster"> <?= dil::soz( "11tamMelumat" ); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div style="display: none" id="tamMelumatBolmeler" style="display: none;">
                                            <div class="form-group" style="border-top: 1px solid #EFEFEF;">
                                                <label class="control-label col-md-3"><?= dil::soz( "11shexsiyyetVesiqesi" ); ?></label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon aze">AZE</span>
                                                        <input class="form-control" style="width: 35%;" name="sv_seriya" placeholder="seria №" type="text">
                                                        <input class="form-control" style="width: 65%;" name="sv_nomre" placeholder="pin kodu" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11contactSts" ); ?></label>
                                                <div class="col-md-8">
                                                    <input class="form-control" data-plugin="select2-ajax" data-plugin-params='{"queryString": {"ne": "prodoc_strukturlar"}}' name="contactStrukturs" multiple>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11mPhone" ); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="mphone" name="mphone"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11smsNumber" ); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="sphone" name="smsphone"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11homePhone" ); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="hphone" name="homephone"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11Birthday" ); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control form-control-inline input-small" data-plugin="date" id="bdate" size="16" name="birthday"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11Address" ); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="address"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11City" ); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="city"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11Country" ); ?></label>
                                                <div class="col-md-8">
                                                    <input class="form-control" data-plugin="select2-ajax" data-plugin-params='{"queryString": {"ne": "country"}}' name="country">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= dil::soz( "11melumat" ); ?></label>
                                                <div class="col-md-8">
                                                    <textarea class="form-control" rows="3" name="about"></textarea>
                                                    <input type="text" name="salan" style="display:none;" value="<?= $userId ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-offset-4 col-md-6">
                                            <a href="javascript:;" class="btn green button-next"status="0"><?= dil::soz( "11Submit" ); ?>
                                                <i class="m-fa fa-swapright m-fa fa-white"></i></a>
                                            <a data-dismiss="modal" class="btn-circle btn default"><?= dil::soz( "11Close" ); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="color: red; text-align: center;" vezife="error">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <button data-dismiss="modal" style="display:none;" id="close_modal"></button>
    </div>
</div>

<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="assets/scripts/form-wizard2.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.az.js"></script>

<script>
    var shirket = '<?= $gonderen_teshkilat ?>';
    var shirket_ad = '<?= $gonderen_teshkilat_ad ?>';

    $(function() {
        Component.Plugin.PluginManager.init($('#form_wizard_1'));
        FormWizard.init();


        $( '#user_creat select[name="contactStrukturs"]' ).select2( "val" , [] );
        $( "#user_creat input[name='mTipi'][value='2']" ).click();
        $( '#user_creat #shirket_id' ).select2('data', {id: shirket, text: shirket_ad});
        $( "#user_creat a[href='#tab1']" ).click();

        $( '#form_wizard_1 .button-next' ).click( function() {
            var firstname = $( "#user_creat input[name='firstname']" ).val() ,
                lastname = $( "#user_creat input[name='lastname']" ).val() ,
                fathername = $( "#user_creat input[name='fathername']" ).val() ,
                company = $( '#user_creat #shirket_id' ).val(),
                contactStrukturs = $( '#user_creat select[name="contactStrukturs"]' ).val();

            if((firstname != '' || lastname != '') && (firstname != '' && lastname != '')){

                $.post( proBundle + "includes/customer/add_customer.php" ,
                    {
                        'firstname' : firstname ,
                        'lastname' : lastname ,
                        'fathername' : fathername ,
                        'company' : company ,
                        'contactStrukturs' : contactStrukturs ,
                        'status' : 0 ,
                        'idPrint' : 1
                    }).done( function( netice ) {
                    if( netice == "daxil_olmayib" ) {
                        daxilOlmayibModal();
                        return false;
                    }
                    if( netice == "tekrar_olmaz" ) {
                        $( '#form_wizard_1 div[vezife="error"]' ).hide().text( "Bu subyekt daha öncə əlavə edilib, eyni subyekti təkrar əlavə etmək olmaz." ).fadeIn( 300 );
                    } else {
                        // var data = {
                        //     id: netice,
                        //     text: firstname + " " + lastname
                        // };
                        //
                        // var newOption = new Option(data.text, data.id, false, false);
                        // $('#nusxelerId211 > tr:nth-child(2) > td:nth-child(4) > div > select').append(newOption).trigger('change');
                        //
                        $( '#form_wizard_1 div[vezife="error"]' ).hide();
                        $( '#close_modal' ).click();
                    }
                });
            }
        });
    });
</script>
