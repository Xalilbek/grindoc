<style>
    #portledbody {
        overflow: scroll;
    }
    .sechilib {
        background: #cecece !important;
    }
    #tab_16 table>thead>tr>th:last-child {
        width: 80px !important;
    }
</style>

<div class="tab-pane" id="tab_16" style="width: 200%;">
    <div class="row">
        <div class="col-md-3">
<!--            <span style="position: fixed; left: 260px; cursor: pointer; height: 50px; padding-top: 20px;"><i class="fa fa-arrow-left leftArrow"></i></span>-->
<!--            <span style="position: fixed; right: 55px; cursor: pointer; height: 50px; padding-top: 20px;"><i class="fa fa-arrow-right rightArrow"></i></span>-->
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="sened_tip_df_table">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                        <th>Məktubun tipi</th>
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Qısa məzmun">QM <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Elektron Səhiyyə">ES <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Procall">PC <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Prodoc">PD <i class="fa fa-info-circle"></i></th>-->
                        <th><a href="javascript:;" class="btn default btn-xs green" id="sened_tip_df_add"><i class="icon-plus"></i> Yeni</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $filtr = 2;
//                    $filtr2 = $filtr==1?"(nov_dh='1' OR nov_cs='1')":"nov_df='1'";
                    $filtr2 = '1=1';
                    $query = DB::fetchAll("SELECT * FROM tb_mektubun_tipleri WHERE silinib='0' AND (parent_id=0  OR  parent_id is null)");
                    if(count($query)==0)
                    {
                        print "<tr time='null'><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query))
                        {
                            $i++;
                            print '<tr tr_id="'.(int)$mass['id'].'" tip="'.htmlspecialchars($mass['sened_tip'],ENT_QUOTES).'">';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['ad'],ENT_QUOTES).'</td>';
                            if($filtr==1)
                            {
                                print '<td align="center">'.($mass['cavab']=="1"?"<i class='fa fa-check'></i>":"").'</td>';
                                print '<td align="center">'.($mass['nov_dh']==1?'<i class="fa fa-check"></i>':'').'</td>';
                                print '<td align="center">'.($mass['nov_cs']==1?'<i class="fa fa-check"></i>':'').'</td>';
                            }
//                            else
//                            {
//                                print '<td align="center">'.((int)$mass['qisa_mezmun_var']=="1"?"<i class='fa fa-check'></i>":"").'</td>';
//                                print '<td align="center">'.((int)$mass['elektron']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                                print '<td align="center">'.((int)$mass['procall']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                                print '<td align="center">'.((int)$mass['prodoc']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                            }
                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i></a><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i></a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="alt_sened_tip_df_table">
                    <thead>
                    <tr>
                        <th style="width: 20px;">№</th>
                        <th>Mövzu</th>
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Elektron Səhiyyə">ES <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Procall">PC <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Prodoc">PD <i class="fa fa-info-circle"></i></th>-->
                        <th><a href="javascript:;" class="btn default btn-xs green" id="alt_sened_tip_df_add"><i class="icon-plus"></i> Yeni</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $query = DB::fetchAll("SELECT * FROM tb_mektubun_tipleri WHERE silinib='0' AND parent_id>0 ");
                    if(count($query)==0)
                    {
                        print "<tr time='null'><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query))
                        {
                            $i++;
                            print '<tr tr_id="'.(int)$mass['id'].'" tip="'.(int)$mass['parent_id'].'">';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['ad']).'</td>';
//                            print '<td align="center">'.((int)$mass['elektron']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                            print '<td align="center">'.((int)$mass['procall']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                            print '<td align="center">'.(int)($mass['prodoc']==1?'<i class="fa fa-check"></i>':'').'</td>';
                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i></a><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i></a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="sened_mezmunu3_table">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                        <th>Alt mövzu</th>
                        <th style="width: 140px;">Alt tip</th>
                        <th style="width: 60px;"></th>
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Elektron Səhiyyə">ES <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Procall">PC <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Prodoc">PD <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Dərman">Dərman <i class="fa fa-info-circle"></i></th>-->
                        <th><a href="javascript:;" class="btn default btn-xs green" id="sened_mezmunu3_add"><i class="icon-plus"></i> Yeni</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $movzular = DB::fetchAll("SELECT * FROM tb_mektubun_tipleri WHERE silinib='0' AND (parent_id=0 OR parent_id IS NULL)");
                    $sira = 0;
                    while($movzuInf = array_shift($movzular))
                    {
                        $sira++;
                        print '<tr tr_id="'.(int)$movzuInf['id'].'" tr_id2="'.(int)$movzuInf['id'].'" style="display: none;">';
                        print '<td>'.$sira.'</td>';
                        print '<td>'.htmlspecialchars($movzuInf['ad']).'</td>';
                        print '<td>'.($movzuInf['nozologiya']==1?"Nazalogia":($movzuInf['nozologiya']==2?"Tibb müəssisələri":($movzuInf['nozologiya']==3?"Tibb müəssisəsi/Nazalogiya":"Yoxdur"))).'</td>';
                        print '<td style="visibility: '.($movzuInf['nozologiya']==2||$movzuInf['nozologiya']==3?"visible":"hidden").';">'.($movzuInf['nozologiya']==2||$movzuInf['nozologiya']==3?($movzuInf['tibb_muessisesi_tip']==1?"Dövlət":($movzuInf['tibb_muessisesi_tip']==0?"Özəl":"Dövlət, Özəl")):"").'</td>';
//                        print '<td align="center">'.((int)$movzuInf['elektron']==1?'<i class="fa fa-check"></i> <i forma data-original-title="Forma" class="tooltips fa fa-edit" style="cursor:pointer"></i>':'').'</td>';
//                        print '<td align="center">'.((int)$movzuInf['procall']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                        print '<td align="center">'.((int)$movzuInf['prodoc']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                        print '<td align="center">'.((int)$movzuInf['derman']==1?'<i class="fa fa-check"></i>':'').'</td>';
                        print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i></a><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i></a></td>';
                        print '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Alt mezmunlar -->

        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="sened_mezmunu4_table">
                    <thead>
                    <tr>
                        <th style="width: 20px;">№</th>
                        <th>Məktubun məzmunu</th>
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Elektron Səhiyyə">ES <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Procall">PC <i class="fa fa-info-circle"></i></th>-->
<!--                        <th style="width: 50px;" class="tooltipsBu" data-container="body" data-original-title="Prodoc">PD <i class="fa fa-info-circle"></i></th>-->
                        <th><a href="javascript:;" id="sened_mezmunu4_add" class="btn default btn-xs green"><i class="icon-plus"></i> Yeni</a></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php

                    $sira = 0;
                    while($movzuInf = array_shift($movzular))
                    {
                        $sira++;
                        print '<tr tip="' . (int)$movzuInf['parent_id'] . '"tr_id="'.(int)$movzuInf['id'].'" tr_id2="'.(int)$movzuInf['parent_id'].'" style="display: none;">';
                        print '<td>'.$sira.'</td>';
                        print '<td>'.htmlspecialchars($movzuInf['ad']).'</td>';
//                        print '<td align="center">'.((int)$movzuInf['elektron']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                        print '<td align="center">'.((int)$movzuInf['procall']==1?'<i class="fa fa-check"></i>':'').'</td>';
//                        print '<td align="center">'.((int)$movzuInf['prodoc']==1?'<i class="fa fa-check"></i>':'').'</td>';
                        print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i></a><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i></a></td>';
                        print '</tr>';
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

    function table_hide_show(table_name) {

        if (table_name == 'sened_mezmunu4_table') return;
        if (table_name != 'sened_mezmunu3_table') $('#sened_mezmunu3_table').hide();;

        $('#sened_mezmunu4_table').hide();

        if (table_name == 'alt_sened_tip_df_table') $('#sened_mezmunu3_table').fadeIn();
        if (table_name == 'sened_mezmunu3_table') $('#sened_mezmunu4_table').fadeIn();
    }

    $('#tab_16 tbody').on("click","tr",function()
    {
        table_hide_show($(this).closest('table').attr('id'));
    });

    $('#sened_tip_df_table>tbody').on("click","tr",function()
    {
        $('#sened_tip_df_table tbody tr.sechilib').removeClass("sechilib");
        $(this).addClass("sechilib");
        $("#alt_sened_tip_df_table tbody tr").hide();
        $("#alt_sened_tip_df_table tbody tr[tip='"+$(this).attr("tr_id")+"']").show();
        $('#sened_mezmunu3_table>tbody>tr').hide();
        $("#alt_sened_tip_df_table tbody tr[tip='"+$(this).attr("tr_id")+"']:eq(0)").click();
    });
    $('#sened_tip_df_add').click(function()
    {
        if($("#sened_tip_df_table tbody tr[time='null']").length)
        {
            $('#sened_tip_df_table tbody tr').remove();
        }
        var say = $('#sened_tip_df_table tbody tr').length+1;
        $('#sened_tip_df_table tbody').append("<tr tr_id='0'>" +
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td><input type='text' class='form-control' style='height: 30px;'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
            "<td style='text-align:center;'>" +
                "<a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a>" +
                "<a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a>" +
            "</td>" +
            "</tr>");
        $('#sened_tip_df_table tbody tr:last input[type="checkbox"]').uniform();
        $('#sened_tip_df_table tbody tr:last .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
    });
    $('#sened_tip_df_table>tbody').on('click','.blue',function()
    {
        var sened_tip = $(this).parent('td').parent('tr').children('td').eq(1).children('input').val(),
            qisa_mezmun_var = $(this).parent('td').parent('tr').children('td').eq(2).find('input').is(":checked")?1:0,
            es = $(this).parent('td').parent('tr').children('td').eq(3).find('input').is(":checked")?1:0,
            pc = $(this).parent('td').parent('tr').children('td').eq(4).find('input').is(":checked")?1:0,
            pd = $(this).parent('td').parent('tr').children('td').eq(5).find('input').is(":checked")?1:0,
            t = $(this),
            tr = t.parent('td').parent('tr'),
            tr_id = tr.attr("tr_id"),
            sayi = tr.children('td').eq(0).text();
        if(sened_tip=="")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#sened_tip_df_table tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php", {'sened_tip':sened_tip,'sened_tip_id':tr_id,'qisa_mezmun_var':qisa_mezmun_var,'es':es,'pc':pc,'pd':pd,'nov_df':1},function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla vəzifə mövcuddur.</td></tr>');
                    setTimeout(function(){$('#sened_tip_df_table tbody tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                        "<td>"+sened_tip+"</td>" +
//                        "<td>"+(qisa_mezmun_var==1?"<i class='fa fa-check'></i>":"")+"</td>" +
//                        "<td>"+(es==1?"<i class='fa fa-check'></i>":"")+"</td>" +
//                        "<td>"+(pc==1?"<i class='fa fa-check'></i>":"")+"</td>" +
//                        "<td>"+(pd==1?"<i class='fa fa-check'></i>":"")+"</td>" +
                        "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
                }
            });
        }
    });
    //bura
    $('#sened_tip_df_table>tbody').on('click','.purple',function()
    {
        var td = $(this).closest('tr').children('td'),
            adi = td.eq(1).text(),
//            qisa_mezmun_var = td.eq(2).find("i").length>0?1:0,
//            es = td.eq(3).find("i").length>0?1:0,
//            pc = td.eq(4).find("i").length>0?1:0,
//            pd = td.eq(5).find("i").length>0?1:0,
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text(),
            th = $(this).parent('td').parent('tr');
        $(this).parent('td').parent('tr').html("" +
            "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
            "<td><input type='text' class='form-control' style='height: 30px;' value='"+adi+"'></td>" +
//            "<td><input type='checkbox' "+(qisa_mezmun_var==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(es==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(pc==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(pd==1?" checked":"")+"></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td>");
        th.find('input[type="checkbox"]').uniform();
        th.find('.yellow').click(function()
        {
            th.html("" +
                "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                "<td>"+adi+"</td>" +
//                "<td>"+td.eq(2).html()+"</td>" +
//                "<td>"+td.eq(3).html()+"</td>" +
//                "<td>"+td.eq(4).html()+"</td>" +
//                "<td>"+td.eq(5).html()+"</td>" +
                "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
        });
    });
    $('#sened_tip_df_table>tbody').on('click','.red',function()
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic .blue').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'sened_tip_id':idsi,'sened_tip_sil':"sened_tip_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#sened_tip_df_table tbody').children('tr').children('td').length)
                {
                    $('#sened_tip_df_table tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });

    $('#alt_sened_tip_df_add').click(function()
    {
        if($("#alt_sened_tip_df_table>tbody tr[time='null']").length)
        {
            $('#alt_sened_tip_df_table>tbody tr').remove();
        }
        var say = $('#alt_sened_tip_df_table>tbody tr').length+1,
            tip = $('#sened_tip_df_table tbody tr.sechilib').attr("tr_id");
        $('#alt_sened_tip_df_table>tbody').append("<tr tr_id='0' tip='"+tip+"'>" +
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td><input type='text' class='form-control' style='height: 30px;'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td></tr>");
        $('#alt_sened_tip_df_table>tbody tr:last .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
        $('#alt_sened_tip_df_table tbody tr:last input[type="checkbox"]').uniform();
    });
    $('#alt_sened_tip_df_table>tbody').on('click','.blue',function()
    {
        var sened_tip = $(this).parent('td').parent('tr').children('td').eq(1).children('input').val(),
            t = $(this),
            tr = t.parent('td').parent('tr'),
            tr_id = tr.attr("tr_id"),
            tip = tr.attr("tip"),
            sayi = tr.children('td').eq(0).text(),
            es = $(this).parent('td').parent('tr').children('td').eq(2).find('input').is(":checked")?1:0,
            pc = $(this).parent('td').parent('tr').children('td').eq(3).find('input').is(":checked")?1:0,
            pd = $(this).parent('td').parent('tr').children('td').eq(4).find('input').is(":checked")?1:0;
        if(sened_tip=="")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#alt_sened_tip_df_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php", {'alt_sened_tip':sened_tip,'alt_sened_tip_id':tr_id,'tip':tip,'es':es,'pc':pc,'pd':pd,'nov_df':1},function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla vəzifə mövcuddur.</td></tr>');
                    setTimeout(function(){$('#alt_sened_tip_df_table>tbody tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id",result).html("" +
                        "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                        "<td>"+sened_tip+"</td>" +
//                        "<td>"+(es==1?"<i class='fa fa-check'></i>":"")+"</td>" +
//                        "<td>"+(pc==1?"<i class='fa fa-check'></i>":"")+"</td>" +
//                        "<td>"+(pd==1?"<i class='fa fa-check'></i>":"")+"</td>" +
                        "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
                }
            });
        }
    });

    $('#alt_sened_tip_df_table>tbody').on('click','.purple',function()
    {
        var td = $(this).closest('tr').children('td'),
            adi = td.eq(1).text(),
            es = td.eq(2).find("i").length>0?1:0,
            pc = td.eq(3).find("i").length>0?1:0,
            pd = td.eq(4).find("i").length>0?1:0,
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text(),
            th = $(this).parent('td').parent('tr');

        $(this).parent('td').parent('tr').html("" +
            "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
            "<td><input type='text' class='form-control' style='height: 30px;' value='"+adi+"'></td>" +
//            "<td><input type='checkbox' "+(es==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(pc==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(pd==1?" checked":"")+"></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td>");
        th.find('input[type="checkbox"]').uniform();
        th.find('.yellow').click(function()
        {
            th.html("" +
                "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                "<td>"+adi+"</td>" +
//                "<td>"+td.eq(2).html()+"</td>" +
//                "<td>"+td.eq(3).html()+"</td>" +
//                "<td>"+td.eq(4).html()+"</td>" +
                "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
        });
    });
    $('#alt_sened_tip_df_table>tbody').on('click','.red',function()
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic .blue').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'alt_sened_tip_id':idsi,'alt_sened_tip_sil':"alt_sened_tip_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#alt_sened_tip_df_table>tbody').children('tr').children('td').length)
                {
                    $('#alt_sened_tip_df_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });
    $('#alt_sened_tip_df_table>tbody').on("click","tr",function()
    {
        $('#alt_sened_tip_df_table>tbody>tr.sechilib').removeClass("sechilib");
        $(this).addClass("sechilib");
        var thisId = $(this).attr("tr_id");
        if(thisId>0)
        {
            $('#sened_mezmunu3_table>tbody>tr[tr_id2="'+thisId+'"]').show();
            $('#sened_mezmunu3_table>tbody>tr:not([tr_id2="'+thisId+'"])').hide();
        }
    });


    /****************************************************************************************/
    //gel
    $('#sened_mezmunu3_table>tbody').on("click","tr",function()
    {
        $('#sened_mezmunu3_table>tbody>tr.sechilib').removeClass("sechilib");
        $(this).addClass("sechilib");
        var thisId = $(this).attr("tr_id");
        if(thisId>0)
        {
            $('#sened_mezmunu4_table>tbody>tr[tr_id2="'+thisId+'"]').show();
            $('#sened_mezmunu4_table>tbody>tr:not([tr_id2="'+thisId+'"])').hide();
        }
    });

    $('#sened_mezmunu3_add').click(function()
    {
        if($("#sened_mezmunu3_table>tbody>tr[time='null']").length)
        {
            $('#sened_mezmunu3_table>tbody>tr').remove();
        }
        var say = $('#sened_mezmunu3_table>tbody>tr').length+1,
            tr_id2 = $('#alt_sened_tip_df_table>tbody>tr.sechilib').attr("tr_id");
        $('#sened_mezmunu3_table>tbody').append("<tr tr_id='0' tr_id2='"+tr_id2+"'>" +
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td><input type='text' class='form-control'></td>" +
            "<td><select class='form-control alt_tip_select' placeholder='Alt tip'><option value='0'>Yoxdur</option><option value='1'>Nazalogia</option><option value='2'>Tibb müəssisəsi</option><option value='3'>Tibb müəssisəsi/Nazalogiya</option></select></td>" +
            "<td style='visibility: hidden;'><button style='width: 55px;' class='btn default btn-xs dovlet_ozel' tm='dovlet' type='button'>Dövlət</button><button class='btn default btn-xs dovlet_ozel' tm='ozel' type='button' style='width: 55px;'>Özəl</button></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td></tr>");

        $('#sened_mezmunu3_table tbody tr:last input[type="checkbox"]').uniform();

        $('#sened_mezmunu3_table>tbody>tr:last .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
        $('#sened_mezmunu3_table>tbody>tr:last select').select2();
    });
    $('#sened_mezmunu3_table>tbody').on("change",".alt_tip_select",function()
    {
        if($(this).val()=="2" || $(this).val()=="3")
        {
            $(this).parent("td").next("td").css('visibility',"visible");
        }
        else
        {
            $(this).parent("td").next("td").css('visibility',"hidden");
        }
    });
    $('#sened_mezmunu3_table>tbody').on("click",".dovlet_ozel",function()
    {
        if($(this).hasClass("default"))
        {
            $(this).switchClass("default","green",200);
        }
        else
        {
            $(this).switchClass("green","default",200);
        }
    });



    $('#sened_mezmunu3_table>tbody').on("click",".blue",function()
    {
        var t = $(this),
            tr = t.parent('td').parent('tr'),
            sayi = tr.children('td').eq(0).text(),
            tr_id = t.parents("tr").eq(0).attr("tr_id"),
            tr_id2 = t.parents("tr").eq(0).attr("tr_id2"),
            name = tr.children('td').eq(1).children('input').val().trim(),
            altTip = tr.children('td').eq(2).children('select').val(),
            altTipAd = altTip==0?"Yoxdur":(altTip==1?"Nazalogia":(altTip==2?"Tibb müəssisəsi":"Tibb müəssisəsi/Nazalogiya")),
            td3 = tr.children('td').eq(3),
            es = $(this).parent('td').parent('tr').children('td').eq(4).find('input').is(":checked")?1:0,
            pc = $(this).parent('td').parent('tr').children('td').eq(5).find('input').is(":checked")?1:0,
            pd = $(this).parent('td').parent('tr').children('td').eq(6).find('input').is(":checked")?1:0,
            de = $(this).parent('td').parent('tr').children('td').eq(7).find('input').is(":checked")?1:0;
        dovlet_ozel = altTip=="2"||altTip=="3"?(td3.children("button[tm='ozel'].green").length==1&&td3.children("button[tm='dovlet'].green").length==1?2:(td3.children("button[tm='ozel'].green").length==1?0:1)):-1;
        dovlet_ozel_ad = altTip=="2"||altTip=="3"?(td3.children("button[tm='ozel'].green").length==1&&td3.children("button[tm='dovlet'].green").length==1?"Dövlət, Özəl":(td3.children("button[tm='ozel'].green").length==1?"Özəl":"Dövlət")):"";
        if(name=="" || isNaN(parseInt(altTip)))
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Məlumatları düzgün daxil edin!</td></tr>');
            setTimeout(function(){$('#sened_mezmunu3_table>tbody>tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php", {'sened_mezmunu3':name,'altTip':altTip,'dovlet_ozel':dovlet_ozel,'sened_mezmunu3_id':tr_id,'ust_id':tr_id2,'pc':pc,'pd':pd,'de':de,'es':es}).done(function(netice)
            {
                if(netice=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adda ölçü vahidi daha öncə əlavə edilib.</td></tr>');
                    setTimeout(function(){$('#sened_mezmunu3_table>tbody>tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id", netice).html("" +
                        "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                        "<td>"+name+"</td>" +
                        "<td>"+altTipAd+"</td>" +
                        "<td>"+dovlet_ozel_ad+"</td>" +
//                        "<td>"+(es==1?"<i class='fa fa-check'></i> <i forma data-original-title='Forma' class='tooltips fa fa-edit' style='cursor:pointer'></i>":"")+"</td>" +
//                        "<td>"+(pc==1?"<i class='fa fa-check'></i> ":"")+"</td>" +
//                        "<td>"+(pd==1?"<i class='fa fa-check'></i>":"")+"</td>" +
//                        "<td>"+(de==1?"<i class='fa fa-check'></i>":"")+"</td>" +
                        "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td></tr>");
                }
            });
        }
    });

    $('#sened_mezmunu3_table>tbody').on("click",".purple",function()
    {
        var th = $(this).parent('td').parent('tr'),
            td = th.children('td'),
            es = td.eq(4).find("i").length>0?1:0,
            pc = td.eq(5).find("i").length>0?1:0,
            pd = td.eq(6).find("i").length>0?1:0,
            de = td.eq(7).find("i").length>0?1:0,
            adi = td.eq(1).text().trim(),
            altTipAd = td.eq(2).text(),
            dovletOzelAd = td.eq(3).text(),
            altTip = altTipAd=="Yoxdur"?"0":(altTipAd=="Nazalogia"?"1":(altTipAd=="Tibb müəssisələri"?"2":"3")),
            btnDovletCls = dovletOzelAd=="Dövlət"?"green":(dovletOzelAd=="Özəl"?"default":"green"),
            btnOzelCls = dovletOzelAd=="Dövlət"?"default":(dovletOzelAd=="Özəl"?"green":"green"),
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text();

        $(this).parent('td').parent('tr').html("" +
            "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
            "<td><input type='text' class='form-control' value='"+adi+"'></td>" +
            "<td><select class='form-control alt_tip_select' placeholder='Alt tip'><option value='0'>Yoxdur</option><option value='1'>Nazalogia</option><option value='2'>Tibb müəssisəsi</option><option value='3'>Tibb müəssisəsi/Nazalogiya</option></select></td>" +
            "<td style='visibility: hidden;'><button style='width: 55px;' class='btn "+btnDovletCls+" btn-xs dovlet_ozel' tm='dovlet' type='button'>Dövlət</button><button class='btn "+btnOzelCls+" btn-xs dovlet_ozel' tm='ozel' type='button' style='width: 55px;'>Özəl</button></td>" +
//            "<td><input type='checkbox' "+(es==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(es==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(pd==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(de==1?" checked":"")+"></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td>");
        th.find('input[type="checkbox"]').uniform();
        th.find("select").val(altTip).select2().trigger("change");
        th.find('.yellow').click(function()
        {
            th.html("" +
                "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                "<td>"+adi+"</td>" +
                "<td>"+altTipAd+"</td>" +
                "<td>"+dovletOzelAd+"</td>" +
//                "<td>"+td.eq(4).html()+"</td>" +
//                "<td>"+td.eq(5).html()+"</td>" +
//                "<td>"+td.eq(6).html()+"</td>" +
//                "<td>"+td.eq(7).html()+"</td>" +
                "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td></tr>");
        });
    });
    $('#sened_mezmunu3_table>tbody').on("click",".red",function()
    {
        var idsi = $(this).parent('td').parent('tr').attr("tr_id"),
            t = $(this);
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic .blue').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'sened_mezmunu3_id':idsi,'sened_mezmunu3_sil':"sened_mezmunu3_sil"}).done(function(netice)
            {
                t.parent('td').parent('tr').remove();
                $('#basic .default').click();
                if($('#sened_mezmunu3_table>tbody').children('tr').children('td').length==0)
                {
                    $('#sened_mezmunu3_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });
    $('#sened_tip_df_table tbody tr').eq(0).trigger("click");

    //// alt movzular son



    //// qisa mezmunlar
    $('#sened_mezmunu4_add').click(function()
    {
        if($("#sened_mezmunu4_table>tbody tr[time='null']").length)
        {
            $('#sened_mezmunu4_table>tbody tr').remove();
        }
        var say = $('#sened_mezmunu4_table>tbody tr').length+1,
            tip = $('#sened_mezmunu3_table tbody tr.sechilib').attr("tr_id");
        $('#sened_mezmunu4_table>tbody').append("<tr tr_id='0' tip='"+tip+"'>" +
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td><input type='text' class='form-control' style='height: 30px;'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
//            "<td><input type='checkbox'></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td></tr>");
        $('#sened_mezmunu4_table>tbody tr:last .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
        $('#sened_mezmunu4_table tbody tr:last input[type="checkbox"]').uniform();
    });

    $('#sened_mezmunu4_table>tbody').on('click','.blue',function()
    {
        var sened_tip = $(this).parent('td').parent('tr').children('td').eq(1).children('input').val(),
            t = $(this),
            tr = t.parent('td').parent('tr'),
            tr_id = tr.attr("tr_id"),
            tip = tr.attr("tip"),
            sayi = tr.children('td').eq(0).text(),
            es = $(this).parent('td').parent('tr').children('td').eq(2).find('input').is(":checked")?1:0,
            pc = $(this).parent('td').parent('tr').children('td').eq(3).find('input').is(":checked")?1:0,
            pd = $(this).parent('td').parent('tr').children('td').eq(4).find('input').is(":checked")?1:0;
        if(sened_tip=="")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#sened_mezmunu4_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php", {'sened_mezmunu4_table':sened_tip,'sened_mezmunu4_table_id':tr_id,'tip':tip,'es':es,'pc':pc,'pd':pd,'nov_df':1},function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla vəzifə mövcuddur.</td></tr>');
                    setTimeout(function(){$('#sened_mezmunu4_table>tbody tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id",result).html("" +
                        "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                        "<td>"+sened_tip+"</td>" +
//                        "<td>"+(es==1?"<i class='fa fa-check'></i>":"")+"</td>" +
//                        "<td>"+(pc==1?"<i class='fa fa-check'></i>":"")+"</td>" +
//                        "<td>"+(pd==1?"<i class='fa fa-check'></i>":"")+"</td>" +
                        "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
                }
            });
        }
    });

    $('#sened_mezmunu4_table>tbody').on('click','.purple',function()
    {
        var td = $(this).closest('tr').children('td'),
            adi = td.eq(1).text(),
            es = td.eq(2).find("i").length>0?1:0,
            pc = td.eq(3).find("i").length>0?1:0,
            pd = td.eq(4).find("i").length>0?1:0,
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text(),
            th = $(this).parent('td').parent('tr');

        $(this).parent('td').parent('tr').html("" +
            "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
            "<td><input type='text' class='form-control' style='height: 30px;' value='"+adi+"'></td>" +
//            "<td><input type='checkbox' "+(es==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(pc==1?" checked":"")+"></td>" +
//            "<td><input type='checkbox' "+(pd==1?" checked":"")+"></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td>");
        th.find('input[type="checkbox"]').uniform();
        th.find('.yellow').click(function()
        {
            th.html("" +
                "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                "<td>"+adi+"</td>" +
//                "<td>"+td.eq(2).html()+"</td>" +
//                "<td>"+td.eq(3).html()+"</td>" +
//                "<td>"+td.eq(4).html()+"</td>" +
                "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
        });
    });

    $('#sened_mezmunu4_table>tbody').on('click','.red',function()
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic .blue').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'sened_mezmunu4_table_id':idsi,'sened_mezmunu4_table_sil':"sened_mezmunu4_table_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#sened_mezmunu4_table>tbody').children('tr').children('td').length)
                {
                    $('#sened_mezmunu4_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });
    /// qisa mezmunlar son


</script>