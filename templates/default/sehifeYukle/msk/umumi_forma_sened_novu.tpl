<style type="text/css">
    #tab_13 tbody[vezife='menyular1'] td{
        cursor: pointer;
    }
</style>

<div id="tab_13">
    <div class="row">
        <div class="col-md-10">
            <table class="table table-striped table-bordered table-advance table-hover" id="contact_types">
                <thead>
                <tr>
                    <th>№</th>
                    <th style="width: 200px;">Sənəd növü</th>
                    <th style="width: 200px;">Rol</th>
                    $theadElements$
                </tr>
                </thead>
                <tbody vezife="menyular1">
                $contactTypes$
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('select').select2();

    var fildTypesHtml = '<select class="form-control" name="tip"><option></option>$tipler$</select>';
    var fildTypesHtml3 = '<input name="rollar" class="form-control select" vezife="rollar" data-id="1" placeholder="Rol">';

    $("#tab_13 tbody[vezife='menyular1'] tr:first td:first").click();

    $('#contact_type_add').click(function() {
        var tableId = "contact_types";
        if ($("#" + tableId + " tbody tr[time='null']").length) {
            $('#' + tableId + ' tbody tr').remove();
        }
        var say = $('#' + tableId + ' tbody tr').length + 1;
        $('#' + tableId + ' tbody').append("<tr tr_id='0' del='1'><td style='width:20px;text-align:center;'>" + say + "</td><td>" + fildTypesHtml + "</td><td>" + fildTypesHtml3 + "</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> </a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> </a></td></tr>");

        $('input[maxlength]').maxlength();
        $('#' + tableId + ' tbody').find('select').select2();
        $('#' + tableId + ' tbody tr:last .yellow').click(function () {
            $(this).parent('td').parent('tr').remove();
        });
        select();
    });

    $('#contact_types>tbody').on("click",".blue",function(e)
    {
        var tableId = $(this).parents("table").eq(0).attr("id"),
            t = $(this),
            tr = t.parents("tr").eq(0),
            sayi = tr.children('td').eq(0).text(),
            tip = tr.children('td').eq(1).find("select").val(),
            fildTypeName = tr.children('td').eq(1).find("select option:selected").text(),
            tid = tr.attr("tr_id");


        console.log(tr.children('td').eq(1).find("select"));


        // seçilib?
        var sechilib = "";
        if (tr.find("td[sechilib='1']").length) {
            sechilib = "sechilib='1'";

            console.log(sechilib);
        }
        var disabled = "";
        if (tr.attr('del') === '0') disabled = " disabled ";

        if(tip == "")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">$9956error2$</td></tr>');
            setTimeout(function(){$('#'+tableId+' tbody tr[error]').remove();}, 3000);
        }
        else
        {
            var rollar = $('input[name="rollar"]').val();

            $.post("prodoc/ajax/msk/umumi_forma_sened_novu.php", {'tid':tid, 'tip' : tip, 'rollar':rollar},function(netice)
            {
                if(netice == "error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">$9956error3$</td></tr>');
                    setTimeout(function(){$('#'+tableId+' tbody tr[error]').remove();}, 3000);
                }
                else
                {

                    var valuesOfInstitution1=$('input[name="rollar"]').val().split(",");
                    var arrOfInstitution1= $('input[name="rollar"]').select2('data');
                    var count=0;

                    var fildTypeName3="";
                    var fildType3="[";
                    arrOfInstitution1.forEach(function(element) {
                        if(arrOfInstitution1.length-1>count){
                            fildTypeName3+= element.text+",";
                            fildType3+="{ \"id\" : "+valuesOfInstitution1[count]+", \"text\": \""+element.text+"\" },";
                        }
                        else{
                            fildTypeName3+= element.text;
                            fildType3+="{ \"id\" : "+valuesOfInstitution1[count]+", \"text\": \""+element.text+"\" }";
                        }
                        count++;

                    });
                    fildType3+="]";

                    tr.attr("tr_id",netice).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td val="+tip+" "+sechilib+">"+fildTypeName+"</td><td data-rollar-ad='"+fildType3+"'>"+fildTypeName3+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> </a><a "+disabled+" href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> </a></td>");
                    select();
                }
            });
        }
        e.stopPropagation();
    });

    $('#contact_types>tbody').on("click",".purple",function(e) {
        var th = $(this).parent('td').parent('tr'),
            idN = th.attr("tr_id"),
            fildTypeName = th.children("td").eq(1).text(),
            // tip = th.children('td').eq(1).find("select").val(),
            tipval = th.children("td").eq(1).attr('val'),
            fildType1 = th.children("td").eq(2).attr('val'),
            fildType3 = th.children("td").eq(2).attr('data-rollar-ad'),
            fildTypeName3 = th.children("td").eq(2).text(),

            say = $(this).parent('td').parent("tr").children('td').eq(0).text();

        // seçilib?
        var sechilib = "";
        if (th.find("td[sechilib='1']").length) {
            sechilib = "sechilib='1'";
        }

        var disabled = "";
        if (th.attr('del') == '0') disabled = " disabled ";

        th.html("<td style='width:20px;text-align:center;'>" + say + "</td><td " + sechilib + ">" + fildTypesHtml + "</td><td data-rollar-ad='" + fildType3 + "' rollar_id='" + fildTypeName3 + "'>" + fildTypesHtml3 + "</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> </a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> </a></td>");
        $('input[maxlength]').maxlength();
        th.find('select').select2();
        th.find('select').select2('val', fildType1);
        var option = new Option(fildTypeName, tipval, true, true);
        th.find('select[name="tip"]').append(option).trigger('change');
        th.find('select[name="tip"]').select2("data", {'id': tipval, 'text': fildTypeName}, true);


        th.find('.yellow').click(function () {
            th.html("<td style='width:20px;text-align:center;'>" + say + "</td><td val=" + tipval + " " + sechilib + ">" + fildTypeName + "</td><td data-rollar-ad='" + fildType3 + "'>" + fildTypeName3 + "</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> </a><a " + disabled + " href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> </a></td>");
        });
        select();

        // th.find('[name="mesul_shexs"]').select2('data', JSON.parse(fildType2));
        th.find('[name="rollar"]').select2('data', JSON.parse(fildType3));

        e.stopPropagation();
    });

    $('#contact_types>tbody').on("click",".red",function(e) {
        tableId = $(this).parents("table").eq(0).attr("id");
        var idsi = $(this).parent('td').parent('tr').attr("tr_id");
        var t = $(this);
        var alt_qurum = $('input[name="mesul_shexs"]').val();
        var rollar = $('input[name="rollar"]').val();

        swals({
                title: "$9956eminsinizhead$",
                text: "$9956eminsinizdesc$",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "$9956ok$",
                cancelButtonText: "$9956bagla$",
                closeOnConfirm: true
            },
            function () {
                $.post("prodoc/ajax/msk/umumi_forma_sened_novu.php", {'tid':idsi,'ne':"sil"}).done(function (netice) {
                    t.parent('td').parent('tr').remove();
                    if ($('#' + tableId + ' tbody').children('tr').children('td').length == 0) {
                        $('#' + tableId + ' tbody').html("<tr time='null'><td colspan='100%'>$9956empty$</td></tr>");
                    }
                    swals("$9956silindi$", "$9956silindi$", "success");
                });
            });
        e.stopPropagation();
    });
    function select() {
        var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);
        axtarish($('input[name="mesul_shexs"]'), {
            allowClear: false,
            multiple: true,
            getAjaxData: function () {
                return {
                    'ne': 'alt_qurum',
                    'a': ''

                }
            }
        });

        axtarish($('input[name="rollar"]'), {
            allowClear: false,
            multiple: true,
            getAjaxData: function () {
                return {
                    'ne': 'rollar',
                    'a': ''

                }
            }
        });
    }
</script>