<p>
    <small>$gorush_ishtapshiriqi$:</small><br>
    <span  vezife="kim" visit_uid="$gorushenin_uid$">№$sid$</span>
</p>

<p>
    <small>$11gShexs$:</small><br>
    <span vezife="kim" visit_uid="$gorushenin_uid$">$kim$</span>
</p>

<p>
    <small>$11iShexsler$:</small><br>
    <span vezife="ishtirakchilar">$istirakcilar$</span>
</p>

<p>
    <small>$11gTeyinEden$:</small><br>
    <span vezife="gorushuTeyinEdib" visit_uid="$teyinedenuser$">$teyineden$</span>
</p>

<p>
    <small>$11shirket$:</small><br>
    <span vezife="shirket">$sirket$</span>
</p>

<p>
    <small>$11nvsifarishet$:</small><br>
    <span vezife="mushteri">$nvSifarish$</span>
</p>

<p>
    <small>$11contactPersn$:</small><br>
    <span vezife="kim" vezife="mushteri">$musteri$</span>
</p>

<p>
    <small>$11tarix$:</small><br>
    <span vezife="tarix">$tarix$</span>
</p>

<p>
    <small>$11hMuddeti$:</small><br>
    <span vezife="yolvaxti">$yolVaxti$</span>
</p>

<p>
    <small>$teyinat1$:</small><br>
    <span vezife="vezife="melumat"">$teyinat2$</span>
</p>

<p>
    <small>$11melumat$:</small><br>
    <span vezife="melumat">$melumat$</span>
</p>

<p>
    <small>$11testiqlemeyenler$:</small><br>
    <span vezife="testiqlemeyenler">$tesdiqlemeyenler$</span>
</p>

<p>
    <small>$11testiqleyenler$:</small><br>
    <span vezife="testiqleyenler">$tesdiqleyenler$</span>
</p>

<p>
    <small>$11qebulEdib$:</small><br>
    <span vezife="qebulEdib"></span>
</p>

<p>
    <small>$11deyishdirib$:</small><br>
    <span vezife="editOlub"></span>
</p>

<p>
    <small>$11imtinaEdib$:</small><br>
    <span visit_uid="$imtina_edenin_uid$" vezife="imtinaEdib">$imtinaSebeb$</span>
</p>


<p>
    <small>$11imtinasebebi$:</small><br>
    <span vezife="imtinaSebebi">$imtinaSebeb$</span>
</p>

<p>
    <small>$11arayishtelebetdi$:</small><br>
    <span vezife="arayish_teleb_etdi">$imtinaSebeb$</span>
</p>

<p>
    <small>$11arayishtelebolunsu$:</small><br>
    <span vezife="arayish_teleb_olunur"></span>
</p>



<script>
    if("$testiqBtn$"==1 && "$arayish_teleb_etdi$">0===false)
    {

        $("[vezife='arayish_teleb_olunur']").parent("p").show();
        $("[vezife='arayish_teleb_etdi']").parent("p").hide();
    }
    else
    {
        $("[vezife='arayish_teleb_olunur']").parent("p").hide();
        $("[vezife='arayish_teleb_etdi']").text("yox").parent("p").show();
        if("$arayish_teleb_etdi$">0)
        {
            $("[vezife='arayish_teleb_etdi']").html("<span visit_uid=$arayish_teleb_etdi$>$arayish_teleb_etdi_ad$</span>"+(parseInt("$arayish_id$")>0?' ( <a href="?module=arayishlar&arayish_id='+parseInt("$arayish_id$")+'" target="_blank">$11arayish$ №'+"$arayish_id$"+'</a> )':""));
        }
    }
    if("$qebulLog0$"!="")
    {
        $("[vezife='qebulEdib']").parent("p").show();
        $("[vezife='qebulEdib']").html("<span visit_uid=''>$qebulLog0$</span>" + " - <i class=\"fa fa-time\"></i> " + "$qebulLog1$");
    }
    else
    {
        $("[vezife='qebulEdib']").parent("p").hide();
    }
    $("[vezife='editOlub']").text("");
    var editss = JSON.parse("$edits$");
    if(editss.length>0)
    {
        $("[vezife='editOlub']").parent("p").show();
        var editsTxt = "";
        for(var nn in editss)
        {
            editsTxt += "<span visit_uid='"+editss[nn][3]+"'>"+editss[nn][0] + "</span> - <i class=\"fa fa-time\"></i> " + editss[nn][1] + "<br>Səbəb: " + editss[nn][2] + "<br>";
        }
        $("[vezife='editOlub']").html(editsTxt);
    }
    else
    {
        $("[vezife='editOlub']").parent("p").hide();
    }

    if("$imtinaSebeb$"!="sehv" && "$imtinaEden$"!="sehv")
    {
        $("[vezife='imtinaEdib']").text("$imtinaEden$").parent("p").show();
        $("[vezife='imtinaSebebi']").text("$imtinaSebeb$").parent("p").show();

    }
    else
    {
        $("[vezife='imtinaEdib']").parent("p").hide();
        $("[vezife='imtinaSebebi']").parent("p").hide();

    }
</script>

