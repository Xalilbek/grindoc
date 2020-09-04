<div class="modal-body form">
    <form class="form-horizontal form-bordered ">
        <div class="form-body" style="padding-left: 5px; padding-right: 5px">

            <div class="form-group">
                <label class="control-label col-md-2">Format:</label>
                <div class="col-md-3">
                    <select class="form-control select2me" id="format" placeholder="Format">
                        <option value='a4'>A4</option>
                        <option value='a5'>A5</option>
                    </select>
                </div>
                <label class="control-label col-md-3">Şablon:</label>
                <div class="col-md-3">
                    <select class="form-control" id="shablon" placeholder="Şablon">
                        <option></option>
                        <?= $shablonlar ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-12" style="width: 100%; padding: 0px; margin: 0px" >
                    <textarea id="ckeditor" name="editor1" style="height: 500px;"></textarea>
                </div>
            </div>

        </div>
    </form>
</div>
<div class="modal-footer" style="text-align: left;border-top: 0;">

</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <button class="btn green"><i class="fa fa-save"></i> Yadda saxla</button>
        <button class="btn default" data-dismiss="modal"><i class="fa fa-close"></i> Bağla</button>
    </div>
</div>
<script src="assets/plugins/tinymce/tinymce.min.js"></script>

<script>

    $("body").children(".mce-menu,.mce-window,.mce-widget,.mce-tooltip,.mce-menu-item,.mce-disabled").remove();
    $(document).ready(function()
    {
        tinymce.init({
            selector: "#ckeditor",
            theme: "modern",
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak lineheight",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern"
            ],
            toolbar1: "insertfile undo redo | styleselect | bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            toolbar2: "print preview media | forecolor backcolor | fontselect | fontsizeselect | lineheightselect",
            image_advtab: true,
            readonly: <?= $readonly ?>,
            contextmenu: "link image inserttable | cell row column deletetable paste",
            language: 'az',
            fullscreen_new_window : true,

            paste_word_valid_elements: "b,strong,i,em,h1,h2,u,p,ol,ul,li,a[href],span,color,font-size,font-color,font-family,mark,table,tr,td",
            paste_retain_style_properties: "all",

            fontsize_formats: "8pt 9pt 10pt 11pt 12pt 14pt 18pt 24pt 30pt 36pt 48pt 72pt",

            init_instance_callback: function (editor) {

                editor.on('ExecCommand', function (e) {
                    if (e.command === 'mcePrint') {
                        document.title = "Forma";
                    }
                });

                editor.on('FullscreenStateChanged', function(e) {
                    if (e.state)
                    {
                        $(bosh_modal + " .modal-dialog").css({
                            width: "100%",
                            marginTop: "0px"
                        });
                    }
                    else
                    {
                        $(bosh_modal + " #format").trigger('change');
                        $(bosh_modal + " .modal-dialog").css({
                            marginTop: "30px"
                        });
                    }
                });

            }
            // plugins: 'lineheight',
            // toolbar: 'lineheightselect'
        });

        tinymce.addI18n('az', {
            "Line Height": "Sətrin hündürlüyü"
        });

    });

    $(bosh_modal + " .green").click(function()
    {
        var html = tinymce.get('ckeditor').getContent();
        var page_size = $("#format").val();
        var page_size_text = $("#format option:selected").text();

        sehifeLoading(1);
        $.post(proBundle + "includes/msk/teyinat_document_add_edit.php",
            {'html':html,'doc_id':'<?= $doc_id ?>', "page_size": page_size},
            function(netice)
            {
                $(bosh_modal + "").modal("hide");
                toastr['success']("Yadda saxlandı!");

                if ('<?= $doc_id ?>' === '0')
                {
                    // yeni forma
                    doc_ids.push(netice);
                }
                else
                {
                    netice = JSON.parse(netice);
                    var versiyanin_idsi = netice[1];
                    var netice = netice[0];

                    if (doc_ids !== undefined)
                    {
                        doc_ids.push(versiyanin_idsi);
                    }
                }

                // var selected_teyinat = $("#teyinat").val();
                // qurum yoxsa erizechi?
                // if (selected_teyinat == "2")
                // {
                // 	docs[selected_teyinat][teyinat_qurum_erize] = netice;
                // }
                // else
                // {
                // 	docs[selected_teyinat] = netice;
                // }
                // $("#teyinat").trigger('change');
                // docs[netice] =
                if (selected_form.hasClass('edit'))
                {
                    selected_form.prev().text("Forma" + " (" + page_size_text + ")");
                }
                else
                {
                    selected_form.attr('did', netice);
                    selected_form.text("Forma" + " (" + page_size_text + ")");
                }

                if (selected_form.closest('td').length)
                {
                    selected_form.closest('td').find('.upl').show();
                }
                else
                {
                    selected_form.closest('div').find('.upl').show();
                }
            }
        );

        sehifeLoading(0);
    });

    $(bosh_modal + ' select').select2();
    $(bosh_modal + ' #shablon').select2({allowClear: true});

    $(bosh_modal + " #format").change(function(){
        var modal_dialog = $(bosh_modal + ' .modal-dialog');

        var width = "21cm";
        var height = "29.7cm";
        switch ($(this).val())
        {
            case "a4":
                width = "21cm";
                height = "29.7cm";
                break;
            case "a5":
                width = "14.8cm";
                height = "21cm";
                break;
        }

        modal_dialog.animate({"width": width}, 200, 'linear'); // css(, width);
        // modal_dialog.animate({"height": height}); // css(, width);
        // modal_dialog.css("height", height);
    });

    // Update
    if (Number("<?= $doc_id ?>") > 0)
    {
        $("#format").select2("val", "$page_size$");
        $("#ckeditor").html("$content$");
    }
    $(bosh_modal + " #format").trigger('change');
    $(bosh_modal + " .modal-title").text("Forma");


    if ('<?= $readonly ?>' == '1')
    {
        $(bosh_modal + " #format").prop('disabled', true);
        $(bosh_modal + ' .green').remove();
    }

    $("#shablon").on('change', function(){
        var shablon = $(this).val();

        if (!$.isNumeric(shablon)) {
            tinymce.get('ckeditor').setContent("");
            return;
        }

        sehifeLoading(1);
        $.post(proBundle + "includes/msk/teyinat_document_add_edit.php", {'doc_id': shablon, 'action': 'get'}, function(shablon_info){
            shablon_info = JSON.parse(shablon_info);
            $(bosh_modal + " #format").val(shablon_info.page_size).trigger('change');
            tinymce.get('ckeditor').setContent(shablon_info.content);
            sehifeLoading(0);
        });
    });

</script>