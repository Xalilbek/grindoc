
<div class="profile-content">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title tabbable-line">
                    <div class="caption caption-md">
                        <i class="icon-globe theme-font hide"></i>
                        <span class="caption-subject font-blue-madison bold uppercase">ACOUNT DATA</span>
                    </div>
                    <div class="actions">
                        <a id="add" href="javascript:;" class="btn btn-circle btn-default disabled"  data-toggle="tooltip" data-placement="left" title="save">
                            <i class="fa fa-save"></i>
                        </a>
                    </div>
                </div>
                <div class="portlet-body" style="overflow: hidden;">
                    <form role="form" action="#">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Rollar</label>
                                <?php
                                $privler = '';
                                if (isset($privs)){
                                    $privler.=" <select class=\"form-control privs js-states form-control\" name=\"priv\" id=\"privs\">";
                                    foreach ($privs as $privilegiya){
                                        $selected = $prodocGroupId == $privilegiya['id'] ? 'selected' : '';
                                        $privler.="<option ".$selected." value=\"".$privilegiya['id']."\">".htmlspecialchars($privilegiya['name'])."</option>";
                                    }
                                    $privler.="</select>";
                                }
                                print $privler;
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(".privs").select2({
        placeholder: "Rollar",
        allowClear: true
    });

    $('#add').on('click', function(){
        let rol  = $('select[name="priv"]').val();
        sehv_var = false;
        if (rol === "") {
            sehv_var = true;
            $('select[name="priv"]').prev('div').css('cssText','border : 1px dashed red !important')
        } else {
            $('select[name="priv"]').prev('div').css('cssText','border : ')
        }

        if (sehv_var) return;

        var form = new FormData();
        form.append("priv", rol);
        form.append("user_id", profileId);
        $.ajax({
            url: 'ajax/general/profile/sened_dovriyyesi_privilegiya.php',
            data: form,
            type: 'POST',
            contentType: false,
            processData: false,
        }).done(function(response)
        {
            sehifeLoading(0);
            response = JSON.parse(response);
            if (response['status'] == 'success')
            {
                toastr["success"]('Yadda saxlanıldı');
                $('#add').switchClass('red-sunglo', 'disabled', 350);
                $('#add').css('color', '#666');
            }
            else if(response['status'] == 'error')
            {
                errorModal(response['errorMsg'],2000,true);
            }
            else{
                errorModal(saveError);
            }
        });

    });

    $("input:file").change(function (){
        activateBtn();
    });


</script>