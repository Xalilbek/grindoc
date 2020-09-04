<?php
//look for image file
$image_file_exist = file_exists(UPLOADS_DIR_PATH.'logos/'.getProjectName().'.png')

?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css" rel="stylesheet" media="screen">

<div class="fileinput fileinput-new" data-provides="fileinput">
    <div>
        <span class="fileinput-filename">

        </span>
        <br>
        <span class="btn btn-outline-secondary btn-file">
            <span class="btn btn-circle btn-secondary grey fileinput-new" >Şəkil seç</span>
            <span class="btn btn-circle btn-secondary grey fileinput-exists deyish">Dəyiş</span>
            <input type="file" id="file_name" name="file_name" >
        </span>
        <a href="#" class="btn btn-circle red btn-outline-secondary " data-dismiss="fileinput" id="remove" >Sil</a>
        <a class="btn btn-circle green save fileinput-exists" data-balloon="Yadda saxla" data-balloon-pos="left">Yadda saxla</a>
        <hr>
        <span>Şəklin ölçüləri eni(width) 658px, hündürlüyü(height) 456px və uzantısı png olmalıdır.</span>
    </div>
</div>

<script type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.js"></script>
<script>
    $(document).ready(function(){

        // get image actual name
        var imageExist = '<?php echo $image_file_exist; ?>';
        var image = "<?php  echo $image_file_exist ? getProjectName().'.png': '' ?>"; // get image name if exist
        $(".fileinput-filename").html(image);

        $('#file_name').on('change',function(){
            $("#remove").css("display", "none");
             $(".save").css("display", "inline-block");
        })

        if (!imageExist || imageExist === ""){
            $("#remove").css("display", "none");
        }

        $('.save').click(function () {
            var formData = new FormData();
            var loginPageImg = ($('[name=file_name]').val() != "" ? $('[name=file_name]')[0].files[0]  : "")
            formData.append('loginPageImg[]',loginPageImg)
            $.ajax({
                url: "prodoc/ajax/fileUpload/login_page.php",
                type: 'POST',
                data : formData,
                processData: false,
                contentType: false,
                success: function(result){
                    var result = JSON.parse(result);
                    if(result.status == "success"){
                        toastr.success('Şəkil uğurla yadda saxlanıldı!');
                        $("#remove").css("display", "inline-block");
                        $(".save").css("display", "none");
                    }else{
                        errorModal(result.errors,2000);
                    }
                }
            })
        })

        $('#remove').click(function ()
        {
            $.post('prodoc/ajax/fileUpload/login_page.php', {'sil':1}, function(result){
                result = JSON.parse(result);
                if(result.status == "success")
                {
                    toastr.error('Şəkil silindi!');
                    $(".save").css("display", "none");
                }
                else if(result.status == "error") errorModal(result.errorMsg, 3000, true);
                else errorModal('Xəta!', 2000, true);
            });

        })
    })


</script>