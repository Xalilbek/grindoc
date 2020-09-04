<link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="asset/global/plugins/uniform/css/uniform.default.css?v1"/>

<style>
    .cemi label
    {
        float: right;
        font-size: 17px;
        font-weight: 900;
    }

    textarea {
        max-width: 140px;
        height: 40px;
        margin-bottom: -15px;
        display: inherit !important;
        visibility: hidden;
    }
</style>

<div class="modal-body">
    <table class="table table-striped table-bordered table-advance table-hover">
        <thead>
        <tr>
            <th style="width: 20px; text-align: center;">№</th>
            <th style="width: 90px; text-align: center;">Malın kodu</th>
            <th style="text-align: center;">Malın adı</th>
            <th style="width: 90px; text-align: center;">Ölçü vahidi</th>
            <th style="width: 90px; text-align: center;">Miqdar</th>
            <th style="text-align: center;">Vahidin dəyəri</th>
            <th style="width: 110px; text-align: center;">Məbləğin cəmi</th>
            <th style="text-align: center;">İcra müddəti</th>
            <th style="text-align: center;">Şəkil</th>
        </tr>
        </thead>
        <tbody class="mallar">
        <?php $j = 0; foreach ($docs as $doc): $j++; ?>
            <tr malin_id="<?php print $doc['id']; ?>">
                <td><?php print $j; ?></td>
                <td><?php print htmlspecialchars($doc['malin_kodu']); ?></td>
                <td style="width: 120px"><?php print htmlspecialchars($doc['mal_adi']); ?></td>
                <td><?php print htmlspecialchars($doc['olcu_vahidi']); ?></td>
                <td><?php print htmlspecialchars($doc['miqdar']); ?></td>
                <td style="width: 100px">
                    <input type="text" class="spinner-input form-control mebleq" value="<?php print htmlspecialchars($doc['mebleq']); ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="12" disabled>
                </td>
                <td style="width: 100px">
                    <input type="text" class="spinner-input form-control mebleq_cemi" disabled>
                </td>
                <td>
                    <input type="text" style="width: 50px; display: inherit !important;" class="spinner-input form-control gun" value="<?php print htmlspecialchars($doc['gun']); ?>" disabled>
                    <input type="checkbox" data-plugin="uniform" name="gun_activ" hidden>
                    <textarea placeholder="Səbəb qeyd et" class="sebeb_qeyd_et"><?= $doc['sebeb_qeyd_et']; ?></textarea>
<!--                    <input type="text" style="width: 150px; display: inherit !important; visibility: hidden;" value="--><?//= $doc['sebeb_qeyd_et']; ?><!--" class="spinner-input form-control sebeb_qeyd_et" placeholder="Səbəb qeyd et">-->
                </td>
                <td style="width: 150px;">
                    <?php $i = 0; foreach ($fileName as $value): $i++; ?>
                        <?php if($doc['id'] == $value['module_entry_id']): ?>
                            <a href="<?php print PRODOC_FILES_WEB_PATH.$value['file_actual_name']; ?>" download><?php print htmlspecialchars($value['file_original_name']); ?></a>
                            <br>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="cemi">
        <label></label>
        <label for="">Cəmi:</label>
    </div>
</div>

<div class="modal-footer" style="border: 0;">
    <div style="float: right;">
        <button type="button" class="btn btn-outline btn-circle green-meadow testiqle" style="display: none;">
            Təsdiqlə
        </button>
        <button type="button" data-dismiss="modal" class="btn btn-outline btn-circle grey-cascade bagla">
            Bağla
        </button>
    </div>
</div>

<script type="text/javascript">

    $(function() {
        oldSum();
        $("input[type='checkbox']").uniform();

        var edit = <?php echo $edit; ?>;

        if(edit)
        {
            $('.testiqle').show();
            $('.mebleq').removeAttr('disabled');
            $('.mallar tr').each(function () {
                $(this).find('td:eq(7) input').eq(1).parents('div').show();
            });
        }

        $('.mebleq').on('input', function () {
            calculQuantity($(this));
        });

        $('input[name=gun_activ]').on('click', function () {
            var gun = $(this).parents('td').find($('.gun')),
            qeyd_et = $(this).parents('td').find($('.sebeb_qeyd_et'));

            if($(this).is(':checked'))
            {
                gun.removeAttr('disabled');
                qeyd_et.css("visibility","visible");
            }
            else
            {
                gun.prop('disabled', true);
                qeyd_et.css("visibility","hidden");
            }
        });
    });

    $('.testiqle').click(function () {
        var MallarObj = [],
            testiq_id = '<?php echo $testiq_id; ?>',
            sehv_var  = false;

        $('.mallar tr').each(function () {
            var check = $(this).find('td:eq(7) input').eq(1).is(':checked'),
                qeyd  = $(this).find('td:eq(7) textarea'),
                vahidin_deyeri = $(this).find('td:eq(5) input');

            if(check && _.isEmpty(qeyd.val()))
            {
                sehv_var = true;
                qeyd.css('border','1px dashed red');
            }
            else
            {
                qeyd.css('border', '');
            }

            if(vahidin_deyeri.val() == 0)
            {
                sehv_var = true;
                vahidin_deyeri.css('border','1px dashed red');
            }
            else
            {
                vahidin_deyeri.css('border', '');
            }

            MallarObj.push({ 'id': $(this).attr('malin_id'),
                         'mebleq': $(this).find('td:eq(5) input').val(),
                         'gun'   : $(this).find('td:eq(7) input').eq(0).val(),
                         'qeyd'  : check ? $(this).find('td:eq(7) textarea').val() : ''
            });
        });

        if(!sehv_var)
        {
            $.post('prodoc/ajax/testiqleme/testiq_et_daxili.php',
            {
                "id": testiq_id,
                "mallarObj": JSON.stringify(MallarObj)
            },
            function() {
                $('#senedler-tbody').find('tr.selected td:first').trigger('click');
                $('.bagla').trigger('click');
            });
        }
    });
    
    function calculQuantity(path) {
        var miqdar = path.parents('tr').find('td').eq(4).text(),
            deyer  = path.val();

        if(!_.isEmpty(deyer))
        {
            path.parents('tr').find('td:eq(6) input').val(deyer * miqdar);
        }
        else
        {
            path.parents('tr').find('td:eq(6) input').val('');
        }

        calculSum();
    }

    function oldSum() {
        $('.mallar tr').each(function () {
            var miqdar = $(this).find('td:eq(5) input').val(),
                deyer  = $(this).find('td:eq(4)').text(),
                sebeb  = $(this).find('td:eq(7) textarea').val();

            $(this).find('td:eq(6) input').val(deyer * miqdar);

            if(!_.isEmpty(sebeb)) {
                $(this).find('td:eq(7) textarea').css("visibility","visible").prop('disabled', true);
            }
        });

        calculSum();
    }

    function calculSum() {
        var sum  = 0,
            cemi = 0;
        $('.mallar tr').each(function () {
            cemi = parseInt($(this).find('td:eq(6) input').val());

            if(!_.isNaN(cemi))
            {
                sum = sum + cemi;
            }
        });

       $('.cemi').find('label').eq(0).text(sum);
    }

</script>