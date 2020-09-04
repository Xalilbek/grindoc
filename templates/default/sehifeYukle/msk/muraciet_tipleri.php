<style>
    tr.sil_hide td:last-child .btn.red {
        display: none;
    }
</style>
<div class="tab-pane" id="tab_20">
    <?php
    $haveToCheckLastClosingOperation =
        (int)Service\Option\Option::getOrCreateValue('haveToCheckLastClosingOperation', 1)
    ;
    ?>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-10">
            Əməliyyatlar bitəndən sonra sənəd avtomatik bağlanmasın:
            <input
                    type="checkbox"
                    id="haveToCheckLastClosingOperation"
                <?php if ($haveToCheckLastClosingOperation): ?> checked <?php endif; ?>
            >
        </div>
        <script>
            $(function() {
                $("#haveToCheckLastClosingOperation").on('click', function() {
                    var haveToCheckLastClosingOperation = +$(this).is(':checked');
                    $.post('includes/msk/options_edit.php', {option: 'haveToCheckLastClosingOperation', value: haveToCheckLastClosingOperation});
                });
                $("#haveToCheckLastClosingOperation").uniform();
            });
        </script>
    </div>
    <div class="row">
        <div class="col-md-10">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="muraciet_tip_table">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                        <th>Adı</th>
                        <th>Sərbəst</th>
                        <th>Əməliyyat</th>
                        <th>Daxil olan sənədin statusu</th>
                        <th title="Seçim etmək imkanı">Cavab gozlənilmir</th>
                        <th title="Sənədə bağla əlaqələndirmə">Əlaqələndirmə</th>
                        <th></th>
                        <th style="text-align:right;"><a href="javascript:;" class="btn default btn-xs green" id="muraciet_tip_add"><i class="icon-plus"></i> Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $muraciet_tipSiyahi = DB::fetchAll("SELECT * FROM tb_prodoc_muraciet_tip WHERE silinib='0'");
                    $sira = 0;
                    foreach($muraciet_tipSiyahi as $muracietInfo)
                    {
                        $sira++;
                        $sil_hide =(!is_null($muracietInfo['extra_id'])?'sil_hide':'');
                        print '<tr tr_id="'.(int)$muracietInfo['id'].'" class="'.$sil_hide.'">';
                        print '<td>'.$sira.'</td>';
                        print '<td>'.htmlspecialchars($muracietInfo['ad']).'</td>';
                        print '<td align="center">'.((int)$muracietInfo['serbest']==1?'<i class="fa fa-check"></i>':'').'</td>';
                        print '<td align="center">'.((int)$muracietInfo['emeliyyat']==1?'<i class="fa fa-check"></i>':'').'</td>';
                        print '<td align="center" val="'.$muracietInfo['dos_status'].'">';
                        print getDosStatusTitle($muracietInfo['dos_status']);
                        print '</td>';
                        print '<td align="center">'.((int)$muracietInfo['cavab_gozlenilmir']==1?'<i class="fa fa-check"></i>':'').'</td>';
                        print '<td align="center">'.((int)$muracietInfo['elaqelendirme']==1?'<i class="fa fa-check"></i>':'').'</td>';
                        print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                        if (is_null($muracietInfo['extra_id'])) {
                            print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td></tr>';
                        } else {
                            print '<td style="width:100px;text-align:center;"></tr>';
                        }
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

    var fildTypesHtml = '<select class="dos_status form-control">‌<option value="2">Bağlı</option><option value="1">Açıq</option><option value="3">Seçimli</option></select>';


    /******************** SHOBELER *********************/
    $('#muraciet_tip_add').click(function()
    {
        if($("#muraciet_tip_table>tbody tr[time='null']").length)
        {
            $('#muraciet_tip_table>tbody tr').remove();
        }
        var say = $('#muraciet_tip_table>tbody tr').length+1;
        $('#muraciet_tip_table>tbody').append("<tr tr_id='0'><td style='width:20px;text-align:center;'>"+say+"</td><td><input type='text' class='form-control'></td><td align='center' style='padding: 0;'><input type='checkbox'></td><td align='center' style='padding: 0;'><input type='checkbox'></td><td>" + fildTypesHtml + "</td><td align='center' style='padding: 0;'><input type='checkbox'></td><td align='center' style='padding: 0;'><input type='checkbox'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td></tr>");
        $('#muraciet_tip_table tbody tr:last input[type="checkbox"]').uniform();
        $('#muraciet_tip_table tbody tr:last select').select2();
        $('#muraciet_tip_table>tbody .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });

        $('.dos_status').on('change', function () {
            if($(this).val()==3){
                $(this).parents('tr').children('td').eq(6).find('span').removeClass('checked');
                $(this).parents('tr').children('td').eq(6).find('input').removeAttr('checked');
                $(this).parents('tr').children('td').eq(6).find('input').prop( "disabled", true );
            }else {
                $(this).parents('tr').children('td').eq(6).find('input').prop( "disabled", false );

            }
        })
    });

    $('#muraciet_tip_table>tbody').on("click",".blue",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            tr_id = tr.attr("tr_id"),
            muraciet_ad = tr.children('td').eq(1).children('input').val(),
            serbest = $(this).parent('td').parent('tr').children('td').eq(2).find('input').is(":checked")?1:0,
            emeliyyat = $(this).parent('td').parent('tr').children('td').eq(3).find('input').is(":checked")?1:0,
            cavab_gozlenilmir = $(this).parent('td').parent('tr').children('td').eq(5).find('input').is(":checked")?1:0,
            elaqelendirme = $(this).parent('td').parent('tr').children('td').eq(6).find('input').is(":checked")?1:0,
            fildType = tr.children('td').eq(4).find("select").val(),
            fildTypeName = tr.children('td').eq(4).find("select option:selected").text(),
            sayi = tr.children('td').eq(0).text();

        if(muraciet_ad.trim()=="")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#muraciet_tip_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php", {'muraciet_ad':muraciet_ad, "fildType":fildType,'muraciet_id':tr_id,'serbest':serbest,'emeliyyat':emeliyyat,'cavab_gozlenilmir':cavab_gozlenilmir, 'elaqelendirme':elaqelendirme },function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla şöbə mövcuddur.</td></tr>');
                    setTimeout(function(){$('#muraciet_tip_table>tbody tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+muraciet_ad+"</td><td align='center'>"+(serbest=="1"?"<i class='fa fa-check'></i>":"")+"</td><td align='center'>"+(emeliyyat=="1"?"<i class='fa fa-check'></i>":"")+"</td><td val="+fildType+">" + fildTypeName + "</td><td align='center'>"+(cavab_gozlenilmir=="1"?"<i class='fa fa-check'></i>":"")+"</td><td align='center'>"+(elaqelendirme=="1"?"<i class='fa fa-check'></i>":"")+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
                }
            });
        }
    });
    $('#muraciet_tip_table>tbody').on("click",".purple",function()
    {
        var adi = $(this).parent('td').parent('tr').children('td').eq(1).text(),
            td = $(this).closest('tr').children('td'),
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text(),
            serbest = (td.eq(2).children('i').length>0),
            emeliyyat = (td.eq(3).children('i').length>0),
            cavab_gozlenilmir = (td.eq(5).children('i').length>0),
            elaqelendirme = (td.eq(6).children('i').length>0),
            th = $(this).parent('td').parent('tr'),
            fildType = th.children("td").eq(4).attr('val'),
            fildTypeName = th.children("td").eq(4).text();

        if (td.eq(4).attr('val') == 3) {
            $(this).parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>" + sayi + "</td><td><input type='text' class='form-control' value='" + adi + "'></td><td align='center' style='padding: 0;'><input type='checkbox'" + (serbest ? " checked" : "") + "></td><td align='center' style='padding: 0;'><input type='checkbox'" + (emeliyyat ? " checked" : "") + "></td><td>" + fildTypesHtml + "</td><td align='center' style='padding: 0;'><input type='checkbox'" + (cavab_gozlenilmir ? " checked" : "") + "></td><td align='center' style='padding: 0;'><input type='checkbox'" + (elaqelendirme ? "checked" : "") + " disabled></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td>");

        }else{
            $(this).parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td><input type='text' class='form-control' value='"+adi+"'></td><td align='center' style='padding: 0;'><input type='checkbox'"+(serbest?" checked":"")+"></td><td align='center' style='padding: 0;'><input type='checkbox'"+(emeliyyat?" checked":"")+"></td><td>" + fildTypesHtml + "</td><td align='center' style='padding: 0;'><input type='checkbox'"+(cavab_gozlenilmir?" checked":"")+"></td><td align='center' style='padding: 0;'><input type='checkbox'"+(elaqelendirme? "checked":"")+"></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td>");

        }

        th.find('input[type="checkbox"]').uniform();
        th.find('select').select2().select2('val', fildType);
        th.find('.yellow').click(function()
        {
            th.html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+adi+"</td><td align='center'>"+(serbest?'<i class="fa fa-check"></i>':'')+"</td><td align='center'>"+(emeliyyat?'<i class="fa fa-check"></i>':'')+"</td><td align='center' val="+fildType+">"+fildTypeName+"</td><td align='center'>"+(cavab_gozlenilmir?'<i class="fa fa-check"></i>':'')+"</td><td align='center'>"+(elaqelendirme?'<i class="fa fa-check"></i>':'')+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
        });

        $('.dos_status').on('change', function () {
            if($(this).val()==3){
                $(this).parents('tr').children('td').eq(6).find('span').removeClass('checked');
                $(this).parents('tr').children('td').eq(6).find('input').removeAttr('checked');
                $(this).parents('tr').children('td').eq(6).find('input').prop( "disabled", true );
            }else {
                $(this).parents('tr').children('td').eq(6).find('input').prop( "disabled", false );

            }
        })
    });
    $('#muraciet_tip_table>tbody').on("click",".red",function()
    {
        var t = $(this),
            iserbesti = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('iserbesti', iserbesti);
        $('#msk_delete').click();
        $('#basic [data-id=beli]').unbind('click').click(function()
        {
            var iserbesti = $('#basic').attr('iserbesti');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'muraciet_id':iserbesti,'muraciet_sil':"muraciet_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#muraciet_tip_table>tbody').children('tr').children('td').length)
                {
                    $('#muraciet_tip_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });



</script>