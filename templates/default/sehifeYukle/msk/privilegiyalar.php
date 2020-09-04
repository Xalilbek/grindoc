<div class="tab-pane" id="tab_31">
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="privilegiya_table">
                    <thead>
                    <tr>
                        <th style="width: 40px;">№</th>
                        <th style="max-width: 250px;">Rol</th>
                        <th style="max-width: 250px;">Privilegiya</th>
                        <th style="text-align:center;" colspan="2"><a href="javascript:;" class="btn default btn-xs green" id="privilegiya_add"><i class="icon-plus"></i> Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php

                    $sira = 0;
                    foreach($privilegiyalar as $priv)
                    {
                        $rollar_adlar_arr = [];
                        $exploded_roles = explode(',',$priv['roles']);
                        foreach ($exploded_roles as $role) {
                            $role_ad = DB::fetchColumn("SELECT [name] FROM tb_prodoc_privilegiyalar WHERE id = '$role'");
                            $rollar_adlar_arr[] = $role_ad;
                        }
                        $role_adlar = implode(', ', $rollar_adlar_arr);

                        $sira++;
                        print '<tr tr_id="'.(int)$priv['id'].'">';
                        print '<td>'.$sira.'</td>';
                        print '<td>'.$priv['name'].'</td>';
                        print '<td rollar="' . $priv['roles'] . '" >'.$role_adlar.'</td>';
                        print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                        print '<td style="text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td></tr>';
                    }

                    ?>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6" id="privilegiya_tabs">
            <div class="row">
                <div class="col-xs-12">
                    <ul class="nav nav-pills nav-justified">
						<?php if ($prName === "msk_protask_privilegiyalar"): ?>
							<li role='presentation'><a data-toggle='tab' href='#protask'>Dashboard</a></li>
						<?php else: ?>
							<li role='presentation'><a data-toggle='tab' href='#dashboard'>Dashboard</a></li>
							<li role='presentation'><a data-toggle='tab' href='#daxili_senedler'>Daxili sənədlər</a></li>
						<?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="tab-content" id="">

                        <table class="table table-striped table-bordered table-advance table-hover" id="privilegiya_tabs_table">
                            <thead>
                            <tr>
                                <th style="max-width: 250px;">Menyular və əməliyyatlar</th>
                                <th style="max-width: 250px;">Priveligiya</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
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

    var rollar = JSON.parse('<?= json_encode($privilegiyalar); ?>');

    var rollarHTML = '';
    for (var i = 0; i < Object.keys(rollar).length; i++) {
        if ( parseInt(rollar[i].is_group) == 0 )
            rollarHTML += '<option value="' + rollar[i].id + '">' + rollar[i].name + '</option>'
    }

    function sssspanToSelect(element) {

        if (element.prop('checked'))
        {
           element.parents('td:eq(0)').children('sssspan').hide();
           element.parents('td:eq(0)').children('.select2me').show();
        }
        else
        {
           element.parents('td:eq(0)').children('.select2me').hide();
           element.parents('td:eq(0)').children('sssspan').show()
        }
    }

    $('#privilegiya_add').click( function()
    {
        if($("#privilegiya_table>tbody tr[time='null']").length)
        {
            $('#privilegiya_table>tbody tr').remove();
        }

        var say = $('#privilegiya_table>tbody tr').length+1;

        $('#privilegiya_table>tbody').append("<tr tr_id='0'>" +
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td><input type='text' class='form-control'></td>" +
            "<td>" +
                "<input class='is_group' onchange='sssspanToSelect($(this))' style='width:100%; margin-right:10px' type='checkbox'>" +
                "<sssspan> </sssspan>" +
                "<select style='width:80%;display:none' class='form-control select2me' multiple>" + rollarHTML + "</select>" +
            "</td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td></tr>");


        var last_tr_td = $("#privilegiya_table tbody tr:last td");

        last_tr_td.eq(2).find('.is_group').uniform();
        last_tr_td.eq(2).find('select').select2();

        last_tr_td.find('.yellow').click(function() {
            $(this).parent('td').parent('tr').remove();
        });

        last_tr_td.eq(2).find('.is_group').click( function () {
            if ($(this).is(':checked'))
                last_tr_td.eq(2).find('select').fadeIn();
            else
                last_tr_td.eq(2).find('select').fadeOut();
        });
    });

    $('#privilegiya_table>tbody').on("click",".blue",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            tr_id = tr.attr("tr_id"),
            sayi = tr.children('td').eq(0).text(),
            td = $(this).parent('td').parent('tr').children('td'),
            rol         = td.eq(1).find('input').val(),
            is_group    = td.eq(2).find('.is_group').is(':checked') ? '1' : '0',
            rollar_idler    = td.eq(2).find('select').select2("val"),
            rollar_data     = td.eq(2).find('select').select2("data");

        var rollar_adlar = rollar_data.map(e => e.text).join(",");

        console.log(rollar_idler);
        if(rol.trim()=="")
        {
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            setTimeout(function(){$('#privilegiya_table>tbody tr[error]').remove();}, 3000);
        }
        else
        {
            $.post(proBundle + "includes/msk/sened_novleri.php",
                {
                    'privilegiya_id' : tr_id,
                    'rol': rol,
                    'is_group': is_group,
                    'rollar_idler': rollar_idler
                },
                function(result)
                {
                    if(result=="error")
                    {
                        tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu adla şöbə mövcuddur.</td></tr>');
                        setTimeout(function(){$('#privilegiya_table>tbody tr[error]').remove();}, 3000);
                    }
                    else
                    {
                        tr.attr("tr_id",result).html("<td style='width:20px;text-align:center;'>"+sayi+"</td>" +
                            "<td>"+rol+"</td>" +
                            "<td rollar='" + rollar_idler + "' >"+rollar_adlar+"</td>" +
                            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td>" +
                            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
                    }
                }
            );
        }
    });

    $("#privilegiya_table").on("click",".purple",function()
    {
        var t = $(this),
            tr = t.parents("tr").eq(0),
            tr_id = tr.attr("tr_id"),
            say = tr.children('td').eq(0).text(),
            td = $(this).parent('td').parent('tr').children('td'),
            rol         = td.eq(1).text().trim(),
            rollar_idler= td.eq(2).attr("rollar"),
            is_group    = rollar_idler.trim() != '' ? 1 : 0;

        var tr_clonu = tr.clone();

        tr.html(
            "<td style='width:20px;text-align:center;'>"+say+"</td>" +
            "<td><input type='text' class='form-control' value='" + rol + "'></td>" +
            "<td>" +
                "<input class='is_group' onchange='sssspanToSelect($(this))' style='width:100%; margin-right:10px' type='checkbox'>" +
                "<sssspan> </sssspan>" +
                "<select style='width:80%;display:none' class='form-control select2me' multiple>" + rollarHTML + "</select>" +
            "</td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td>" +
            "<td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td></tr>"
        );

        tr.find('.is_group').uniform();
        if (is_group) tr.find('.is_group').trigger('click');
        $.uniform.update();

        tr.find('.select2me').val(rollar_idler.split(",")).select2();

        tr.find('.yellow').click(function()
        {
            $(this).parent('td').parent('tr').html(tr_clonu.html());
        });
    });

    $('#privilegiya_table>tbody').on("click",".red",function()
    {
        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic [data-id=beli]').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/sened_novleri.php", {'privilegiya_id':idsi,'privilegiya_sil':"privilegiya_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#privilegiya_table>tbody').children('tr').children('td').length)
                {
                    $('#privilegiya_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    });

    $('#privilegiya_tabs .nav').on("click","a[href]",function()
    {
        var tr_id = $('#privilegiya_table>tbody').find('tr.active').attr('tr_id'),
            key = $(this).attr('href').replace('#', '');

        $.post( proBundle + "includes/msk/alt_privilegiyalar.php",
            {
                'key' : key,
                'role_id' : tr_id,
				'template_name': '<?= $prName ?>'
            },
            function(netice)
            {
                var r = JSON.parse(netice);

                if(r.status == 'success') {
                    console.log(r.is_group);
                    if (r.is_group == '0') {
                        $('#privilegiya_tabs').fadeIn();
                        $('#privilegiya_tabs_table').fadeIn();
                        $('#privilegiya_tabs_table').find('tbody').html(r.result);
                    }
                    else {
                        $('#privilegiya_tabs').hide();
                    }
                }
            }
        );
    });
    var tr_id="";
    $('#privilegiya_table>tbody').on("click","tr",function()
    {
        tr_id = $(this).attr('tr_id');

        $(this).parent().children().removeClass('active');
        $(this).addClass('active');

        $('#privilegiya_tabs_table').hide();

        <?php if ($prName === "msk_protask_privilegiyalar"): ?>
			$('#privilegiya_tabs .nav a[href=#protask]').trigger('click');
		<?php else: ?>
			$('#privilegiya_tabs .nav a[href=#dashboard]').trigger('click');
		<?php endif; ?>
    });

    $('#privilegiya_table>tbody').find('tr:first').trigger('click');


    $('#privilegiya_tabs_table>tbody').on("change","select",function()
    {
        var priv_id = $(this).parent().parent().attr('priv-id'),
            tr_id = $('#privilegiya_table>tbody').find('tr.active').attr('tr_id'),
            priv_value = $(this).val(),
            priv_glav  = $(this).parents().parents().attr('priv-glav');

        $.post( proBundle + "includes/msk/alt_privilegiyalar.php",
            {
                'role_id' : tr_id,
                'priv_id' : priv_id,
                'priv_value' : priv_value,
                'priv-glav'  : priv_glav,
				'template_name': '<?= $prName ?>'
            },
            function(netice)
            {
                var r = JSON.parse(netice);

                if(r.status == 'succes')
                    $("#privilegiya_table").find("tr[tr_id="+tr_id+"]").click();
            }
        );
    });

</script>