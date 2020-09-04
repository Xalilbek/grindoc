<style>
    .probtn{
        background: #1C8F5F !important;
        border-color: #1C8F5F !important;
    }
</style>
<div class="modal-body form">
    <form class="form-horizontal">
        <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-4">Başlıq:</label>
                <div class="col-md-6">
                    <input class="form-control" vezife="ad" placeholder="Başlıq">
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <button type="button" class="btn green probtn btn-circle" vezife="testiqle">Əlavə et</button>
        <button type="button" class="btn default btn-circle" data-dismiss="modal">Bağla</button>
    </div>
</div>
<script>
  $(bosh_modal + " button[vezife='testiqle']").click(function()
  {
    var baqlaBtn = $(this).next("button"),
        ad = $(bosh_modal + " input[vezife='ad']").val().trim();
    if(ad!="")
    {
      modal_loading(1,MN);
      $.post(proBundle + "includes/msk/umumi_msk.php", {'menbe_id':'0','menbe':ad}, function(netice)
      {
        modal_loading(0,MN);
        if(netice=="error")
        {
          errorModal("Bu adda mənbə daha öncə əlavə edilib!",3000,true);
        }
        else
        {
          $(bosh_modal).modal("hide");
        }
      });
    }
    else
    {
      errorModal("Başlığı daxil etmədiz!",3000,true);
    }
  });
</script>
