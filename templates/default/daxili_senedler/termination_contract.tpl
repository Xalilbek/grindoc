<p>
    <small>$47emrin_nomresi$:</small><br>
    <span>$emrin_nomresi$</span>
</p>

<p>
    <small>$47emekdash$:</small><br>
    <span vezife="kim" visit_uid="$gorushenin_uid$">$user_ad$</span>
</p>

<p>
    <small>$47esas$:</small><br>
    <span vezife="ishtirakchilar">$qeyd$</span>
</p>

<p>
    <small>$47ishchinin_erizesi$:</small><br>
    <span vezife="gorushuTeyinEdib" visit_uid="$teyinedenuser$">$employe_petition$</span>
</p>
$detailed_information$
<p>
    <small>$47xitam_verildiyi_bolme$:</small><br>
    <span > $bolme_ad$</span>
</p>

<p>
    <small>$47xitam_verildiyi_vezife$:</small><br>
    <span vezife="mushteri">$vezife_ad$</span>
</p>

<p>
    <small>İşdən çıxma kateqoriyası:</small><br>
    <span vezife="kim" vezife="mushteri">$isden_cixma_kategory$</span>
</p>

<p>
    <small>$47emrin_verilme_tarixi$:</small><br>
    <span >$emrin_verilme_tarixi$</span>
</p>

<p>
    <small>$47xitam_verilme_tarixi$:</small><br>
    <span vezife="yolvaxti">$teyin_olunma_tarixi$</span>
</p>

<p>
    <small>$47sened$:</small><br>
    <span vezife="yolvaxti">$sened$</span>
</p>

<p>
    <small>$47qoshma$:</small><br>
    <span vezife="yolvaxti">$qoshma$</span>
</p>

<p>
    <small>$47deyishdirilib$:</small><br>
    <span vezife="yolvaxti">$edits$</span>
</p>

<p>
    <small>$47imtina_edilib$:</small><br>
    <span vezife="yolvaxti">$imtinaEden$</span>
</p>

<p>
    <small>$47imtinanin_sebebi$:</small><br>
    <span vezife="yolvaxti">$imtinaSebeb$</span>
</p>

<p>
    <small>$47son_haqqhesab$:</small><br>
    <span vezife="yolvaxti">$son_haqq_hesab$</span> AZN <i class="fa fa-edit" vezife="quick_change" style="cursor: pointer; color: #3B9C96;"></i></span>
</p>

<p>
    <small>$47qisa_mezmun$:</small><br>
    <span vezife="yolvaxti">$esas$</span>
</p>

<p>
    <small>$47imtina_edilib$:</small><br>
    <span vezife="yolvaxti">$imtinaEden$</span>
</p>

<p>
    <small>$47imtina_edilib$:</small><br>
    <span vezife="yolvaxti">$imtinaEden$</span>
</p>

<p>
    <small>$47imtina_edilib$:</small><br>
    <span vezife="yolvaxti">$imtinaEden$</span>
</p>

<script>
    $("i[vezife='quick_change']").click(function()
    {
        var hesab_deyish = $("#bosh_modal$MN$ #haqq_hesab").text();
        var mn2 = modal_yarat("$47duzelish_et$","<div class='modal-body form' style='padding: 0;'><form class='form-horizontal form-bordered form-row-stripped'><div class='form-body'><div class='form-group'><label class='control-label col-md-4'>$47son_haqqhesab$</label><div class='col-md-6'><input value='"+hesab_deyish+"' id='son_haqq_hesab' class='form-control' autocomplete='off' /></div></div></div></form></div>","<button class='btn default' data-dismiss='modal'>$47bagla$</button><button class='btn green saveBtn' ><i class='icon-check'></i> $47tesdiqle$</button>","btn-info" , false, true);
        $("#bosh_modal" + mn2 + " .saveBtn").click(function()
        {
            var hesab = $("#bosh_modal" + mn2 + " #son_haqq_hesab").val();
            var error = false;
            if(hesab== '' || !$.isNumeric(hesab))
            {
                $("#bosh_modal" + mn2 + " #son_haqq_hesab").attr('style',"border: 1px dashed red !important;");
                error=true;
            }
            else
                $("#bosh_modal" + mn2 + " #son_haqq_hesab").attr('style',"");
            if(error===false)
            {
                $.post("includes/proreport/proid/son_haqqhesab.php",{"hesab":hesab,"id":sid},function(netice){
                    if(netice=="daxil_olmayib")
                    {
                        daxilOlmayibModal();
                        return false;
                    }
                    netice = JSON.parse(netice);
                    if(netice['status']=="hazir")
                    {
                        $("#bosh_modal$MN$ #haqq_hesab").html(hesab);
                        $("#bosh_modal" + mn2 + " .saveBtn").prev('button').click();
                        modal_loading(0);
                    }
                    else
                    {

                    }
                });
            }
        });
        //modal_loading(1);
    });
</script>

