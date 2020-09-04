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
</style>

<div class="modal-body" id="bm<?=$MN;?>">
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
            <tr malin_id="<?php print $doc['id']; ?>" class="old">
                <td><?php print $j; ?></td>
                <td><?php print htmlspecialchars($doc['malin_kodu']); ?></td>
                <td style="width: 150px"><?php print htmlspecialchars($doc['mal_adi']); ?></td>
                <td><?php print htmlspecialchars($doc['olcu_vahidi']); ?></td>
                <td><input type="text" class="spinner-input form-control" value="<?php print htmlspecialchars($doc['miqdar']); ?>" disabled></td>
                <td style="width: 100px">
                    <input type="text" class="spinner-input form-control mebleq" value="<?php print htmlspecialchars($doc['mebleq']); ?>" disabled>
                </td>
                <td style="width: 100px">
                    <input type="text" class="spinner-input form-control mebleq_cemi" disabled>
                </td>
                <td>
                    <input type="text" style="width: 120px; display: inherit !important;" class="spinner-input form-control gun" value="<?php print htmlspecialchars($doc['gun']); ?>" disabled>
                </td>
                <td style="width: 150px;">
                    <?php $i = 0; foreach ($fileName as $value): $i++; ?>
                        <?php if($doc['id'] == $value['module_entry_id'] &&  $value['module_name'] == 'satinAlma_fayl'): ?>
                            <a href="<?php print PRODOC_FILES_WEB_PATH.$value['file_actual_name']; ?>" download>
                                <?php print htmlspecialchars($value['file_original_name']); ?></a>
                            <br>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr malin_id="<?php print $doc['id']; ?>" class="new">
                <input type="hidden" value="<?php print $doc['id']; ?>" name="malin_id[]">
                <td><?php print $j; ?></td>
                <td><?php print htmlspecialchars($doc['malin_kodu']); ?></td>
                <td style="width: 150px"><?php print htmlspecialchars($doc['mal_adi']); ?></td>
                <td><?php print htmlspecialchars($doc['olcu_vahidi']); ?></td>
                <td><input type="text" class="spinner-input form-control miqdar" name="miqdar[]" value="<?php print htmlspecialchars($doc['netice_miqdar']); ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="12" disabled></td>
                <td style="width: 100px">
                    <input type="text" class="spinner-input form-control mebleq_necite" value="<?php print htmlspecialchars($doc['mebleq']); ?>" disabled>
                </td>
                <td style="width: 100px">
                    <input type="text" class="spinner-input form-control mebleq_cemi" disabled>
                </td>
                <td>
                    <input type="text" style="width: 120px; display: inherit !important;" class="spinner-input form-control gun" value="<?php print htmlspecialchars($doc['gun']); ?>" disabled>
                </td>
                <td style="width: 170px;">
                    <?php $i = 0; foreach ($fileName as $value): $i++; ?>
                        <?php if($doc['id'] == $value['module_entry_id'] &&  $value['module_name'] == 'satinAlma_netice'): ?>
                            <div class="file">
                                <a href="<?php print PRODOC_FILES_WEB_PATH.$value['file_actual_name']; ?>" download><?php print htmlspecialchars($value['file_original_name']); ?></a>
                                <i style="cursor: pointer; display: none;" data-id="<?= $value['id'] ?>" class="fa fa-trash text-danger remove"></i>
                            </div>
                            <br>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <div class="file-upload" data-name-pattern="document_%s[]" style="display: none;">
                        <div class="add-file-btn">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                            <button type="button" class="btn btn-link font-dark" style="padding: 6px;">
                                <span style="font-weight: 500;">Sənəd əlavə et</span>
                            </button>
                        </div>
                        <div class="list-of-files">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Şərh</td>
                <td colspan="8"><input type="text" class="spinner-input form-control serh" name="serh[]" value="<?php print htmlspecialchars($doc['netice_serh']); ?>" disabled></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="cemi">
        <label></label>
        <label for="">Cəmi:</label>

        <label style="color: #1BBC9B; margin-right: 12px;"></label>
        <label for="" style="color: #1BBC9B;">Cəmi:</label>
    </div>
    <div class="tam_tehvil" style="display: none">
        <label for="">Tam təhvil</label>
        <input type="checkbox" data-plugin="uniform" name="tam_tehvil">
    </div>
    <input type="hidden" name="id" value="<?=$testiq_id?>">
</div>

<div class="modal-footer" style="border: 0;">
    <div style="float: right;">
        <button type="button" class="btn btn-outline btn-circle green-meadow testiqle" style="display: none;">
            Qismən həll olunub
        </button>
        <button type="button" class="btn btn-outline btn-circle green-meadow tehvil_al" style="display: none;">
            Təhvil al
        </button>
        <button type="button" class="btn btn-outline btn-circle red-intense imtina" style="display: none;">
            İmtina
        </button>
        <button type="button" data-dismiss="modal" class="btn btn-outline btn-circle grey-cascade bagla">
            Bağla
        </button>
    </div>
</div>

<script src="prodoc/asset/widget/fileUpload.js"></script>
<script type="text/javascript">

    $(function() {
        oldSum();
        $("input[type='checkbox']").uniform();

        $('.mallar tr.new').each(function () {
           $(this).find('.file-upload').fileUpload({
                name: 'will_be_set'
            });
        });
        
        var edit = <?php echo $edit; ?>;
        var testiq_imtina = <?php echo $testiq_imtina; ?>;

        if(edit)
        {
            $('.file i').show();
            $('.testiqle').show();
            $('.cemi').show();
            $('.miqdar').removeAttr('disabled');
            $('.tam_tehvil').show();
            $('.mallar tr.new').each(function () {
                $(this).find('td:eq(8)').find('.file-upload').show();
                $(this).next().find('input').removeAttr('disabled');
            });
        }

        $(".mallar").on('click', '.remove', function() {
            var id = $(this).data('id'),
                td = $(this).closest('div.file');

            var mn2 = modal_yarat(
                "Əminsiniz?",
                "<p style='padding-left: 20px;'>Silmək istədiyinizə əminsiniz?</p>",
                "<button class='btn btn-danger testiqleFayl' data-dismiss='modal'> Bəli</button> <button class='btn default cancel' data-dismiss='modal'>Xeyir</button>",
                "btn-danger",
                "",
                true
            );

            $("#bosh_modal"+mn2+" button.testiqleFayl").unbind("click").click(function()
            {
                $.post('prodoc/ajax/faylSil.php', {
                    'edit'  : +edit,
                    'fileID': id
                }, function () {
                    td.remove();
                });
            });
        });

        if(testiq_imtina)
        {
            $('.imtina').show();
            $('.tehvil_al').show();
        }

        $('.imtina').click(function () {
            $("button[vezife='imtina']").trigger('click');
            $('.bagla').trigger('click');
        });

        $('.tehvil_al').click(function () {
            $("button[vezife='testiqle']").trigger('click');
            $('.bagla').trigger('click');
        });

        $('.miqdar').on('input', function () {
            calculQuantity($(this));
        });

        $('[name="tam_tehvil"]').click(function () {
           var param = $(this).is(':checked'),
               val   = param ? 'Təhvil ver' : 'Qismən həll olunub';

           $('.testiqle').text(val);
        });
    });

    function setFileInputIndexes()
    {
        var index = 0;
        $(".mallar").find('tr.new').each(function() {
            $(this).find('.list-of-files .file input[type="file"]').each(function() {
                var namePattern = $(this).closest('[data-name-pattern]').attr('data-name-pattern');

                $(this).attr('name', s.sprintf(namePattern, index));
            });
            index++;
        });
    }

    $('.testiqle').click(function () {
        var bm = $("#bm"+<?= $MN ?>);
        setFileInputIndexes();

        Component.Form.send({
            form: bm,
            url: 'prodoc/ajax/testiqleme/testiq_et_daxili.php',
            sendUncheckedCheckbox: true,
            checkboxCheckedValue: 1,
            checkboxUncheckedValue: 0,
            useDefaultFormData: false,
            success: function (res) {
                res = JSON.parse(res);
                if (res.status === "error") {
                } else {
                    $('#senedler-tbody').find('tr.selected td:first').trigger('click');
                    $('.bagla').trigger('click');
                }
            }
        });
    });

    function calculQuantity(path) {
        var miqdar = path.parents('tr').find('td:eq(5) input').val(),
            deyer  = path.val();

        if(!_.isEmpty(deyer))
        {
            path.parents('tr').find('td:eq(6) input').val(miqdar * deyer);
        }
        else
        {
            path.parents('tr').find('td:eq(6) input').val('');
        }

        calculSum('.new', 2);
    }
    
    function oldSum() {
        $('.mallar tr').each(function () {
            var miqdar = $(this).find('td:eq(5) input').val(),
                deyer  = $(this).find('td:eq(4) input').val();

            $(this).find('td:eq(6) input').val(deyer * miqdar);
        });

        calculSum('.old', 0);
        calculSum('.new', 2);
    }

    function calculSum(attr_name, eq) {
        var sum  = 0,
            cemi = 0;
        $('.mallar tr'+attr_name).each(function () {
            cemi = parseInt($(this).find('td:eq(6) input').val());

            if(!_.isNaN(cemi))
            {
                sum = sum + cemi;
            }
        });

        $('.cemi').find('label').eq(eq).text(sum);
    }
</script>