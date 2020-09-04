<style>
    .accordion-block .fa-user{
        margin-top: 32px;
    }
</style>
<div class="whiteboard icraya_gonder" style="display: none;">
    <div class="col-md-12">
        <div class="blockname">
            <h3 class="text-success text-left">
                <strong class="bp-users">
					<?php echo (getProjectName() === TS ? dsAlt('2616derkenar', "Dərkənar") : dsAlt("2616icraya_gonder", 'İcraya göndər')) ?>
				</strong>
            </h3>
            <span class="accordion-toggler">
                <i class="fa fa-minus"></i>
            </span>
        </div>
    </div>
    <div class="accordion-block">
        <div class="form-group">
            <div class="<?= $input_tip ?> rey_muellifi_icraya_gonder ">
                <div class="row">
                    <label class="col-md-12" id="rey"> <?= dsAlt('2616rey_muellifi_dos', "Rəy müəllifi"); ?>:</label>
                    <div class="col-md-12" id="reyInput">
                        <input name="rey_muellifi"
                               class="form-control select"
                               vezife="rey_muellifi" placeholder="<?= dsAlt('2616rey_muellifi_dos', "Rəy müəllifi"); ?>"
							   data-plugin="select2-ajax"
						>
                    </div>
                </div>
            </div>
            <div class="<?= $input_tip ?> icra_uchun kurator_icraya_gonder">
                <div class="row">
                    <label class="col-md-12"><?php print dsAlt('2616rollar_icraya_nezaret_eden', "İcraya nəzarət edən şəxs"); ?>:</label>
                    <div class="kuratorShexs">
                        <div style="top: 8px;" class="col-md-12" id="kuratorInput" data-function="container">
                            <div onclick="templateYukle('axtarishmodali', 'Əməkdaş seçimi', {'tip': 'kurator_shexs','sened_novu':<?= $sened_novu ?>,'modal_tipi':'emekdash','class':'kurator_icraya_gonder','id':'kuratorInput','name':'kurator[]','extra_emekdash': collectValues(),'cari_emekdash': inputValues('kurator[]')}, 0, true);" class="selectall" style="color:#1c8f5f;height: 18px;width: 13px;margin-left: 101%;margin-top: -29px;">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display: block;top: 8px;" class="<?= $input_tip ?> melumat_uchun melumat_icraya_gonder">
                <div class="row">
                    <label style="top: -8px;" class="col-md-12 "><?= dsAlt('2616qeydiyyat_penceleri_melumatlandirlan', "Məlumatlandırılan şəxslər:")?> </label>
                    <div class="melumat">
                        <div class="col-md-12" id="melumatInput" data-function="container">
                            <div onclick="templateYukle('axtarishmodali', 'Əməkdaş seçimi', {'tip': 'melumat_shexsler','sened_novu':<?= $sened_novu ?>,'modal_tipi':'group','class':'melumat_icraya_gonder','id':'melumatInput','name':'melumat[]','ne':'melumat_shexsler','extra_emekdash': collectValues(),'cari_emekdash': inputValues('melumat[]')}, 0, true);" class="selectall" style="color:#1c8f5f;height: 18px;width: 13px;margin-left: 101%;margin-top: -29px;">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group icra_uchun ">
            <div class="<?= $input_tip ?> mesul_shexs_icraya_gonder">
                <div class="row">
                    <label class="col-md-12"><?php print dsAlt('2616icrachi_dos', "İcraçı"); ?>:</label>
                    <div class="incoming-document">
                        <div style="top: 6px;" class="col-md-12" id="mesul_shexsInput" data-function="container">

                            <div onclick="templateYukle('axtarishmodali', 'Əməkdaş seçimi', {'tip': 'icraci_shexs','sened_novu':<?= $sened_novu ?>,'modal_tipi':'emekdash','class':'mesul_shexs_icraya_gonder','id':'mesul_shexsInput','name':'mesul_shexs[]','extra_emekdash': collectValues(), 'cari_emekdash': inputValues('mesul_shexs[]') }, 0, true);" class="selectall" style="color:#1c8f5f;height: 18px;width: 13px;margin-left: 101%;margin-top: -29px;">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="<?= $input_tip  ?> icra_uchun ishtirakchi_icraya_gonder">
                <div class="row">
                    <label class="col-md-12"> <?php print dsAlt('2616rollar_hemicraci', "Həmicraçı"); ?>:</label>
                    <div class="ishtirakchi">
                        <div style="top: 6px" class="col-md-12" id="ishtirakchiInput" data-function="container">
                            <div onclick="templateYukle('axtarishmodali', 'Əməkdaş seçimi', {'tip': 'ishtirakchi_shexs','sened_novu':<?= $sened_novu ?>,'modal_tipi':'group','class':'ishtirakchi_icraya_gonder','id':'ishtirakchiInput','name':'ishtirakchi[]','ne':'ishtirakchi_shexsler','extra_emekdash': collectValues(),'cari_emekdash': inputValues('ishtirakchi[]')}, 0, true);" class="selectAllHemicraci" style="color:#1c8f5f;height: 18px;width: 13px;margin-left: 101%;margin-top: -29px;">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="form-group">
            <div class="<?= $input_tip ?> yoxlayan_shexs_icraya_gonder">
                <div class="row">
                    <label class="col-md-12"><?php print dsAlt('2616yoxlayan_sh', "Yoxlayan şəxs"); ?>:</label>
                    <div class="col-md-12">
                        <input name="yoxlayan_shexs"
                               class="form-control select"
                               vezife="yoxlayan_shexs" placeholder="Yoxlayan şəxs">
                    </div>
                </div>
            </div>
            <?php if(getProjectName() === TS || getProjectName() === ANAMA || getProjectName() === AP ): ?>
                <div class="<?= $input_tip ?>" >
                    <div class="row">
                        <label class="col-md-12"><?php print dsAlt('2616etrafli_derkenar_metn', "Dərkənar mətni"); ?></label>
                        <div class="col-md-12">
                            <div class="input-group" style="width:100% !important;">
                                <input  name="derkenar_metn_id"
                                       class="form-control derkenar_metn_select"
                                       data-plugin="select2-ajax"
                                       placeholder="Dərkənarın mətni...">
                                <div class="input-group-addon addIcon msk_huquqi qeydiyyat_derkenar derkenar_metni">
                                    <i class="fa fa-plus"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="<?= $input_tip ?>">
                <div class="row">
                    <label class="col-md-12"><?php print dsAlt('2616daxil_olan_mektub_nezaretde', "Məktub nəzarətdədir"); ?>:</label>
                    <div class="col-md-1" style="padding-top: 7px;">
<!--                        <input type="checkbox" data-plugin="uniform" name="mektub_nezaretdedir"-->
<!--                               onchange="fadeInOut($(this), $('.gizlet'))"/>-->

                            <input type="checkbox" data-plugin="uniform" name="mektub_nezaretdedir"/>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

