<style type="text/css">
    #tab_13 tbody[vezife='menyular1'] td{
        cursor: pointer;
    }
</style>

<div id="tab_13">
    <div class="row">
        <div class="col-md-8">
            <table class="table table-striped table-bordered table-advance table-hover" id="tibb_muessisesi_table">
                <thead>
                <tr>
                    <th style="width: 30px;">№</th>
                    <th>Tibb müəssisəsi</th>
                    <th>Təyinat</th>
                    <th>Region</th>
                    <th></th>
                    <th style="text-align:right;"><a href="javascript:;" class="btn default btn-xs green" id="tibb_muessisesi_add"><i class="icon-plus"></i> Əlavə et</a></th>
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

    $("#tab_13 tbody[vezife='menyular1'] tr:first td:first").click();

    $('#tibb_muessisesi_add').click(function()
    {
        if($("#tibb_muessisesi_table>tbody tr[time='null']").length)
        {
            $('#tibb_muessisesi_table>tbody tr').remove();
        }
        var say = $('#tibb_muessisesi_table>tbody tr').length+1;
        $('#tibb_muessisesi_table>tbody').append("<tr tr_id='0'><td style='width:20px;text-align:center;'>"+say+"</td><td><input type='text' class='form-control'></td><td><select class='form-control' placeholder='Təyinat'><option></option><option value='1'>Dövlət</option><option value='0'>Özəl</option></select></td><td><input class='form-control' placeholder='Region'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td></tr>");
        $('#tibb_muessisesi_table>tbody>tr:last .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
        $('#tibb_muessisesi_table>tbody>tr:last select').select2();
        $('#tibb_muessisesi_table>tbody>tr:last>td:eq(3)>input').select2({
            allowClear: true,
            ajax: {
                url: "includes/plugins/axtarish.php",
                type: 'POST',
                dataType: 'json',
                data: function (soz)
                {
                    return {
                        'a': soz,
                        'ne': 'region'
                    };
                },
                results: function(data,a)
                {
                    return data
                },
                cache: true
            }
        });
    });

    $('#tibb_muessisesi_table>tbody').on("click",".blue",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            tr_id = tr.attr("tr_id"),
            tibb_muessisesi = tr.children('td').eq(1).children('input').val(),
            dovlet = tr.children('td').eq(2).children('select').val(),
            region = tr.children('td').eq(3).children('input').val(),
            sayi = tr.children('td').eq(0).text();
        if(tibb_muessisesi.trim()=="" || isNaN(parseInt(dovlet)) || (region>0)==false)
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#tibb_muessisesi_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post("prodoc/ajax/msk/msk_prodoc_tibb_muessisesi.php", {'tibb_muessisesi':tibb_muessisesi,'dovlet':dovlet,'region':region,'tibb_muessisei_id':tr_id},function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla vəzifə mövcuddur.</td></tr>');
                    setTimeout(function(){$('#tibb_muessisesi_table>tbody tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+tibb_muessisesi+"</td><td>"+(dovlet==1?"Dövlət":"Özəl")+"</td><td region='"+region+"'>"+(tr.children('td').eq(3).children('input').select2("data").text)+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
                }
            });
        }
    });


    $('#tibb_muessisesi_table>tbody').on("click",".purple",function()
    {
        var th = $(this).parent('td').parent('tr'),
            td = th.children("td"),
            adi = td.eq(1).text(),
            teyinat = td.eq(2).text(),
            region_ad = td.eq(3).text(),
            region_id = td.eq(3).attr("region"),
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text();
        $(this).parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td><input type='text' class='form-control' value='"+adi+"'></td><td><select class='form-control' placeholder='Təyinat'><option></option><option value='1'"+(teyinat=="Dövlət"?" selected":"")+">Dövlət</option><option value='0'"+(teyinat=="Özəl"?" selected":"")+">Özəl</option></select></td><td><input class='form-control' placeholder='Region'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td>");
        th.find('select').select2();
        th.find('td:eq(3)>input').select2({
            allowClear: true,
            ajax: {
                url: "includes/plugins/axtarish.php",
                type: 'POST',
                dataType: 'json',
                data: function (soz)
                {
                    return {
                        'a': soz,
                        'ne': 'region'
                    };
                },
                results: function(data,a)
                {
                    return data
                },
                cache: true
            }
        }).select2("data",{"id":region_id,"text":region_ad});
        th.find('.yellow').click(function()
        {
            th.html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+adi+"</td><td>"+teyinat+"</td><td>"+region_ad+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
        });
    });

    $('#tibb_muessisesi_table>tbody').on("click",".red",function(e)
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#msk_delete').click();

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
            function(){
                $.post("prodoc/ajax/msk/msk_prodoc_tibb_muessisesi.php", {'tibb_muessisei_id':idsi,'tibb_muessisei_sil':"tibb_muessisei_sil"}).done(function(netice)
                {
                    $('#basic .default').click();
                    t.parent('td').parent('tr').remove();
                    if(!$('#tibb_muessisesi_table>tbody').children('tr').children('td').length)
                    {
                        $('#tibb_muessisesi_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                    }
                    swals("$9956silindi$", "$9956silindi$", "success");
                });
            });
        e.stopPropagation();
    });
</script>