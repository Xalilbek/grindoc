<div class="tab-content" style="overflow: auto;">
<div style="width: 2600px;" class="tab-pane active" id="tab_1">
    <div class="row">
        <div class="col-md-4"  style="width: 630px;">
            <span style="position: fixed; left: 260px; cursor: pointer; height: 50px; padding-top: 20px;"><i class="fa fa-arrow-left leftArrow"></i></span>
            <span style="position: fixed; right: 55px; cursor: pointer; height: 50px; padding-top: 20px;"><i class="fa fa-arrow-right rightArrow"></i></span>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="sened_tip_table">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                        <?php
                            $query = DB::fetch("SELECT id, value FROM tb_options WHERE option_name='mektubun_tipi_first_table'");
                            print '<td style="background-color: #f1f4f7;" tr_title_id="'.$query['id'].'" >'.htmlspecialchars($query['value']).'<i class="fa fa-edit mektubun_tipi_first_table" style="float: right; margin-top: 4px; cursor: pointer;"></i></td>';
                        ?>
                        <th style="width: 35px;">Hüquqi</th>
                        <th style="width: 35px;">Fiziki</th>
                        <th style="width: 66px; text-align:center;"><a href="javascript:;" class="btn default btn-xs green" id="sened_tip_add"><i class="icon-plus"></i> Yeni</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $filtr = 1;
                    $filtrTenant = (new User())->getQueryTenantFilter('selectCheckMsk');
                    $query = DB::fetchAll("SELECT * FROM tb_mektubun_tipleri WHERE silinib='0' AND (parent_id=0 OR parent_id IS NULL) AND (select_type ='tipi') AND $filtrTenant");
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
                            print '<td align="center">'.($mass['huquqi']=="1"?"<i class='fa fa-check'></i>":"").'</td>';
                            print '<td align="center">'.($mass['fiziki']==1?'<i class="fa fa-check"></i>':'').'</td>';

                            print '<td style="text-align:center; min-width: 80px;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i></a><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i></a></td>';
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
                <table class="table table-striped table-bordered table-advance table-hover" id="alt_sened_tip_table">
                    <thead>
                    <tr>
                        <th style="width: 20px;">№</th>
                        <?php
                            $query = DB::fetch("SELECT id, value FROM tb_options WHERE option_name='mektubun_tipi_second_table'");
                            print '<td style="background-color: #f1f4f7;" tr_title_id="'.$query['id'].'" >'.htmlspecialchars($query['value']).'<i class="fa fa-edit mektubun_tipi_second_table" style="float: right; margin-top: 4px; cursor: pointer;"></i></td>';
                        ?>
                        <th style="width: 66px; text-align:center;"><a href="javascript:;" class="btn default btn-xs green" id="alt_sened_tip_add"><i class="icon-plus"></i> Yeni</a></th>
                    </tr>
                    </thead>
                    <tbody style="display: none;">
                    <?php

                    $query = DB::fetchAll("SELECT * FROM tb_mektubun_tipleri WHERE silinib='0' AND parent_id>0 AND select_type='alt_tipi' ");
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
                            print '<td style="text-align:center;min-width: 80px;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i></a><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i></a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-5" style="width: 630px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="sened_mezmunu3_table">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                         <?php
                            $query = DB::fetch("SELECT id, value FROM tb_options WHERE option_name='mektubun_tipi_third_table'");
                            print '<td style="background-color: #f1f4f7;" tr_title_id="'.$query['id'].'" >'.htmlspecialchars($query['value']).'<i class="fa fa-edit mektubun_tipi_third_table" style="float: right; margin-top: 4px; cursor: pointer;"></i></td>';
                         ?>
                        <th style="width: 140px;">Alt tip</th>
                        <th style="width: 60px;"></th>
                        <th style="text-align:right; width: 65px;"><a href="javascript:;" class="btn default btn-xs green" id="sened_mezmunu3_add"><i class="icon-plus"></i> Yeni</a></th>
                    </tr>
                    </thead>
                    <tbody style="display: none;">
                    <?php
                    $movzular = DB::fetchAll("SELECT * FROM tb_mektubun_tipleri WHERE silinib='0' AND parent_id>0 AND select_type='last_two'");

                    if(count($movzular)==0)
                    {
                        print "<tr time='null'><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else {
                        $sira = 0;
                        while ($movzuInf = array_shift($movzular)) {
                            $sira++;
                            print '<tr tr_id="' . (int)$movzuInf['id'] . '" tr_id2="' . (int)$movzuInf['parent_id'] . '" style="display: none;">
												<td>' . $sira . '</td>
												<td>' . htmlspecialchars($movzuInf['ad']) . '</td>
												<td>' . ($movzuInf['tibb_muessisesi_tip'] == 1 ? "Nazalogia" : ($movzuInf['tibb_muessisesi_tip'] == 2 ? "Tibb müəssisələri" : ($movzuInf['tibb_muessisesi_tip'] == 3 ? "Tibb müəssisəsi/Nazalogiya" : "Yoxdur"))) . '</td>
												<td style="visibility: ' . ($movzuInf['nozologiya'] == 2 || $movzuInf['nozologiya'] == 3 ? "visible" : "hidden") . ';">' . ($movzuInf['nozologiya'] == 2 || $movzuInf['nozologiya'] == 3 ? ($movzuInf['tibb_muessisesi_tip'] == 1 ? "Dövlət" : ($movzuInf['tibb_muessisesi_tip'] == 0 ? "Özəl" : "Dövlət, Özəl")) : "") . '</td>
												<td style="text-align:center; width: 80px;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i></a><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i></a></td>
												</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-3" style="width: 630px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="sened_mezmunu4_table">
                    <thead>
                    <tr>
                        <th style="width: 20px;">№</th>
                        <?php
                            $query = DB::fetch("SELECT id, value FROM tb_options WHERE option_name='mektubun_tipi_last_table'");
                            print '<td style="background-color: #f1f4f7;" tr_title_id="'.$query['id'].'" >'.htmlspecialchars($query['value']).'<i class="fa fa-edit mektubun_tipi_last_table" style="float: right; margin-top: 4px; cursor: pointer;"></i></td>';
                        ?>
                        <th style="width: 65px; text-align:right;"><button type="button" id="sened_mezmunu4_add" class="btn default btn-xs green"><i class="icon-plus"></i> Yeni</a></th>
                    </tr>
                    </thead>
                    <tbody style="display: none;">
                    <?php
                    $movzular = DB::fetchAll("SELECT * FROM tb_mektubun_tipleri WHERE silinib='0' AND parent_id>0 AND select_type='last' ");

                    if(count($movzular)==0)
                    {
                        print "<tr time='null'><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else {
                        $sira = 0;
                        while ($movzuInf = array_shift($movzular)) {
                            $sira++;
                            print '<tr tip="' . (int)$movzuInf['parent_id'] . '"tr_id="' . (int)$movzuInf['id'] . '" tr_id2="' . (int)$movzuInf['parent_id'] . '" style="display: none;">
												<td>' . $sira . '</td>
												<td>' . htmlspecialchars($movzuInf['ad']) . '</td>
												<td style="width: 80px; text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i></a><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i></a></td>
												</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
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
    $(function () {

        $(".leftArrow").click(function ()
        {
            var leftPos = $('.tab-content').width();
            $(".tab-content").animate({scrollLeft: -leftPos}, 800);
        });

        $(".rightArrow").click(function ()
        {
            var leftPos = $('.tab-content').width();
            $(".tab-content").animate({scrollLeft: leftPos}, 800);
        });

        ///////SENED TIPLERI////////
        $('#sened_tip_table>tbody').on("click","tr",function()
        {
            $('#sened_tip_table tbody tr.sechilib').css("background-color", "");
            $('#sened_tip_table tbody tr.sechilib').removeClass("sechilib");
            $(this).addClass("sechilib");
            $('#sened_tip_table tbody tr.sechilib').css("background-color", "#ffffe6");
            $('#alt_sened_tip_table tbody').show();
            $('#alt_sened_tip_table').css( 'pointerEvents', 'all' );
            $("#alt_sened_tip_table tbody tr").hide();

            // hide other tables
            $('#sened_mezmunu3_table tbody').hide();
            $('#sened_mezmunu4_table tbody').hide();

            $("#alt_sened_tip_table tbody tr[tip='"+$(this).attr("tr_id")+"']").show();
            $("#alt_sened_tip_table tbody tr[tip='"+$(this).attr("tr_id")+"']:eq(0)").click();
            SortTable();


        });
        $('#sened_tip_add').click(function()
        {
            if($("#sened_tip_table tbody tr[time='null']").length)
            {
                $('#sened_tip_table tbody tr').remove();
            }
            var say = $('#sened_tip_table tbody tr').length+1;
            $('#sened_tip_table tbody').append("<tr tr_id='0'><td style='width:20px;text-align:center;'>"+say+"</td><td><input type='text' class='form-control' style='height: 30px;'></td><td align='center' style='padding: 0;'><input type='checkbox'></td><td align='center' style='padding: 0;'><input type='checkbox'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td></tr>");
            $('#sened_tip_table tbody tr:last input[type="checkbox"]').uniform();
            $('#sened_tip_table tbody tr:last .yellow').click(function()
            {
                $(this).parent('td').parent('tr').remove();
            });
        });
        $('#sened_tip_table>tbody').on('click','.blue',function()
        {
            var mektub_tip = $(this).parent('td').parent('tr').children('td').eq(1).children('input').val(),
                huquqi = $(this).parent('td').parent('tr').children('td').eq(2).find('input').is(":checked")?1:0,
                fiziki = $(this).parent('td').parent('tr').children('td').eq(3).find('input').is(":checked")?1:0,
                t = $(this),
                tr = t.parent('td').parent('tr'),
                tr_id = tr.attr("tr_id"),
                sayi = tr.children('td').eq(0).text();
            if(mektub_tip=="")
            {
                tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
                setTimeout(function(){$('#sened_tip_table tbody tr[error]').remove();}, 3000);
            }
            else
            {
                $.post(proBundle + "includes/msk/sened_novleri.php", {'huquqi':huquqi, 'fiziki':fiziki, 'select_type':'tipi', 'mektub_tip':mektub_tip, 'mektub_tip_id':tr_id},function(result)
                {
                    if(result=="error")
                    {
                        tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla vəzifə mövcuddur.</td></tr>');
                        setTimeout(function(){$('#sened_tip_table tbody tr[error]').remove();}, 3000);
                    }
                    else
                    {
                        tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+mektub_tip+"</td><td align='center'>"+(huquqi=="1"?"<i class='fa fa-check'></i>":"")+"</td><td align='center'>"+(fiziki=="1"?"<i class='fa fa-check'></i>":"")+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
                    }
                });
            }
        });
        $('#sened_tip_table>tbody').on('click','.purple',function()
        {
            var td = $(this).closest('tr').children('td'),
                adi = td.eq(1).text(),
                cavab = (td.eq(2).children('i').length>0),
                nov_dh = (td.eq(3).children('i').length>0),
                sayi = $(this).parent('td').parent('tr').children('td').eq(0).text(),
                th = $(this).parent('td').parent('tr');
            $(this).parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td><input type='text' class='form-control input-small' style='height: 30px;' value='"+adi+"'></td><td align='center' style='padding: 0;'><input type='checkbox'"+(cavab?" checked":"")+"></td><td align='center' style='padding: 0;'><input type='checkbox'"+(nov_dh?" checked":"")+"></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td>");
            th.find('input[type="checkbox"]').uniform();
            th.find('.yellow').click(function()
            {
                th.html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+adi+"</td><td align='center'>"+(cavab?'<i class="fa fa-check"></i>':'')+"</td><td align='center'>"+(nov_dh?'<i class="fa fa-check"></i>':'')+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
            });
        });
        $('#sened_tip_table>tbody').on('click','.red',function()
        {
            var t = $(this),
                idsi = t.parent('td').parent('tr').attr("tr_id");
            $('#basic').attr('idsi', idsi);
            console.log('xellll');
            $('#msk_delete').click();
            $('#basic .blue').unbind('click').click(function()
            {
                var idsi = $('#basic').attr('idsi');
                $.post(proBundle + "includes/msk/sened_novleri.php", {'mektub_tip_id':idsi,'sened_tip_sil':"sened_tip_sil"},function(result)
                {
                    $('#basic .default').click();
                    t.parent('td').parent('tr').remove();
                    var count = 0;
                    $("#sened_tip_table>tbody tr").each(function () {
                        $(this).find("td").eq(0).text(++count);
                    });
                    if(!$('#sened_tip_table tbody').children('tr').children('td').length)
                    {
                        $('#sened_tip_table tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                    }
                });
            });
        });


        $('#alt_sened_tip_add').click(function()
        {
            if($("#alt_sened_tip_table>tbody tr[time='null']").length)
            {
                $('#alt_sened_tip_table>tbody tr').remove();
            }
            var say = $('#alt_sened_tip_table>tbody tr').length+1,
                tip = $('#sened_tip_table tbody tr.sechilib').attr("tr_id");
            $('#alt_sened_tip_table>tbody').append("<tr tr_id='0' tip='"+tip+"'><td style='width:20px;text-align:center;'>"+say+"</td><td><input type='text' class='form-control input-small' style='height: 30px;'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td></tr>");
            $('#alt_sened_tip_table>tbody tr:last .yellow').click(function()
            {
                $(this).parent('td').parent('tr').remove();
            });
            SortTable();
        });
        $('#alt_sened_tip_table>tbody').on('click','.blue',function()
        {
            var mektub_tip = $(this).parent('td').parent('tr').children('td').eq(1).children('input').val(),
                t = $(this),
                tr = t.parent('td').parent('tr'),
                tr_id = tr.attr("tr_id"),
                tip = tr.attr("tip"),
                sayi = tr.children('td').eq(0).text();
            if(mektub_tip=="")
            {
                tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
                setTimeout(function(){$('#alt_sened_tip_table>tbody tr[error]').remove();}, 3000);
            }
            else
            {
                $.post(proBundle + "includes/msk/sened_novleri.php", {'alt_mektub_tip':mektub_tip,'select_type':'alt_tipi','alt_mektub_tip_id':tr_id,'tip':tip},function(result)
                {
                    if(result=="error")
                    {
                        tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla vəzifə mövcuddur.</td></tr>');
                        setTimeout(function(){$('#alt_sened_tip_table>tbody tr[error]').remove();}, 3000);
                    }
                    else
                    {
                        tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+mektub_tip+"</td><td style='text-align:center; min-width: 80px;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
                        SortTable();
                    }
                });
            }
        });
        $('#alt_sened_tip_table>tbody').on('click','.purple',function()
        {
            var adi = $(this).parent('td').prev().text(),
                sayi = $(this).parent('td').parent('tr').children('td').eq(0).text(),
                th = $(this).parent('td').parent('tr');
            $(this).parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td><input type='text' class='form-control input-small' style='height: 30px;' value='"+adi+"'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td>");
            th.find('.yellow').click(function()
            {
                th.html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+adi+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
            });
        });
        $('#alt_sened_tip_table>tbody').on('click','.red',function()
        {
            var t = $(this),
                idsi = t.parent('td').parent('tr').attr("tr_id");
            $('#basic').attr('idsi', idsi);
            $('#msk_delete').click();
            $('#basic .blue').unbind('click').click(function()
            {
                var idsi = $('#basic').attr('idsi');
                $.post(proBundle + "includes/msk/sened_novleri.php", {'mektub_tip_id':idsi,'sened_tip_sil':"sened_tip_sil"},function(result)
                {
                    $('#basic .default').click();
                    t.parent('td').parent('tr').remove();
                    SortTable();
                    if(!$('#alt_sened_tip_table>tbody').children('tr').children('td').length)
                    {
                        $('#alt_sened_tip_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                    }
                });
            });
        });
        $('#alt_sened_tip_table>tbody').on("click","tr",function()
        {
            console.log('clicked but');
            $('#alt_sened_tip_table tbody tr.sechilib').css("background-color", "");
            $('#alt_sened_tip_table>tbody>tr.sechilib').removeClass("sechilib");
            $(this).addClass("sechilib");
            $('#alt_sened_tip_table tbody tr.sechilib').css("background-color", "#ffffe6");
            // var thisId = $(this).attr("tr_id");
            // if(thisId>0)
            // {
            //     $('#sened_mezmunu3_table>tbody>tr[tr_id2="'+thisId+'"]').show();
            //     $('#sened_mezmunu3_table>tbody>tr:not([tr_id2="'+thisId+'"])').hide();
            // }

            $('#sened_mezmunu3_table tbody').show();
            $('#sened_mezmunu3_table').css( 'pointerEvents', 'all' );
            $("#sened_mezmunu3_table tbody tr").hide();
            $("#sened_mezmunu3_table tbody tr[tr_id2='"+$(this).attr("tr_id")+"']").show();
            $("#sened_mezmunu3_table tbody tr[tr_id2='"+$(this).attr("tr_id")+"']:eq(0)").click();
            SortTable();
        });
    });

    function SortTable() {
        var count = 0;
        $("#alt_sened_tip_table>tbody tr:visible").each(function () {
           $(this).find("td").eq(0).text(++count);
        });
    }



    /////// 3 TABLE

    $('#sened_mezmunu3_table>tbody').on("click","tr",function()
    {
        $('#sened_mezmunu3_table tbody tr.sechilib').css("background-color", "");
        $('#sened_mezmunu3_table>tbody>tr.sechilib').removeClass("sechilib");
        $(this).addClass("sechilib");
        $('#sened_mezmunu3_table tbody tr.sechilib').css("background-color", "#ffffe6");
        var thisId = $(this).attr("tr_id");
        if(thisId>0)
        {
            $('#sened_mezmunu4_table>tbody>tr[tr_id2="'+thisId+'"]').show();
            $('#sened_mezmunu4_table>tbody>tr:not([tr_id2="'+thisId+'"])').hide();
        }

        $('#sened_mezmunu4_table tbody').show();
        $('#sened_mezmunu4_table').css( 'pointerEvents', 'all' );
        $("#sened_mezmunu4_table tbody tr").hide();
        $("#sened_mezmunu4_table tbody tr[tr_id2='"+$(this).attr("tr_id")+"']").show();
        $("#sened_mezmunu4_table tbody tr[tr_id2='"+$(this).attr("tr_id")+"']:eq(0)").click();
        SortTable();
    });

    $('#sened_mezmunu3_add').click(function()
    {
        if($("#sened_mezmunu3_table>tbody>tr[time='null']").length)
        {
            $('#sened_mezmunu3_table>tbody>tr').remove();
        }
        var say = $('#sened_mezmunu3_table>tbody>tr').length+1,
            tr_id2 = $('#alt_sened_tip_table>tbody>tr.sechilib').attr("tr_id");
        $('#sened_mezmunu3_table>tbody').append("<tr tr_id='0' tr_id2='"+tr_id2+"'><td style='width:20px;text-align:center;'>"+say+"</td><td><input type='text' class='form-control'></td><td><select class='form-control alt_tip_select' placeholder='Alt tip'><option value='0'>Yoxdur</option><option value='1'>Nazalogia</option><option value='2'>Tibb müəssisəsi</option><option value='3'>Tibb müəssisəsi/Nazalogiya</option></select></td><td style='visibility: hidden;'><button style='width: 55px;' class='btn default btn-xs dovlet_ozel' tm='dovlet' type='button'>Dövlət</button><button class='btn default btn-xs dovlet_ozel' tm='ozel' type='button' style='width: 55px;'>Özəl</button></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td></tr>");

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
            table_id = $('#sened_mezmunu3_table>thead>tr>td').attr('tr_title_id');
        dovlet_ozel = altTip=="2"||altTip=="3"?(td3.children("button[tm='ozel'].green").length==1&&td3.children("button[tm='dovlet'].green").length==1?2:(td3.children("button[tm='ozel'].green").length==1?0:1)):-1;
        dovlet_ozel_ad = altTip=="2"||altTip=="3"?(td3.children("button[tm='ozel'].green").length==1&&td3.children("button[tm='dovlet'].green").length==1?"Dövlət, Özəl":(td3.children("button[tm='ozel'].green").length==1?"Özəl":"Dövlət")):"";

        if(name=="" || isNaN(parseInt(altTip)))
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Məlumatları düzgün daxil edin!</td></tr>');
            setTimeout(function(){$('#sened_mezmunu3_table>tbody>tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/mektubun_tipleri.php", {'sened_mezmunu3':name, 'table_id': table_id, 'altTip':altTip, 'dovlet_ozel':dovlet_ozel, 'sened_mezmunu3_id':tr_id, 'ust_id':tr_id2}).done(function(netice)
            {
                if(netice=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adda ölçü vahidi daha öncə əlavə edilib.</td></tr>');
                    setTimeout(function(){$('#sened_mezmunu3_table>tbody>tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id", netice).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+name+"</td><td>"+altTipAd+"</td><td>"+dovlet_ozel_ad+"</td><td style='width: 80px; text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td></tr>");
                }
            });
        }
    });

    $('#sened_mezmunu3_table>tbody').on("click",".purple",function()
    {
        var th = $(this).parent('td').parent('tr'),
            td = th.children('td'),
            adi = td.eq(1).text().trim(),
            altTipAd = td.eq(2).text(),
            dovletOzelAd = td.eq(3).text(),
            altTip = altTipAd=="Yoxdur"?"0":(altTipAd=="Nazalogia"?"1":(altTipAd=="Tibb müəssisələri"?"2":"3")),
            btnDovletCls = dovletOzelAd=="Dövlət"?"green":(dovletOzelAd=="Özəl"?"default":"green"),
            btnOzelCls = dovletOzelAd=="Dövlət"?"default":(dovletOzelAd=="Özəl"?"green":"green"),
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text();

        $(this).parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td><input type='text' class='form-control' value='"+adi+"'></td><td><select class='form-control alt_tip_select' placeholder='Alt tip'><option value='0'>Yoxdur</option><option value='1'>Nazalogia</option><option value='2'>Tibb müəssisəsi</option><option value='3'>Tibb müəssisəsi/Nazalogiya</option></select></td><td style='visibility: hidden;'><button style='width: 55px;' class='btn "+btnDovletCls+" btn-xs dovlet_ozel' tm='dovlet' type='button'>Dövlət</button><button class='btn "+btnOzelCls+" btn-xs dovlet_ozel' tm='ozel' type='button' style='width: 55px;'>Özəl</button></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td>");
        th.find("select").val(altTip).select2().trigger("change");
        th.find('.yellow').click(function()
        {
            th.html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+adi+"</td><td>"+altTipAd+"</td><td>"+dovletOzelAd+"</td><td>"+td.eq(4).html()+"</td></tr>");
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
            $.post(proBundle + "includes/msk/mektubun_tipleri.php", {'sened_mezmunu3_id':idsi,'sened_mezmunu3_sil':"sened_mezmunu3_sil"}).done(function(netice)
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

    $('#sened_mezmunu3_table').on('click', '.mektubun_tipi_third_table', function () {
        var text = $("#sened_mezmunu3_table thead>tr").children().eq(1).text(),
            tr   = $("#sened_mezmunu3_table thead>tr").children().eq(1);

        tr.html("<td style='display: flex'><input type='text' class='form-control edit_title' style='height: 28px;' value='"+text+"'>" +
                                                                    "<i class='fa fa-save edit_save' style='margin-left: 8px; margin-top: 7px; cursor: pointer;'></i></td>");

        $('.edit_save').click(function () {
            var idsi = tr.attr('tr_title_id'),
                text = tr.find('.edit_title').val();
            $.post(proBundle + "includes/msk/sened_novleri.php", {'id':idsi, 'text': text, 'tip':'title_edit'}).done(function(netice)
            {
                tr.html(text+"<i class='fa fa-edit mektubun_tipi_third_table' style='float: right; margin-top: 4px; cursor: pointer;'></i>");
            });
        });

    });


    // 4 TABLE

    $('#sened_mezmunu4_add').click(function()
    {
        if($("#sened_mezmunu4_table>tbody tr[time='null']").length)
        {
            $('#sened_mezmunu4_table>tbody tr').remove();
        }
        var say = $('#sened_mezmunu4_table>tbody tr').length+1,
            tip = $('#sened_mezmunu3_table tbody tr.sechilib').attr("tr_id");
        $('#sened_mezmunu4_table>tbody').append("<tr tr_id='0' tip='"+tip+"'><td style='width:20px;text-align:center;'>"+say+"</td><td><input type='text' class='form-control' style='height: 30px;'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td></tr>");
        $('#sened_mezmunu4_table>tbody tr:last .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
    });

    $('#sened_mezmunu4_table>tbody').on('click','.blue',function()
    {
        var sened_tip = $(this).parent('td').parent('tr').children('td').eq(1).children('input').val(),
            t = $(this),
            tr = t.parent('td').parent('tr'),
            tr_id = tr.attr("tr_id"),
            tip = tr.attr("tip"),
            sayi = tr.children('td').eq(0).text(),
            table_id = $('#sened_mezmunu4_table>thead>tr>td').attr('tr_title_id');
        if(sened_tip=="")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#sened_mezmunu4_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/mektubun_tipleri.php", {'sened_mezmunu4_table':sened_tip, 'table_id': table_id, 'sened_mezmunu4_table_id':tr_id,'tip':tip},function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla vəzifə mövcuddur.</td></tr>');
                    setTimeout(function(){$('#sened_mezmunu4_table>tbody tr[error]').remove();}, 3000);
                }
                else
                {
                    tr.attr("tr_id",result).html("<td style='text-align:center;'>"+sayi+"</td><td>"+sened_tip+"</td><td style='width:80px; text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i></a><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i></a></td>");
                }
            });
        }
    });

    $('#sened_mezmunu4_table>tbody').on('click','.purple',function()
    {
        var td = $(this).closest('tr').children('td'),
            adi = td.eq(1).text(),
            sayi = $(this).parent('td').parent('tr').children('td').eq(0).text(),
            th = $(this).parent('td').parent('tr');

        $(this).parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td><input type='text' class='form-control' style='height: 30px;' value='"+adi+"'></td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i></a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i></a></td>");
        th.find('.yellow').click(function()
        {
            th.html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+adi+"</td><td>"+td.eq(2).html()+"</td>");
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
            $.post(proBundle + "includes/msk/mektubun_tipleri.php", {'sened_mezmunu4_table_id':idsi,'sened_mezmunu4_table_sil':"sened_mezmunu4_table_sil"},function(result)
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

    $('#sened_mezmunu4_table>tbody').on("click","tr",function()
    {
        $('#sened_mezmunu4_table tbody tr.sechilib').css("background-color", "");
        $('#sened_mezmunu4_table>tbody>tr.sechilib').removeClass("sechilib");
        $(this).addClass("sechilib");
        $('#sened_mezmunu4_table tbody tr.sechilib').css("background-color", "#ffffe6");
    });

    $('#sened_mezmunu4_table').on('click', '.mektubun_tipi_last_table', function () {
        var text = $("#sened_mezmunu4_table thead>tr").children().eq(1).text(),
            tr   = $("#sened_mezmunu4_table thead>tr").children().eq(1);

        tr.html("<td style='display: flex'><input type='text' class='form-control edit_title' style='height: 28px;' value='"+text+"'>" +
            "<i class='fa fa-save edit_save' style='margin-left: 8px; margin-top: 7px; cursor: pointer;'></i></td>");

        $('.edit_save').click(function () {
            var idsi = tr.attr('tr_title_id'),
                text = tr.find('.edit_title').val();
            $.post(proBundle + "includes/msk/sened_novleri.php", {'id':idsi, 'text': text, 'tip':'title_edit'}).done(function(netice)
            {
                tr.html(text+"<i class='fa fa-edit mektubun_tipi_last_table' style='float: right; margin-top: 4px; cursor: pointer;'></i>");
            });
        });
    });


    //
    $('#alt_sened_tip_table').on('click', '.mektubun_tipi_second_table', function () {
        var text = $("#alt_sened_tip_table thead>tr").children().eq(1).text(),
            tr   = $("#alt_sened_tip_table thead>tr").children().eq(1);

        tr.html("<td style='display: flex'><input type='text' class='form-control edit_title' style='height: 28px;' value='"+text+"'>" +
            "<i class='fa fa-save edit_save' style='margin-left: 8px; margin-top: 7px; cursor: pointer;'></i></td>");

        $('.edit_save').click(function () {
            var idsi = tr.attr('tr_title_id'),
                text = tr.find('.edit_title').val();
            $.post(proBundle + "includes/msk/sened_novleri.php", {'id':idsi, 'text': text, 'tip':'title_edit'}).done(function(netice)
            {
                tr.html(text+"<i class='fa fa-edit mektubun_tipi_second_table' style='float: right; margin-top: 4px; cursor: pointer;'></i>");
            });
        });
    });

    //

    $('#sened_tip_table').on('click', '.mektubun_tipi_first_table', function () {
        var text = $("#sened_tip_table thead>tr").children().eq(1).text(),
            tr   = $("#sened_tip_table thead>tr").children().eq(1);

        tr.html("<td style='display: flex'><input type='text' class='form-control edit_title' style='height: 28px;' value='"+text+"'>" +
            "<i class='fa fa-save edit_save' style='margin-left: 8px; margin-top: 7px; cursor: pointer;'></i></td>");

        $('.edit_save').click(function () {
            var idsi = tr.attr('tr_title_id'),
                text = tr.find('.edit_title').val();
            $.post(proBundle + "includes/msk/sened_novleri.php", {'id':idsi, 'text': text, 'tip':'title_edit'}).done(function(netice)
            {
                tr.html(text+"<i class='fa fa-edit mektubun_tipi_first_table' style='float: right; margin-top: 4px; cursor: pointer;'></i>");
            });
        });
    });



</script>