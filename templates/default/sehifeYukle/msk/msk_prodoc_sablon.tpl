<div class="tab-pane" id="tab_28">
    <div class="row">
        <div class="col-md-9">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="word_shablonlat_table">
                    <thead>
                    <tr>
                        <th style="width: 30px;">№</th>
                        <th>Word - Başlıq</th>
                        <th>Şöbə</th>
                        <th>Əməkdaş</th>
                        <th>Şablonlar</th>
                        <th></th>
                        <th style="text-align:right;"><a did="0" href="javascript:;" class="btn default btn-xs green" id="word_shablon_add"><i class="icon-plus"></i> Əlavə et</a></th>
                    </tr>
                    </thead>
                    <tbody>
                        $contactTypes$
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/scripts/tippy.all.min.js"></script>
<script type="text/javascript">
    ////// Word - shablonlar //////

    $("#word_shablonlat_table").on('click', 'a[did]', function(){
        selected_form = $(this);
        var doc_id = $(this).attr('did');
        var name = $(this).closest('tr').find('td').eq(1).text();
        templateYukle("prodoc_word_document_shablon_add_edit", '', {"doc_id": doc_id}, 70, 99999);
    });

    $('#word_shablonlat_table>tbody').on("click",".red",function()
    {
        var mn2 = modal_yarat("$9956eminsinizhead$","<p style='padding-left: 20px;'>$9956eminsinizdesc$</p>","<button class='btn btn-danger testiqle' data-dismiss='modal'> $9956ok$</button> <button class='btn default cancel' data-dismiss='modal'>$9956cancel1$</button>","btn-danger","");

        var t = $(this),
            idsi = t.parent('td').parent('tr').attr("tr_id");
        //$('#basic').attr('idsi', idsi);
        $('#msk_delete').click();

        $("#bosh_modal"+mn2+" button.testiqle").unbind("click").click(function()
        {
            //var idsi = $('#basic').attr('idsi');
            $.post("includes/msk/word_document.php",
                    {'doc_id':idsi, action: 'delete'},
                function(result)
                {
                    $('#basic .default').click();
                    t.parent('td').parent('tr').remove();
                    if(!$('#word_shablonlat_table>tbody').children('tr').children('td').length)
                    {
                        $('#word_shablonlat_table>tbody').html("<tr time='null'><td colspan='100%'>Boşdur!</td></tr>");
                    }
                });
        });
    });
</script>