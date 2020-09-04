<div class="modal-body form">
    <form class="form-horizontal form-bordered form-row-stripped">
        <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-4">Təşkilat:</label>
                <div class="col-md-6">
                    <input class="form-control" vezife="teshkilat" placeholder="Təşkilatı seçin:" data-plugin="select2-ajax" data-plugin-params='{"queryString": {"ne": "gonderen_teshkilatlar"}}' >
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Qurumun adı:</label>
                <div class="col-md-6">
                    <input class="form-control" vezife="ad" placeholder="Qurumun adı">
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <button type="button" class="btn green" vezife="testiqle"><i class="fa fa-check"></i> Əlavə et</button>
        <button type="button" class="btn default" data-dismiss="modal"><i class="fa fa-close"></i> Bağla</button>
    </div>
</div>
<script>

    $(function () {
        var bosh_modal_element = $(bosh_modal);
        Component.Plugin.PluginManager.init(bosh_modal_element);

        $(bosh_modal + " button[vezife='testiqle']").click(function()
        {
            var baqlaBtn=$(this).next("button"),
                ad = $(bosh_modal + " input[vezife='ad']").val().trim(),
                cid = $(bosh_modal + " input[vezife='teshkilat']").val();
            if((cid>0)==false)
            {
                $(bosh_modal + " input[vezife='teshkilat']").prev("div").attr("border","1px dotted red");
                return;
            }
            if(ad!="")
            {
                sehifeLoading(1);
                $.post(proBundle + "includes/umumi/teshkilat_qurum_add.php", {'ad':ad,'cid':cid}, function(netice)
                {
                    sehifeLoading(0);
                    try
                    {
                        netice = JSON.parse(netice);
                        if(netice['status']=="hazir")
                        {
//                        var cidYeni = netice['cid'];
//                        $("#gonderen_teshkilat_qurum215").select2("data",{'id':cidYeni,'text':ad});
//                        $("#qeydiyyat select[vezife='gonderen_teshkilat']").select2("val",cid);
                            toastr['success']('Qurum əlavə edildi!');
                            $(bosh_modal).modal('hide');
                        }
                        else
                        {
                            errorModal(netice['sehv'],5000,true);
                        }
                    }
                    catch(e)
                    {

                    }

                });
            }
            else
            {
                errorModal("Başlığı daxil etmədiz!",3000,true);
            }
        });
    })

</script>