<div class="tab-pane" id="tab_26">
    <div class="row">
        <div class="col-md-8">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="yoxlayan_shexsler">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                        <th>Əməkdaş</th>
                        <th></th>
                        <th style="text-align:right;"><a href="javascript:;" class="btn default btn-xs green" id="yoxlayan_shexsler_add"><i class="icon-plus"></i> Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $sql = "SELECT tb1.*, tb2.user_ad_qisa
                        FROM tb_prodoc_{$GLOBALS['prefix']} tb1
                        LEFT JOIN v_user_adlar tb2 ON tb2.USERID = tb1.user_id";

                    $query = DB::fetchAll($sql);
                    if(count($query)==0)
                    {
                        print "<tr bosh><td colspan='100%'>MSK Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query))
                        {
                            $i++;
                            print '<tr tr_id="'.(int)$mass['id'].'" user_id="'.(int)$mass['user_id'].'">';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['user_ad_qisa']).'</td>';
                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                            print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td>';
                            print '</tr>';
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

    function emekdashlar(e)
    {
        e.select2({
            allowClear: false,
            multiple:false,
            ajax: {
                url: proBundle + "includes/plugins/axtarish.php",
                type: 'POST',
                dataType: 'json',
                data: function (soz)
                {
                    return {
                        'a': soz,
                        'ne':'butun_emekdashlar',
                    };
                },
                results: function(data,a)
                {
                    return data
                },
                cache: true
            }
        });
    }

    $('#yoxlayan_shexsler_add').click(function()
    {
        if($("#yoxlayan_shexsler>tbody tr[time='null']").length)
        {
            $('#yoxlayan_shexsler>tbody tr').remove();
        }

        var say = $('#yoxlayan_shexsler>tbody tr').length+1;

        $('#yoxlayan_shexsler>tbody').append("<tr st='0' tr_id='0'> " +
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td> <input placeholder='Əməkdaş' type='text' class='form-control emekdashlar'> </td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a> </td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a> </td><" +
            "/tr>");

        $('#yoxlayan_shexsler>tbody .yellow').click(function()
        {
            $(this).parent('td').parent('tr').remove();
        });
        $("select[filterin_novu"+say+"]").select2();

        var lastTr = $('#yoxlayan_shexsler>tbody tr:last');

        emekdashlar(lastTr.find('.emekdashlar'));
    });

    $('#yoxlayan_shexsler>tbody').on("click",".blue",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            tr_id = tr.attr("tr_id"),
            user_id = tr.children('td').eq(1).children('input').val(),
            user_id_ad = tr.children('td').eq(1).find('span.select2-chosen').text(),
            sayi = tr.children('td').eq(0).text();


            tr.attr('user_id',user_id);


        if(user_id.trim()=="")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir...</td></tr>');
            setTimeout(function(){$('#yoxlayan_shexsler>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/<?= $GLOBALS['phpfile'] ?>", {"tr_id":tr_id,"user_id":user_id},function(result)
            {
                if(result=="error")
                {
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Səhv var.</td></tr>');
                    setTimeout(function(){$('#yoxlayan_shexsler>tbody tr[error]').remove();}, 3000);
                }
                else
                {

                    tr.attr("tr_id",result).html("" +
                        "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                        "<td>"+user_id_ad+"</td>" +
                        "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td>" +
                        "<td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");

                }
            });
        }
    });

    $('#yoxlayan_shexsler>tbody').on("click",".purple",function()
    {
        var tr = $(this).parents("tr:eq(0)"),
            user_id = tr.attr("user_id"),
            user_id_ad = tr.children('td').eq(1).text(),
            sayi = tr.children('td').eq(0).text(),
            th = tr;

        tr.html("" +
            "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
            "<td> <input placeholder='Əməkdaş' type='text' class='form-control emekdashlar'> </td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td>" +
            "<td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td>");

        emekdashlar(tr.find('.emekdashlar'));

        tr.children("td").eq(1).find("input").select2("data",{"id":user_id,"text":user_id_ad});

        th.find('.yellow').click(function()
        {
            th.html("" +
                "<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                "<td>"+user_id_ad+"</td>" +
                "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td>" +
                "<td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
        });
    });

    $('#yoxlayan_shexsler>tbody').on("click",".red",function()
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic [data-id=beli]').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/<?= $GLOBALS['phpfile'] ?>", {'tr_id':idsi,'sil':"sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#yoxlayan_shexsler>tbody').children('tr').children('td').length)
                {
                    $('#yoxlayan_shexsler>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });

</script>