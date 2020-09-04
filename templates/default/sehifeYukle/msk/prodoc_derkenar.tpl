<style>
    .tablar>div
    {
        position: relative;
        margin-left: 2px;
    }
    .tablar>div>div
    {
        position: absolute;
        right: 7px;
        top: 8px;
        cursor: pointer;
    }
    .tablar>div>div:hover
    {
        color: #FFA5A5;
    }
</style>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>

<div class="modal-body form">
    <form class="form-horizontal form-bordered ">
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label">$9954bashliq$:</label>
                <div class="col-md-4" style="width: 44% !important">
                    <input type="text" class="form-control" placeholder="$9954bashliq$" vezife="bashliq" value="$bashliq$" $disabled$>
                </div>
            </div>
            <div class="form-group" procrm style="display:none;">
                <label class="col-md-3 control-label">$9954hell$:</label>
                <div class="col-md-4" style="width: 44%">
                    $procrmHeller$
                </div>
            </div>
            <div class="form-group" seria>
                <label class="col-md-3 control-label">$9954seria$:</label>
                <div class="col-md-5" style="width: 44%">
                    <input $disabled$ type="text" class="form-control" placeholder="$9954senedin_seriasi$" vezife="seria" value="$seria$">
                </div>
            </div>
            <div class="form-group" vezife="table_goster">
                <div class="col-md-10" style="padding-left:20px;">
                    <label><input  type="checkbox" vezife="shablon_sal" onchange="if($(this).is(':checked')){$('#emeliyyatlarCedveli12').parent('div').parent('div').fadeIn(500);}else{$('#emeliyyatlarCedveli12').parent('div').parent('div').fadeOut(500);}"> Əməliyyat ardıcıllığını yığ</label>
                </div>
            </div>
            <div class="form-group" style="display:none;">
                <div class="tablar" style="display:$bolmeler_uzre$;">
                    <div class="col-md-1 btn-circle" style="padding: 1px 10px;"><button type="button" id="tabElaveEt" class="$disabled$ btn btn-default btn-sm " onclick="tabElaveEt();"><i class="icon-plus"></i></button></div>
                </div>
                <div class="col-md-12">
                    <div class="form-group" style="display:$bolmeler_uzre$;">
                        <div class="btn-group col-md-2" id="select_type" vezife="bolmeler_select">
                            <div class="btn dropdown-toggle" data-toggle="dropdown" style="cursor: pointer;float: right;"><i class="fa fa-filter"></i> <span>$9954bolmeler$</span> <i class="fa fa-angle-down"></i></div>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:;" select="struktur" type="1"> $9954bolmeler$</a></li>
                                <li><a href="javascript:;" select="vezife" type="2"> $9954emekdashlar$</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3" vezife="bolmeler_select" type="1">
                            <div style="background: none repeat scroll 0% 0% rgb(238, 238, 238);height: 34px;border: 1px solid rgb(221, 221, 221);padding: 6px 10px;color: rgb(51, 51, 51);">$9954butun_bolmeler_uzre$</div>
                        </div>
                        <div class="col-md-3" vezife="bolmeler_select" type="2">
                            <input class="form-control" multiple placeholder="$9954emekdashlar$" vezife="emekdashlar">
                        </div>
                        <label class="col-md-2 control-label" vezife="bolmeler_select_st">$9954bolmeler$:</label>
                        <div class="col-md-3" style="display:none;" vezife="bolmeler_select_st">
                            <div style="background: none repeat scroll 0% 0% rgb(238, 238, 238);height: 34px;border: 1px solid rgb(221, 221, 221);padding: 6px 10px;color: rgb(51, 51, 51);">$9954butun_bolmeler_uzre$</div>
                        </div>
                        <label class="col-md-1 control-label">$9954bashliq$:</label>
                        <div class="col-md-2">
                            <input class="form-control" maxlength=20 placeholder="$9954bashliq$" vezife="bashliqq">
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-advance table-hover" id="emeliyyatlarCedveli12">
                        <thead>
                        <tr>
                            <th width=40>№</th>
                            <th>$9954emeliyyat$</th>
                            <th>$9954icrachi_tipi$</th>
                            <th>Dərkənar icraçısı</th>
                            <th style="width: 20%">$9954icrachi$</th>
                            <th style="text-align:right;width:50px;"><button type="button" $disabled$ class="btn btn-xs green"><i class="icon-plus"></i> $9954elave_et$</button></th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <button $disabled$ class="btn green btn-circle" style="background: #1C8F5F; border-color: #1C8F5F;" vezife="save"><i class="fa fa-save"></i> $9954yadda_saxla$</button>
        <button class="btn default btn-circle" data-dismiss="modal"><i class="fa fa-close"></i> $9954bagla$</button>
    </div>
</div>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="prodoc/settings.js"></script>

<script>
    var struktur_tipi = "$struktur_tipleri$";
    var emeliyyatlarSiyahi = JSON.parse("$emeliyyatlar_prodoc$");
    var emeliyyatlarMass = JSON.parse("$emeliyyatlar$");

    var emeliyyatlar_slct = '';
    var vezifeler = "$vezifeler$";
    var bolmelerSiyahi = "$bolmelerSiyahi$";
    var userler = "$userler$";

    for(var n in emeliyyatlarSiyahi)
    {
        emeliyyatlar_slct += '<option value="'+emeliyyatlarSiyahi[n][0]+'">'+emeliyyatlarSiyahi[n][1]+'</option>';
    }
    if(1===$shablon_sal$)
    {
        $("#bosh_modal input[vezife='shablon_sal']").click();
    }
    if($standart$===1)
    {
        $("#bosh_modal div[vezife='table_goster']").hide();
    }
    if("$bolmeler_uzre$"=='none')
    {
        $("#bosh_modal div[procrm]").show().find("select").select2();
        $("#bosh_modal div[procrm]").next('div.form-group').hide().find("input").val("1");
    }
    $("#bosh_modal select[vezife='bolmeler']").html(bolmelerSiyahi).select2();



    var qr = 0;
    if(emeliyyatlarMass[0][1][0][0]>0){
        $('[vezife="shablon_sal"]').trigger("click");
    }
    for(var bolmeler in emeliyyatlarMass)
    {
        var templateQrup2 = '<div class="col-md-2 btn btn-default" onclick="if($(event.target).hasClass(\'col-md-2\')){tabSech($(this));}" qrup="'+$(".tablar>.col-md-2").length+'" '+(emeliyyatlarMass[bolmeler][0]['standart']==1?"standart":"")+' bolmeler="'+emeliyyatlarMass[bolmeler][0]['bolmeler']+'" type="'+emeliyyatlarMass[bolmeler][0]['type']+'">'+emeliyyatlarMass[bolmeler][0]['bashliq']+(emeliyyatlarMass[bolmeler][0]['standart']==1?"":("$pr$"=="2"?'<div onclick="tabSil($(this))"><i class="icon-close"></i></div>':""))+'</div>';
        $("#bosh_modal .tablar>.col-md-1").before(templateQrup2);

        for(var i in emeliyyatlarMass[bolmeler][1])
        {

            $("#emeliyyatlarCedveli12 tbody").append('<tr qrup="'+qr+'" bolmeler="'+bolmeler+'"><td>'+($("#emeliyyatlarCedveli12 tbody tr").length+1)+'</td>' +
                '<td><select$disabled$  class="form-control" placeholder="$9954emeliyyati_sechin$"><option></option>'+emeliyyatlar_slct+'</select></td>' +
                '<td><select$disabled$ class="form-control" placeholder="$9954icrachini_sechin$" ><option></option>$icrachi_tipleri$</select></td>' +
                '<td><select$disabled$ class="form-control" placeholder="Dərkənar icraçısı.." onchange="collectPerson($(this));" ><option></option>$derkenar_icracisi$</select></td>' +
                '<td> <input type="text"  placeholder="$9954icrachi$" class="form-control select icrachi " ></td>'+
                '<td style="vertical-align:middle;"><button$disabled$ class="btn btn-xs btn-danger" type="button" onclick="$(this).parents(\'tr\').eq(0).fadeOut(300,function(){$(this).remove();});"><i class="icon-trash"></i> $9954sil$</button></td></tr>');

            $("#emeliyyatlarCedveli12 tbody tr:last select").select2();
            $("#emeliyyatlarCedveli12 tbody tr:last td:nth-child(2) select").select2("val",emeliyyatlarMass[bolmeler][1][i][0]);
            $("#emeliyyatlarCedveli12 tbody tr:last td:nth-child(3) select").select2("val",emeliyyatlarMass[bolmeler][1][i][1]).trigger("change");
            $("#emeliyyatlarCedveli12 tbody tr:last td:nth-child(4) select").select2("val",emeliyyatlarMass[bolmeler][1][i][4]).trigger("change");
            // $("#emeliyyatlarCedveli12 tbody tr:last td:nth-child(5) select").select2("val",mTip);


            // $("#emeliyyatlarCedveli12 tbody tr:last td:nth-child(6) ").hide();
            // $("#emeliyyatlarCedveli12 thead tr:last th:nth-child(6) ").hide();
            $("#emeliyyatlarCedveli12 tbody tr:last td:nth-child(1) input").val(emeliyyatlarMass[bolmeler][1][i][3]);


                $("#emeliyyatlarCedveli12 tbody tr:last td:nth-child(5) input").select2("data",emeliyyatlarMass[bolmeler][1][i][2]).trigger("change");


            $("#emeliyyatlarCedveli12 tbody tr:last td:nth-child(4) select").attr("disabled", "$pr$"=="2"?false:true);
        }
        qr++;
    }
    $("#emeliyyatlarCedveli12 thead button").click(function()
    {
        var checkAdd=true;
        var currentTr=$('#emeliyyatlarCedveli12 tbody').children('tr').length-1;
        var currentTd=$('#emeliyyatlarCedveli12 tbody tr').eq(currentTr).children('td').length-1;
        $('#emeliyyatlarCedveli12 tbody tr').eq(currentTr).children('td').each(function (i) {
            if(i!=0&&i!=currentTd&&i!=currentTd-1){
                if($(this).find('select').val()==0&&$(this).find('select').val()==''){
                    checkAdd=false;
                }

            }
            else if (i==currentTd-1){
                if($(this).find('.select').select2("val")==0&&$(this).find('.select').select2("val")==''){
                    checkAdd=false;
                }
            }

        });
        if (checkAdd==false){
            // $("#bosh_modal div[vezife='error']").text('Əlavə edilən boş sətr mövcuddur');
            return false;
        }

        $("#emeliyyatlarCedveli12 tbody").append('<tr s="1" bolmeler="'+$("#bosh_modal .tablar>.btn-success").attr("bolmeler")+'" qrup="'+$("#bosh_modal .tablar>.btn-success").attr("qrup")+'"><td>'+($("#emeliyyatlarCedveli12 tbody tr").length+1)+'</td>' +
            '<td><select class="form-control" placeholder="$9954emeliyyati_sechin$"><option></option>'+emeliyyatlar_slct+'</select></td><td><select class="form-control" placeholder="$9954icrachini_sechin$" ><option></option>$icrachi_tipleri$</select></td>' +
            '<td><select$disabled$ class="form-control" placeholder="Dərkənar icraçısı.." onchange="collectPerson($(this));" ><option></option>$derkenar_icracisi$</select></td>' +
            '<td> <input type="text" placeholder="$9954icrachi$"  class="form-control select" onclick="selectPerson($(this));" ></td>' +
            '<td style="vertical-align:middle;"><button class="btn btn-xs btn-danger" type="button" onclick="$(this).parents(\'tr\').eq(0).fadeOut(300,function(){$(this).remove();});"><i class="icon-trash"></i> $9954sil$</button></td></tr>');
        $("#emeliyyatlarCedveli12 tbody tr:last select").select2();
        $("#emeliyyatlarCedveli12 tbody tr:last").hide().fadeIn(300);
        siralamaDuzelt();
    });
    function tabSech(t)
    {
        if(t.hasClass("btn-success"))
            return;
        $(".tablar>.btn-success").css("background","");

        $(".tablar>.btn-success").removeClass("btn-success").addClass("btn-default");
        t.removeClass("btn-default").addClass("btn-success");
        t.css("background","#1C8F5F");
        var type = t.attr("type");
        if(typeof t.attr("standart")!="undefined")
        {
            $("#bosh_modal div[vezife='bolmeler_select']").hide();
            $("#bosh_modal div[vezife='bolmeler_select_st']").show();
            $("#bosh_modal input[vezife='bashliqq']").attr('disabled',true);
        }
        else
        {
            $("#bosh_modal input[vezife='bashliqq']").attr('disabled',false);
            $("#bosh_modal div[vezife='bolmeler_select']").show();
            $("#bosh_modal div[vezife='bolmeler_select_st']").hide();
            if(type==1) $("#bosh_modal select[vezife='bolmeler']").select2("val",t.attr("bolmeler").split(","));
            else $("#bosh_modal select[vezife='emekdashlar']").select2("val",t.attr("bolmeler").split(","));
            $("#bosh_modal #select_type a[type='"+type+"']").trigger("click");
        }
        $("#bosh_modal input[vezife='bashliqq']").val(t.text());
        $("#emeliyyatlarCedveli12 tbody tr").hide();
        $("#emeliyyatlarCedveli12 tbody tr[s]").removeAttr("s");
        $("#emeliyyatlarCedveli12 tbody tr[qrup='"+t.attr("qrup")+"']").show().attr("s",1);
        siralamaDuzelt();
    }

    $("#tabElaveEt").hide();

    function siralamaDuzelt()
    {
        var say = 0;
        $("#emeliyyatlarCedveli12 tbody tr[s='1']").each(function()
        {
            say++;
            $(this).children("td").eq(0).text(say);
        });
    }
    tabSech($("#bosh_modal .tablar>.col-md-2[standart]"));

    $("#bosh_modal select[vezife='bolmeler'],#bosh_modal select[vezife='emekdashlar']").unbind("change").change(function()
    {
        var bolmeler = $(this).val().join(",");
        $("#bosh_modal .tablar>.btn-success").attr("bolmeler",bolmeler);
        $("#emeliyyatlarCedveli12 tbody tr[s='1']").attr("bolmeler",bolmeler);
    });
    $("#bosh_modal input[vezife='bashliqq']").keyup(function()
    {
        $("#bosh_modal .tablar>.btn-success").text($(this).val()).append('<div onclick="tabSil($(this))"><i class="icon-close"></i></div>');
    });


    $("#bosh_modal button.green[vezife='save']").click(function()
    {
        var bashliq = $("#bosh_modal input[vezife='bashliq']").val(),
            emeliyyatlar=[],
            seria = "$sened_tip_esas$"!="izahat"?$("#bosh_modal input[vezife='seria']").val():"0",
            procrm_hell_id = $("#bosh_modal div[procrm] select[procrmhell]").val(),
            sehv_var = false,
            shablon_sal = $("#bosh_modal input[vezife='shablon_sal']").is(":checked")?1:0,
            subId = parseInt('$sub_id$');
        if(shablon_sal===1)
        {
            $("#emeliyyatlarCedveli12 tbody tr").each(function()
            {
                var emeliyyat = $(this).children("td").eq(1).find("select").select2("val"),

                    icrachiTip = $(this).children("td").eq(2).find("select").select2("val"),

                    icrachiInput = $(this).children("td").eq(4).find('.select').val();
                    derkenarchi = $(this).children("td").eq(3).find('select').val();
                    icrachi = $(this).children("td").eq(4).find('.select').select2("val");
                    stQrup = $(this).attr("qrup"),
                    qrDiv = $("#bosh_modal .tablar>.col-md-2[qrup='"+stQrup+"']"),
                    bolmeler = qrDiv.attr("bolmeler"),
                    bashliq = qrDiv.text(),
                    standart = typeof qrDiv.attr("standart")!="undefined"?1:0,
                    type = standart==1?1:qrDiv.attr("type");

                if(bashliq=="")
                {
                    tabSech(qrDiv);
                    $("#bosh_modal input[vezife='bashliqq']").css("border","1px dashed red");
                    sehv_var = true;
                }
                else
                {
                    $("#bosh_modal input[vezife='bashliqq']").css("border","");
                }
                if(standart==0 && bolmeler=="")
                {
                    tabSech(qrDiv);
                    if(type==1) $("#bosh_modal select[vezife='bolmeler']").prev("div").attr("style","border:1px dashed red !important;");
                    else $("#bosh_modal select[vezife='emekdashlar']").prev("div").attr("style","border:1px dashed red !important;");
                    sehv_var = true;
                }
                else
                {
                    if(type==1) $("#bosh_modal select[vezife='bolmeler']").prev("div").css("border","");
                    else $("#bosh_modal select[vezife='emekdashlar']").prev("div").css("border","");
                }
                if((emeliyyat>0)==false)
                {
                    $(this).children("td").eq(1).children("div").attr("style","border:1px dashed red !important;");
                    sehv_var = true;
                }
                else
                {
                    $(this).children("td").eq(1).children("div").css("border","");
                }
                if ((icrachi>0)==false&&(icrachiInput>0)==false) {
                    sehv_var = true;
                    $(this).children("td").eq(4).children("div").attr("style","border:1px dashed red !important;");

                }
                else
                {
                    $(this).children("td").eq(4).children("div").css("border","");
                }
                if (derkenarchi=='') {
                    sehv_var = true;
                    $(this).children("td").eq(3).children("div").attr("style","border:1px dashed red !important;");

                }
                else
                {
                    $(this).children("td").eq(3).children("div").css("border","");
                }

                if((icrachiTip>0)===false)
                {
                    $(this).children("td").eq(2).children("div").eq(0).attr("style","border:1px dashed red !important;");
                    sehv_var = true;
                }
                else
                {
                    $(this).children("td").eq(2).children("div").css("border","");
                }

                if(sehv_var===false)
                {
                    emeliyyatlar.push([emeliyyat,icrachiTip,derkenarchi,icrachi,standart,type]);
                }
            });
        }

        if(bashliq.trim()=="")
        {
            $("#bosh_modal input[vezife='bashliq']").css("border","1px dashed red");
            sehv_var = true;
        }
        else
        {
                $("#bosh_modal input[vezife='bashliq']").css("border","");
        }
        if(seria.trim()=="")
        {
            $("#bosh_modal input[vezife='seria']").css("border","1px dashed red");
            sehv_var = true;
        }
        else
        {
            $("#bosh_modal input[vezife='seria']").css("border","");
        }

        if("$bolmeler_uzre$"=='none' && (procrm_hell_id>0)==false)
        {
            $("#bosh_modal div[procrm] select[procrmhell]").prev("div").attr("style","border:1px dashed red !important");
            sehv_var = true;
        }
        else
        {
            $("#bosh_modal div[procrm] select[procrmhell]").prev("div").css("border","");
        }

        if(sehv_var===false)
        {
            modal_loading(1);
            $.post("includes/msk/prodoc/derkenar_sened_novu.php",{"ne":"add_edit","sid":"$sid$","seria":seria,"bashliq":bashliq,"emeliyyatlar":JSON.stringify(emeliyyatlar),"derkenarchi":derkenarchi, "icrachi":icrachi, "shablon_sal":shablon_sal,"sub_id":subId,"sened_tip":"$sened_tip$","procrm_hell_id":procrm_hell_id},function(netice)
            {
                modal_loading(0);
                try
                {
                    netice = JSON.parse(netice);
                    if(netice[0]=="sehv")
                    {
                        $("#bosh_modal div[vezife='error']").text(netice[1]);
                    }
                    else if(netice[0]=="hazir")
                    {
                        $("#bosh_modal").modal("hide");
                        if($sid$==0)
                        {
                            $("#$hara$ tbody").append('<tr ne="'+('$sened_tip$'=='procrm_gorush'||'$sened_tip$'=='procrm_zeng'?'$sened_tip$':"")+'" tr_id="'+netice[1]+'"><td>'+($("#$hara$ tbody tr").length+1)+'</td><td>'+bashliq+'</td><td style="text-align:center;"><button type="button" class="btn btn-xs btn-default" disabled=""><i class="icon-doc"></i> $9954forma$</button></td></tr>');
                        }
                        else
                        {
                        }
                    }
                }
                catch(e)
                {

                }
            });
        }
    });


    //izahatdirsa
    if("$sened_tip_esas$"=="izahat"){
        $(".form-group[seria]").hide();
    }
    function collectPerson(value){
        var tip=(value.val()==2)? 'daxili' :( (value.val()==1)?'huquqi':'fiziki' ) ;
        var shexs = value.parents('tr').children('td').eq(4).find('.select');
        var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

        axtarish(shexs, {
            allowClear: true,
            getAjaxData: function (t) {
                return {
                    'ne': 'rey_muellifleri',
                    'tip':tip
                 }
            }
        });

    }
    function selectPerson(value) {

        if(value.parents('tr').children('td').eq(3).find('select').val()==''||value.parents('tr').children('td').eq(3).find('select').val()==0||value.parents('tr').children('td').eq(3).find('select').val()==null){
            swals("Dərkənar icraçısı seçilməyib!", "", "error");
            return false;
        }
        return true;
    }

    $(".icrachi").on("select2-focus",function () {
        var res=selectPerson($(this));
        if(!res){
            $(this).select2("enable",false);
        }
        else{
            $(this).select2("enable",true);
        }
    })

</script>