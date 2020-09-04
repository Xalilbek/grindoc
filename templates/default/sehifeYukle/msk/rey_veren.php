<?php
$user = new User();
$TenantId = $user->getActiveTenantId();
$emekdashlarSiyahi = DB::fetchAll("SELECT USERID,CONCAT(Soyadi,' ',Adi) AS user_ad FROM v_users WHERE TenantId='$TenantId'");

print "<script>";
print "var emekdashlar = {};";

foreach($emekdashlarSiyahi as $emekdashInf)
{
    print "emekdashlar[".(int)$emekdashInf[0]."] = '".escape($emekdashInf[1])."';";
}

print "</script>";

?>
<style>
    .sechilib{
        background-color: #32c5d2 !important;
        color: white;
    }
</style>
<div class="tab-pane" id="tab_2">
    <div class="row">
        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="icrachi_sened_tip">
                    <tbody>
                        <tr tip="1" id="1" class="sechilib" onclick="Manage(this.id);">
                            <td><i class="fa fa-file"></i> Daxil olan hüquqi sənəd</td>
                        </tr>
                        <tr tip="2" id="2" onclick="Manage(this.id);">
                            <td><i class="fa fa-file"></i> Daxil olan vətəndaş müraciəti</td>
                        </tr>
                        <tr tip="3" id="3" onclick="Manage(this.id);">
                            <td><i class="fa fa-file"></i> Daxili sənəd</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-9">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" style="display: none;" id="rey_veren_table">
                    <thead>
                    <tr>
                        <th style="width: 5%;">  №</th>
                        <th style="width:60%;"></i> Rəy verən</th>

                        <th style="text-align: center;width: 25%"> <i class="fa fa-wrench"></i></th>
                        <th style="text-align:right; width: 25%">  <a href="javascript:;" class="btn default btn-xs green" id="icrachi_shexs_add"><i class="icon-plus"></i>Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $query = DB::fetchAll("SELECT tb1.*,CONCAT(tb2.Soyadi,' ',tb2.Adi) AS user_ad,CONCAT(tb3.Soyadi,' ',tb3.Adi) AS komekchi_user_ad FROM tb_prodoc_icrachi_shexsler tb1 LEFT JOIN tb_users tb2 ON tb2.USERID=tb1.user_id LEFT JOIN tb_users tb3 ON tb3.USERID=tb1.komekchi_user_id where icrachi_tip='rey_muellifi' ORDER BY sira");
                    if(count($query)==0)
                    {
                        print "<tr bosh><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query))
                        {
                            $i++;


                            print '<tr tip="'.(int)$mass['sened_tip'].'" tr_id="'.(int)$mass['id'].'" '.((int)$mass['sened_tip']=="2"?'style="display: none;"':'').'>';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['user_ad']).'</td>';
                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                            print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
                <table class="table table-striped table-bordered table-advance table-hover" style="display: none;" id="kurator_shexs_table">
                    <thead>
                    <tr>
                        <th style="width: 5%;">  №</th>
                        <th style="width:60%;"></i> Kurator şəxs</th>

                        <th style="text-align: center;width: 25%"> <i class="fa fa-wrench"></i></th>
                        <th style="text-align:right; width: 25%">  <a href="javascript:;" class="btn default btn-xs green" id="kurator_shexs_add"><i class="icon-plus"></i>Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $query2 = DB::fetchAll("SELECT tb1.*,CONCAT(tb2.Soyadi,' ',tb2.Adi) AS user_ad,CONCAT(tb3.Soyadi,' ',tb3.Adi) AS komekchi_user_ad FROM tb_prodoc_icrachi_shexsler tb1 LEFT JOIN tb_users tb2 ON tb2.USERID=tb1.user_id LEFT JOIN tb_users tb3 ON tb3.USERID=tb1.komekchi_user_id where icrachi_tip='kurator_shexs' ORDER BY sira");

                    if(count($query2)==0)
                    {
                        print "<tr bosh><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query2))
                        {
                            $i++;


                            print '<tr tip="'.(int)$mass['sened_tip'].'" tr_id="'.(int)$mass['id'].'" '.((int)$mass['sened_tip']=="2"?'style="display: none;"':'').'>';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['user_ad']).'</td>';

                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                            print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
                <table class="table table-striped table-bordered table-advance table-hover" style="display: none;" id="ishtirakchi_shexs_table">
                    <thead>
                    <tr>
                        <th style="width: 5%;">№</th>
                        <th style="width:60%;"><i class="fa fa-user"></i> Həm icraçı</th>
                        <th style="text-align: center;width: 25%">  <i class="fa fa-wrench"></i></th>
                        <th style="text-align:right; width: 25%">   <a href="javascript:;" class="btn default btn-xs green" id="ishtirakchi_shexs_add"><i class="icon-plus"></i>Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $query2 = DB::fetchAll("SELECT tb1.*,CONCAT(tb2.Soyadi,' ',tb2.Adi) AS user_ad,CONCAT(tb3.Soyadi,' ',tb3.Adi) AS komekchi_user_ad FROM tb_prodoc_icrachi_shexsler tb1 LEFT JOIN tb_users tb2 ON tb2.USERID=tb1.user_id LEFT JOIN tb_users tb3 ON tb3.USERID=tb1.komekchi_user_id where icrachi_tip='ishtirakchi_shexs' ORDER BY sira");

                    if(count($query2)==0)
                    {
                        print "<tr bosh><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query2))
                        {
                            $i++;


                            print '<tr tip="'.(int)$mass['sened_tip'].'" tr_id="'.(int)$mass['id'].'" '.((int)$mass['sened_tip']=="2"?'style="display: none;"':'').'>';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['user_ad']).'</td>';

                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                            print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
                <table class="table table-striped table-bordered table-advance table-hover" style="display: none;" id="yoxlayan_shexs_table">
                    <thead>
                    <tr>
                        <th style="width: 5%;">  №</th>
                        <th style="width:60%;"></i> Yoxlayan şəxs</th>
                        <th style="text-align: center;width: 25%"> <i class="fa fa-wrench"></i></th>
                        <th style="text-align:right; width: 25%">  <a href="javascript:;" class="btn default btn-xs green" id="yoxlayan_shexs_add"><i class="icon-plus"></i>Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $query2 = DB::fetchAll("SELECT tb1.*,CONCAT(tb2.Soyadi,' ',tb2.Adi) AS user_ad,CONCAT(tb3.Soyadi,' ',tb3.Adi) AS komekchi_user_ad FROM tb_prodoc_icrachi_shexsler tb1 LEFT JOIN tb_users tb2 ON tb2.USERID=tb1.user_id LEFT JOIN tb_users tb3 ON tb3.USERID=tb1.komekchi_user_id where icrachi_tip='yoxlayan_shexs' ORDER BY sira");

                    if(count($query2)==0)
                    {
                        print "<tr bosh><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query2))
                        {
                            $i++;


                            print '<tr tip="'.(int)$mass['sened_tip'].'" tr_id="'.(int)$mass['id'].'" '.((int)$mass['sened_tip']=="2"?'style="display: none;"':'').'>';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['user_ad']).'</td>';

                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                            print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>

                <table class="table table-striped table-bordered table-advance table-hover" style="display: none;" id="icraci_shexs_table">
                    <thead>
                    <tr>
                        <th style="width: 5%;">  №</th>
                        <th style="width:60%;"></i> İcraçı şəxs</th>
                        <th style="text-align: center;width: 25%"> <i class="fa fa-wrench"></i></th>
                        <th style="text-align:right; width: 25%">  <a href="javascript:;" class="btn default btn-xs green" id="icraci_shexs_add"><i class="icon-plus"></i>Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $query2 = DB::fetchAll("SELECT tb1.*,CONCAT(tb2.Soyadi,' ',tb2.Adi) AS user_ad,CONCAT(tb3.Soyadi,' ',tb3.Adi) AS komekchi_user_ad FROM tb_prodoc_icrachi_shexsler tb1 LEFT JOIN tb_users tb2 ON tb2.USERID=tb1.user_id LEFT JOIN tb_users tb3 ON tb3.USERID=tb1.komekchi_user_id where icrachi_tip='icraci_shexs' ORDER BY sira");

                    if(count($query2)==0)
                    {
                        print "<tr bosh><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query2))
                        {
                            $i++;


                            print '<tr tip="'.(int)$mass['sened_tip'].'" tr_id="'.(int)$mass['id'].'" '.((int)$mass['sened_tip']=="2"?'style="display: none;"':'').'>';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['user_ad']).'</td>';

                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                            print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
                <!-- Məlumatlandırılan Şəxslər -->
                <table class="table table-striped table-bordered table-advance table-hover" style="display: none;" id="melumatlandirilan_table">
                    <thead>
                    <tr>
                        <th style="width: 5%;">  №</th>
                        <th style="width:60%;"></i> Məlumatlandırılan şəxs</th>

                        <th style="text-align: center;width: 25%"> <i class="fa fa-wrench"></i></th>
                        <th style="text-align:right; width: 25%">  <a href="javascript:;" class="btn default btn-xs green" id="melumatlandirilan_shexs_add"><i class="icon-plus"></i>Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $query = DB::fetchAll("SELECT tb1.*,CONCAT(tb2.Soyadi,' ',tb2.Adi) AS user_ad,CONCAT(tb3.Soyadi,' ',tb3.Adi) AS komekchi_user_ad FROM tb_prodoc_icrachi_shexsler tb1 LEFT JOIN tb_users tb2 ON tb2.USERID=tb1.user_id LEFT JOIN tb_users tb3 ON tb3.USERID=tb1.komekchi_user_id where icrachi_tip='melumatlandirilan' ORDER BY sira");
                    if(count($query)==0)
                    {
                        print "<tr bosh><td colspan='100%'>Boşdur!</td></tr>";
                    }
                    else
                    {
                        $i = 0;
                        while($mass = array_shift($query))
                        {
                            $i++;


                            print '<tr tip="'.(int)$mass['sened_tip'].'" tr_id="'.(int)$mass['id'].'" '.((int)$mass['sened_tip']=="2"?'style="display: none;"':'').'>';
                            print '<td style="width:20px;text-align:center;">'.$i.'</td>';
                            print '<td>'.htmlspecialchars($mass['user_ad']).'</td>';
                            print '<td style="text-align:center;"><a href="#" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Düzəliş</a></td>';
                            print '<td style="width:100px;text-align:center;"><a href="javascript:;" class="btn default btn-xs red"><i class="fa fa-trash"></i> Sil</a></td>';
                            print '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
                <!--/ Məlumatlandırılan Şəxslər -->
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
    $('#icrachi_sened_tip .sechilib').removeClass('sechilib');
    var errorSilTimeout = 0;

    //////////ICARCHI SHEXSLER ////////////

    $('#icrachi_shexs_add').click(function()
    {
        addRow('#rey_veren_table', 'rey_veren_save');
    });
    $('#rey_veren_table>tbody').on('click','.blue',function()
    {
        saveInfo($('#rey_veren_save'),'#rey_veren_table','rey_muellifi','sened_novleri');

    });
    $('#rey_veren_table>tbody').on('click','.purple',function()
    {
        edit($(this),'rey_veren_save')

    });
    $('#rey_veren_table>tbody').on('click','.red',function()
    {
        deleteInfo($(this),'sened_novleri');
    });
    //***************************************//

    //////////MELUMATLANDIRILAN SHEXSLER ////////////

    $('#melumatlandirilan_shexs_add').click(function()
    {
        addRow('#melumatlandirilan_table', 'melumatlandirilan_save');
    });
    $('#melumatlandirilan_table>tbody').on('click','.blue',function()
    {
        saveInfo($('#melumatlandirilan_save'),'#melumatlandirilan_table','melumatlandirilan','sened_novleri');

    });
    $('#melumatlandirilan_table>tbody').on('click','.purple',function()
    {
        edit($(this),'melumatlandirilan_save')
    });
    $('#melumatlandirilan_table>tbody').on('click','.red',function()
    {
        deleteInfo($(this),'sened_novleri');
    });
    //***************************************//



    ////////////KURATOR//////////////
    $('#kurator_shexs_add').click(function()
    {
        addRow('#kurator_shexs_table', 'kurator_shexs_save');
    });


    $('#kurator_shexs_table>tbody').on('click','.blue',function()
    {
        saveInfo($('#kurator_shexs_save'),'#kurator_shexs_table','kurator_shexs','sened_novleri');

    });

    $('#kurator_shexs_table>tbody').on('click','.purple',function()
    {
        edit($(this),'kurator_shexs_save')

    });
    $('#kurator_shexs_table>tbody').on('click','.red',function()
    {
        deleteInfo($(this),'sened_novleri');
    });
    //*************************************//



    /////////ISHTIRAKCHI SHEXS//////////////
    $('#ishtirakchi_shexs_add').click(function()
    {
        addRow('#ishtirakchi_shexs_table', 'ishtirakchi_shexs_save');
    });


    $('#ishtirakchi_shexs_table>tbody').on('click','.blue',function()
    {
        saveInfo($('#ishtirakchi_shexs_save'),'#ishtirakchi_shexs_table','ishtirakchi_shexs','sened_novleri');

    });

    $('#ishtirakchi_shexs_table>tbody').on('click','.purple',function()
    {
        edit($(this),'ishtirakchi_shexs_save')

    });
    $('#ishtirakchi_shexs_table>tbody').on('click','.red',function()
    {
        deleteInfo($(this),'sened_novleri');
    });
    //*************************************//



    ///////////YOXLAYAN SHEXS//////////////
    $('#yoxlayan_shexs_add').click(function()
    {
        addRow('#yoxlayan_shexs_table', 'yoxlayan_shexs_save');
    });


    $('#yoxlayan_shexs_table>tbody').on('click','.blue',function()
    {
        saveInfo($('#yoxlayan_shexs_save'),'#yoxlayan_shexs_table','yoxlayan_shexs','sened_novleri');

    });

    $('#yoxlayan_shexs_table>tbody').on('click','.purple',function()
    {
        edit($(this),'yoxlayan_shexs_save')

    });
    $('#yoxlayan_shexs_table>tbody').on('click','.red',function()
    {
        deleteInfo($(this),'sened_novleri');
    });
    //*************************************//

    //////////	İcraçı  SHEXS //////////////
    $('#icraci_shexs_add').click(function()
    {
        addRow('#icraci_shexs_table', 'icraci_shexs_save');
    });


    $('#icraci_shexs_table>tbody').on('click','.blue',function()
    {
        saveInfo($('#icraci_shexs_save'),'#icraci_shexs_table','icraci_shexs','sened_novleri');

    });

    $('#icraci_shexs_table>tbody').on('click','.purple',function()
    {
        edit($(this),'icraci_shexs_save')

    });
    $('#icraci_shexs_table>tbody').on('click','.red',function()
    {
        deleteInfo($(this),'sened_novleri');
    });
    //*************************************//




    $('#icrachi_sened_tip>tbody>tr').click(function(){
        var tip = $(this).attr("tip");
        $('#rey_veren_table>tbody>tr[tip="'+tip+'"]').each(function(i){
            $(this).find('td:eq(0)').text(i+1);
        });
        $('#melumatlandirilan_table>tbody>tr[tip="'+tip+'"]').each(function(i){
            $(this).find('td:eq(0)').text(i+1);
        });
        $('#kurator_shexs_table>tbody>tr[tip="'+tip+'"]').each(function(i){
            $(this).find('td:eq(0)').text(i+1);
        });
        $('#ishtirakchi_shexs_table>tbody>tr[tip="'+tip+'"]').each(function(i){
            $(this).find('td:eq(0)').text(i+1);
        });

        $('#yoxlayan_shexs_table>tbody>tr[tip="'+tip+'"]').each(function(i){
            $(this).find('td:eq(0)').text(i+1);
        });

        $('#icraci_shexs_table>tbody>tr[tip="'+tip+'"]').each(function(i){
            $(this).find('td:eq(0)').text(i+1);
        });
    });
    function Manage(id) {
        $('#rey_veren_table').show();
        $('#rey_veren_table tbody tr').hide();
        $('#melumatlandirilan_table').show();
        $('#melumatlandirilan_table tbody tr').hide();
        $('#kurator_shexs_table').show();
        $('#kurator_shexs_table tbody tr').hide();
        $('#ishtirakchi_shexs_table').show();
        $('#ishtirakchi_shexs_table tbody tr').hide();
        $('#yoxlayan_shexs_table').show();
        $('#yoxlayan_shexs_table tbody tr').hide();
        $('#icraci_shexs_table').show();
        $('#icraci_shexs_table tbody tr').hide();
        $('#icrachi_sened_tip .sechilib').removeClass('sechilib');
        $('#'+id).addClass('sechilib');

        $(this).addClass('sechilib');$('#rey_veren_table tbody tr[tip='+id+']').show();
        $(this).addClass('sechilib');$('#melumatlandirilan_table tbody tr[tip='+id+']').show();
        $(this).addClass('sechilib');$('#kurator_shexs_table tbody tr[tip='+id+']').show();
        $(this).addClass('sechilib');$('#ishtirakchi_shexs_table tbody tr[tip='+id+']').show();
        $(this).addClass('sechilib');$('#yoxlayan_shexs_table tbody tr[tip='+id+']').show();
        $(this).addClass('sechilib');$('#icraci_shexs_table tbody tr[tip='+id+']').show();

    }

    function addRow(table, saveId){
        if($(table+" tbody tr[bosh]").length)
        {
            $(table+' tbody tr[bosh]').remove();
        }

        var lengthOfLastTd=$(table+' tbody tr[tip="'+$("#icrachi_sened_tip tr.sechilib").attr("tip")+'"]').length-1;
        if ($(table+' tbody tr[tip="'+$("#icrachi_sened_tip tr.sechilib").attr("tip")+'"]').eq(lengthOfLastTd).children('td').eq(1).find('select').length>0){
           return false;
        }

        var say = $(table+' tbody tr[tip="'+$("#icrachi_sened_tip tr.sechilib").attr("tip")+'"]').length+1,emekdashlarHtml = "",sechilenSenedTip = $("#icrachi_sened_tip tr.sechilib").attr("tip");
        for(var uid in emekdashlar)
        {
            emekdashlarHtml += "<option value='"+uid+"'>"+emekdashlar[uid]+"</option>";
        }
        $(table+' tbody').append("<tr tip='"+sechilenSenedTip+"' tr_id='0'><td style='width:20px;text-align:center;'>"+say+"</td><td><select class='form-control' placeholder='İcraçı şəxsi seçin'><option></option>"+emekdashlarHtml+"</select></td><td style='text-align:center;'><a href='javascript:;' id='"+saveId+"' class='btn default btn-xs blue'> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td></tr>");

        $(table+' tbody tr:last input[type="checkbox"]').uniform();


        $(table+' tbody tr:last select').select2();
        $(table+' tbody tr:last .yellow').click(function()
        {
            $(this).parents('tr').eq(0).remove();
        });
    }

    function saveInfo(button, table, icrachi_tip, sendPost)
    {
        var tr = $(button).parents('tr').eq(0),
            icarchi_shexs = tr.find('select').eq(0).val(),
            icarchi_shexs_ad = tr.find('select').eq(0).children(":selected").text(),
            tip = tr.attr("tip"),
            t = $(button),
            sayi = tr.children('td').eq(0).text(),
            tr_id = tr.attr("tr_id");


        if ((icarchi_shexs > 0) == false) {
            tr.next("tr[error]").remove();
            clearTimeout(errorSilTimeout);
            tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bütün sahələrin doldurulması mütləqdir.</td></tr>');
            errorSilTimeout = setTimeout(function () {
                $(table+' tbody tr[error]').remove();
            }, 3000);
        }
        else {
            $.post(proBundle + "includes/msk/"+sendPost+".php", {
                'icarchi_shexs': icarchi_shexs,
                'icarchi_shexs_id': tr_id,
                'tip': tip,
                'icrachi_tip': icrachi_tip
            }, function (result) {
                if (result == "error") {
                    tr.next("tr[error]").remove();
                    clearTimeout(errorSilTimeout);
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Bu əməkdaşı artıq icraçı kimi əlavə etmisiniz.</td></tr>');
                    errorSilTimeout = setTimeout(function () {
                        $(table+' tbody tr[error]').remove();
                    }, 3000);
                }
                else if (parseInt(result) > 0) {
                    tr.attr("tr_id", result).html("<td style='width:20px;text-align:center;'>" + sayi + "</td><td>" + icarchi_shexs_ad + "</td> <td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
                }
                else {
                    tr.next("tr[error]").remove();
                    clearTimeout(errorSilTimeout);
                    tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">Yalnışlıq baş verdi, zəhmət olmasa yenidən cəhd edin.</td></tr>');
                    errorSilTimeout = setTimeout(function () {
                        $(table+' tbody tr[error]').remove();
                    }, 3000);
                }
            });
        }
    }
    function edit(button, saveId) {
        var tr = button.parents("tr").eq(0),
            icrachi_shexs_ad = tr.children("td").eq(1).text(),
            komekchi_shexs_ad = tr.children("td").eq(2).text(),
            komekchi_tip_ad = tr.children("td").eq(3).text(),
            sayi = tr.children('td').eq(0).text(),
            viza = (tr.children('td').eq(4).children('i').length>0),
            sira = tr.children("td").eq(5).text(),
            emekdashlarHtml1 = "",
            emekdashlarHtml2 = "",
            th = button.parent('td').parent('tr');
        for(var uid in emekdashlar)
        {
            emekdashlarHtml1 += "<option value='"+uid+"'"+(emekdashlar[uid]==icrachi_shexs_ad?" selected":"")+">"+emekdashlar[uid]+"</option>";
            //emekdashlarHtml2 += "<option value='"+uid+"'"+(emekdashlar[uid]==komekchi_shexs_ad?" selected":"")+">"+emekdashlar[uid]+"</option>";
        }
        button.parent('td').parent('tr').html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td><select class='form-control' placeholder='İcraçı şəxsi seçin'><option></option>"+emekdashlarHtml1+"</select></td><td style='text-align:center;'><a href='javascript:;' id='"+saveId+"' class='btn default btn-xs blue'><i class='fa fa-save'></i> Yadda saxla</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> İmtina</a></td>");
        th.find('input[type="checkbox"]').uniform();
        tr.find('select').select2();
        // tr.find('select[k_tip]').select2("val",(komekchi_tip_ad=="Köməkçi"?"komekchi":(komekchi_tip_ad=="Yoxlayıcı"?"yoxlayici":"evez_edici")));
        tr.find('.yellow').click(function()
        {
            tr.html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+icrachi_shexs_ad+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> Düzəliş</a></td><td style='width:100px;text-align:center;'><a href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> Sil</a></td>");
        });
    }

    function deleteInfo(button, sendPost) {
        var idsi = button.parents('tr').attr("tr_id"),
            t = button;
        $('#basic').attr('idsi', idsi);
        $('#msk_delete').click();
        $('#basic .blue').unbind('click').click(function()
        {
            var idsi = $('#basic').attr('idsi');
            $.post(proBundle + "includes/msk/"+sendPost+".php", {'icarchi_shexs_id':idsi,'icarchi_shexs_sil':"icarchi_shexs_sil"},function(result)
            {
                $('#basic .default').click();
                t.parent('td').parent('tr').remove();
                if(!$('#rey_veren_table tbody').children('tr').children('td').length)
                {
                    $('#rey_veren_table tbody').html("<tr bosh><td colspan='100%'>Boşdur!</td></tr>");
                }
            });
        });
    }
</script>