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

<div class="tab-pane" id="tab_7">
    <div class="row">
        <div class="col-md-10">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="skan_merkezi_table">
                    <thead>
                    <tr>
                        <th style="width: 40px;">№</th>
                        <th style="max-width: 250px;">Şöbə</th>
                        <th style="max-width: 250px;">Bölmə</th>
                        <th style="max-width: 250px;">Əməkdaşlar</th>
                        <th style="text-align:center;" colspan="2"><a href="javascript:;" class="btn default btn-xs green" id="skan_merkezi_add"><i class="icon-plus"></i> Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    $privsler = DB::fetchAll("SELECT *, 
                      emekdashlar, 
                      ( SELECT ad FROM tb_prodoc_shobeler WHERE id = tb_prodoc_skan_merkezi_privs_dpb.shobe_id ) AS shobe, 
                      ( CASE WHEN bolme = '1' THEN N'Daxil olan Sənəd - Fiziki şəxs' WHEN bolme = '2' THEN N'Daxil olan Sənəd - Hüquqi şəxs' WHEN bolme = '3' THEN N'Çıxan Sənəd' END ) AS bolme_ad 
                      FROM tb_prodoc_skan_merkezi_privs_dpb");
                    $sira = 0;

                    foreach($privsler as $priv)
                    {
                        $emekdash_adlar_arr = [];
                        $exploded_emekdashlar = explode(',',$priv['emekdashlar']);
                        foreach ($exploded_emekdashlar as $emekdash) {
                            $emekdash_ad = DB::fetchColumn("SELECT user_ad FROM v_user_adlar WHERE USERID = $emekdash");
                            $emekdash_adlar_arr[] = $emekdash_ad;
                        }
                        $emekdash_adlar = implode(', ', $emekdash_adlar_arr);

                        $sira++;
                        print '<tr tr_id="'.(int)$priv['id'].'">';
                        print '<td>'.$sira.'</td>';
                        print '<td shobe="'.$priv['shobe_id'].'">'.escape($priv['shobe']).'</td>';
                        print '<td bolme="'.$priv['bolme'].'">'.escape($priv['bolme_ad']).'</td>';
                        print '<td userler="'.$priv['emekdashlar'].'">'.escape($emekdash_adlar).'</td>';
                        print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                        print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td></tr>';
                    }
                    ?>

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

    var emekdashlarHtml = '';
    for(var uid in emekdashlar)
    {
        emekdashlarHtml += "<option value='"+uid+"'>"+emekdashlar[uid]+"</option>";
    }

    $('#skan_merkezi_add').click(function()
    {
        if($("#skan_merkezi_table>tbody tr[time='null']").length)
        {
            $('#skan_merkezi_table>tbody tr').remove();
        }
        var say = $('#skan_merkezi_table>tbody tr').length+1;
        $('#skan_merkezi_table>tbody').append("<tr tr_id='0'>" +
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td><input type='text' placeholder='Şöbəni seçin...' class='form-control shobeler'></td>" +
            "<td><select class='form-control select2me'>" +
                "<option value='1'>Daxil olan Sənəd - Fiziki şəxs</option>" +
                "<option value='2'>Daxil olan Sənəd - Hüquqi şəxs</option>" +
                "<option value='3'>Çıxan Sənəd</option>" +
            "</select></td>" +
            "<td><select placeholder='Əməkdaşları seçin...' class='form-control emekdash' multiple>"+emekdashlarHtml+"</select></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td>" +
            "<td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td></tr>");

        $('#skan_merkezi_table>tbody .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });

        $("#skan_merkezi_table tbody tr:last .shobeler").select2({
            allowClear: true,
            ajax: {
                url: "prodoc/includes/plugins/axtarish.php",
                type: 'POST',
                dataType: 'json',
                data: function (soz) {
                    return {
                        'ne':'prodoc_shobeler',
                        'a':soz
                    };
                },
                results: function(data,a) {
                    return data;
                },
                cache: true
            }
        });
        $("#skan_merkezi_table tbody tr:last .emekdash").select2();
    });

    $('#skan_merkezi_table>tbody').on("click",".blue",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            tr_id = tr.attr("tr_id"),
            sayi = tr.children('td').eq(0).text(),
            td = $(this).parent('td').parent('tr').children('td'),
            shobe_id    = td.eq(1).find('input').select2("val"),
            shobe_ad    = shobe_id.trim() != '' ? td.eq(1).find('input').select2("data").text : '',
            bolme_id       = td.eq(2).find('select').val(),
            bolme_ad       = td.eq(2).find('select').children(":selected").text(),
            emekdashlar_id    = td.eq(3).find('select').select2("val"),
            emekdashlar_data  = td.eq(3).find('select').select2("data");

        var emekdashlar_adlar = emekdashlar_data.map(e => e.text).join(",");

        if(shobe_ad.trim()=="" || bolme_ad.trim()=="" || emekdashlar_id.length <= 0)
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#skan_merkezi_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php",
                {
                    'priv_id' : tr_id,
                    'shobe_id': shobe_id,
                    'bolme_id': bolme_id,
                    'emekdashlar_id': emekdashlar_id
                },
                function(result)
                {
                    if(result=="error")
                    {
                        tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla şöbə mövcuddur.</td></tr>');
                        setTimeout(function(){$('#skan_merkezi_table>tbody tr[error]').remove();}, 3000);
                    }
                    else
                    {
                        tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                            "<td shobe='"+shobe_id+"'>"+shobe_ad+"</td>" +
                            "<td bolme='"+bolme_id+"'>"+bolme_ad+"</td>" +
                            "<td userler='"+emekdashlar_id+"'>"+emekdashlar_adlar+"</td>" +
                            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td>" +
                            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
                    }
                }
            );
        }
    });

    $("#skan_merkezi_table").on("click",".purple",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            tr_id = tr.attr("tr_id"),
            say = tr.children('td').eq(0).text(),
            td = $(this).parent('td').parent('tr').children('td'),
            shobe_id = td.eq(1).attr("shobe"),
            shobe_ad = td.eq(1).text(),
            bolme_id = td.eq(2).attr("bolme"),
            bolme_ad = td.eq(2).text(),
            emekdashlar_id = td.eq(3).attr("userler");

        var tr_clonu = tr.clone();

        tr.html(
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td><input type='text' class='form-control shobeler'></td>" +
            "<td><select class='form-control select2me'>" +
            "<option value='1'>Daxil olan Sənəd - Fiziki şəxs</option>" +
            "<option value='2'>Daxil olan Sənəd - Hüquqi şəxs</option>" +
            "<option value='3'>Çıxan Sənəd</option>" +
            "</select></td>"
        );

        tr.find('.shobeler').select2({
            allowClear: true,
            ajax: {
                url: "prodoc/includes/plugins/axtarish.php",
                type: 'POST',
                dataType: 'json',
                data: function (soz) {
                    return {
                        'ne':'prodoc_shobeler',
                        'a':soz
                    };
                },
                results: function(data,a) {
                    return data;
                },
                cache: true
            }
        });


        tr.find('.shobeler').select2('data', {id: shobe_id, text: shobe_ad});
        tr.find('.select2me').val(bolme_id);

        tr.append("<td><select placeholder='Əməkdaşları seçin...' class='form-control emekdash' multiple>"+emekdashlarHtml+"</select></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td>" +
            "<td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td>"
        );

        tr.find('.emekdash').val(emekdashlar_id.split(",")).select2();

        tr.find('.yellow').click(function()
        {
            $(this).parent('td').parent('tr').html(tr_clonu.html());
        });
    });

    $('#skan_merkezi_table>tbody').on("click",".red",function()
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic [data-id=beli]').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'priv_id':idsi,'priv_sil':"priv_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#skan_merkezi_table>tbody').children('tr').children('td').length)
                {
                    $('#skan_merkezi_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });

</script>