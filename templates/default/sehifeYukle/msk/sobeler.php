<?php

$emekdashlarSiyahi = DB::fetchAll("SELECT USERID,CONCAT(Soyadi,' ',Adi) AS user_ad FROM tb_users");

print "<script>";
print "var emekdashlar = {};";

foreach($emekdashlarSiyahi as $emekdashInf)
{
    print "emekdashlar[".(int)$emekdashInf[0]."] = '".escape($emekdashInf[1])."';";
}

print "</script>";

?>

<div class="tab-pane" id="tab_20">
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="shobeler_table">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                        <th>Şöbə adı</th>
                        <th></th>
                        <th style="text-align:right;"><a href="javascript:;" class="btn default btn-xs green" id="shobeler_add"><i class="icon-plus"></i> Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $shobelerSiyahi = DB::fetchAll("SELECT * FROM tb_prodoc_shobeler WHERE silinib='0'");
                    $sira = 0;
                    foreach($shobelerSiyahi as $shobeInfo)
                    {
                        $sira++;
                        print '<tr tr_id="'.(int)$shobeInfo['id'].'">';
                        print '<td>'.$sira.'</td>';
                        print '<td>'.htmlspecialchars($shobeInfo['ad']).'</td>';
                        print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                        print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="shobe_emekdash_table">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                        <th>Əməkdaş</th>
                        <th style="text-align:right;"><a href="javascript:;" class="btn default btn-xs green" id="shobe_emekdash_add"><i class="icon-plus"></i> Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<a data-toggle="modal" href="#basic" id="msk_delete" style="display:none;"></a>
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                Əminsiniz?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Xeyir</button>
                <button type="button" class="btn blue" data-id="beli">Bəli</button>
            </div>
        </div>
    </div>
</div>
<script>

    /******************** SHOBELER *********************/
    $('#shobeler_add').click(function()
    {
        if($("#shobeler_table>tbody tr[time='null']").length)
        {
            $('#shobeler_table>tbody tr').remove();
        }
        var say = $('#shobeler_table>tbody tr').length+1;
        $('#shobeler_table>tbody').append("<tr tr_id='0'><td style='width:20px;text-align:center;'>"+say+"</td><td><input type='text' class='form-control'></td><td align='center' style='padding: 0;'><input type='checkbox'></td><td align='center' style='padding: 0;'><input type='checkbox'></td><td align='center' style='padding: 0;'><input type='checkbox'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td></tr>");
        $('#shobeler_table tbody tr:last input[type="checkbox"]').uniform();
        $('#shobeler_table>tbody .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
    });

    $('#shobeler_table>tbody').on("click",".blue",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            tr_id = tr.attr("tr_id"),
            shobe_ad = tr.children('td').eq(1).children('input').val(),
            ds = $(this).parent('td').parent('tr').children('td').eq(2).find('input').is(":checked")?1:0,
            mm = $(this).parent('td').parent('tr').children('td').eq(3).find('input').is(":checked")?1:0,
            dr = $(this).parent('td').parent('tr').children('td').eq(4).find('input').is(":checked")?1:0,
            sayi = tr.children('td').eq(0).text();
        if(shobe_ad.trim()=="")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#shobeler_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php", {'shobe_ad':shobe_ad,'shobe_id':tr_id,'ds':ds,'mm':mm,'dr':dr},function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla şöbə mövcuddur.</td></tr>');
                    setTimeout(function(){$('#shobeler_table>tbody tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+shobe_ad+"</td><td align='center'>"+(ds=="1"?"<i class='fa fa-check'></i>":"")+"</td><td align='center'>"+(mm=="1"?"<i class='fa fa-check'></i>":"")+"</td><td align='center'>"+(dr=="1"?"<i class='fa fa-check'></i>":"")+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
                }
            });
        }
    });
    $('#shobeler_table>tbody').on("click",".purple",function()
    {
        var adi = $(this).parent('td').parent('tr').children('td').eq(1).text(),
            td = $(this).closest('tr').children('td'),
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text(),
            ds = (td.eq(2).children('i').length>0),
            mm = (td.eq(3).children('i').length>0),
            dr = (td.eq(4).children('i').length>0),
            th = $(this).parent('td').parent('tr');
        $(this).parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td><input type='text' class='form-control' value='"+adi+"'></td><td align='center' style='padding: 0;'><input type='checkbox'"+(ds?" checked":"")+"></td><td align='center' style='padding: 0;'><input type='checkbox'"+(mm?" checked":"")+"></td><td align='center' style='padding: 0;'><input type='checkbox'"+(dr?" checked":"")+"></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td>");
        th.find('input[type="checkbox"]').uniform();
        th.find('.yellow').click(function()
        {
            th.html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+adi+"</td><td align='center'>"+(ds?'<i class="fa fa-check"></i>':'')+"</td><td align='center'>"+(mm?'<i class="fa fa-check"></i>':'')+"</td><td align='center'>"+(dr?'<i class="fa fa-check"></i>':'')+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
        });
    });
    $('#shobeler_table>tbody').on("click",".red",function()
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic [data-id=beli]').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'shobe_id':idsi,'shobe_sil':"shobe_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#shobeler_table>tbody').children('tr').children('td').length)
                {
                    $('#shobeler_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });
    $('#shobeler_table>tbody').on("click","tr",function()
    {
        if($(this).hasClass("sechilib")) return;
        sehifeLoading(1);
        $('#shobeler_table>tbody>tr.sechilib').removeClass("sechilib");
        $(this).addClass("sechilib");
        $("#shobe_emekdash_table>tbody").html("");
        $.post(proBundle + "includes/msk/shobedeki_emekdashlar.php",{'shobe':$(this).attr("tr_id")},function(netice)
        {
            $("#shobe_emekdash_table>tbody").html(netice);
            sehifeLoading(0);
        });
    });

    $('#shobe_emekdash_add').click(function()
    {
        if($("#shobe_emekdash_table>tbody tr[time='null']").length)
        {
            $('#shobe_emekdash_table>tbody tr').remove();
        }
        var say = $('#shobe_emekdash_table>tbody tr').length+1;
        $('#shobe_emekdash_table>tbody').append("<tr tr_id='0'><td style='width:20px;text-align:center;'>"+say+"</td><td><input class='form-control'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a> <a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td></tr>");
        $('#shobe_emekdash_table>tbody>tr:last .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
        $("#shobe_emekdash_table>tbody>tr:last input.form-control").select2({
            allowClear: true,
            ajax: {
                url: "includes/plugins/axtarish.php",
                type: 'POST',
                dataType: 'json',
                data: function (soz)
                {
                    return {
                        'a': soz,
                        'ne': 'emekdash'
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
    $('#shobe_emekdash_table>tbody').on("click",".blue",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            shobe_emekdash_id = tr.children('td').eq(1).children('input').val(),
            sayi = tr.children('td').eq(0).text();
        if((shobe_emekdash_id>0)==false)
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#shobe_emekdash_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php", {'shobe_emekdash_id':shobe_emekdash_id,'shobe_id':$('#shobeler_table>tbody>tr.sechilib').attr("tr_id")},function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla şöbə mövcuddur.</td></tr>');
                    setTimeout(function(){$('#shobe_emekdash_table>tbody tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+tr.children('td').eq(1).children('input').select2("data").text+"</td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
                }
            });
        }
    });

    $('#shobe_emekdash_table>tbody').on("click",".red",function()
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic [data-id=beli]').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'shobe_emekdash_id':idsi,'shobe_emekdash_sil':"shobe_emekdash_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#shobe_emekdash_table>tbody').children('tr').children('td').length)
                {
                    $('#shobe_emekdash_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });

</script>