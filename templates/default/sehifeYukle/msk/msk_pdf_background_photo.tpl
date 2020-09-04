<link rel="stylesheet" type="text/css" href="assets/plugins/dropzon/dropzone.css">
<script type="text/javascript" src="assets/plugins/dropzon/dropzone.js"></script>
<style>
    .side_body {
        position: relative;
        top: -17px;
    }

    .side_body_nav {
        display: flex;
        justify-content: space-between;
        background-color: #dedcdc;
        color:black;
        margin-bottom: 0;
    }

    .side_body_nav li {
        border-bottom: 4px solid transparent;
        flex: 1;
    }

    .side_body_nav li a span {
        background: white;
        color: black !important;
        font-weight: bold;
    }

    .side_body_nav li:hover,.side_body_nav li.active {
        border-bottom: 4px solid #1c8f5f;
        flex: 1;
    }

    .side_body_nav li.active a,.side_body_nav li.active a:hover,.side_body_nav li.active a:link,.side_body_nav li.active a:active,.side_body_nav li.active a:visited ,
    .side_body_nav li a,.side_body_nav li a:hover,.side_body_nav li a:link,.side_body_nav li a:active,.side_body_nav li a:visited {
        color: black;
        text-align: center;
        background: transparent;
        font-weight: bold;
        font-size: 16px;
    }
    .side_body_buttons {
        display: flex;
        align-items: center;
    }

    .side_body_buttons a {
        padding-right: 15px;
        text-decoration: none;
        text-align: center;
        color: black;
    }

    .side_body_buttons .qosma, .side_body_buttons .sened {
        font-size: 13px;
        font-weight: bold;
    }

    .side_body_buttons a i {
        border: 1px solid white;
        color: #1c8f5f;
        border-radius: 50%;
        padding: 15px;
        font-size: 18px;
        background: white;
    }

    .side_body_buttons a.sened i, .side_body_buttons a.qosma i {
        border: none;
        padding: 0;
        font-size: initial;
        background: transparent;
        color: #1c8f5f;
    }

    .side_body_content {
        background: white;
        height: 1500px;
    }

    .side_body_sened img {
        display: block;
        margin: 0 auto;
        padding-bottom: 20px;
    }

    .sened .btn {
        background-color: transparent;
        font-weight: bold;
    }

    .sened li a {
        font-weight: bold;
    }

    .sened i {
        color: #1c8f5f;
        font-size: 16px;
        padding-right: 2px;
    }

    .counters{
        margin:0 0 10px;
    }
    .counters:first-child{
        padding-left:17px;
    }

    .sened button,.sened button:link,.sened button:visited,.sened button:active,.sened button:hover{
        outline:none;
        border:none
    }

    .side_body_content .list-of-files{
        background: white;
        overflow: auto;
        height: 260px;
        padding: 12px;
        position: relative;
        z-index: 99;
        border: 3px solid #ededed;
        word-wrap: break-word;
    }

    .side_body_content .list-of-files::-webkit-scrollbar {
        width: 5px;
    }

    .side_body_content .list-of-files::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    }

    .side_body_content .list-of-files::-webkit-scrollbar-thumb {
        background-color: darkgrey;
        outline: 1px solid slategrey;
    }

    .upIcon{
        position: absolute;
        font-size: 21px;
        color: beige;
        top: -10px;
        left: 86px;
    }

    .side_body_content .list-of-files,.upIcon{
        display: none !important;
    }
    .file{
        margin: 5px 0;
        border-bottom: 1px solid #ededed;
        padding-bottom:5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .file-description{
        width:185px;
    }

    .file-upload-qoshma .upIcon{
        left:90px;
    }

    .imgHolder{
        width:100%;
        margin: 10px auto 0;
        background-size: contain;
        background-position: top;
        background-repeat: no-repeat;
    }
    .imgHolder img{
        display: block;
        width:100%;
        height:100%;
    }

    .active_file .file-name{
        color:#1C8F5F;
        font-weight: bold;
    }
    .file-name,.counters{
        cursor: pointer;
    }

    .dropzone
    {
        border: 2px dashed rgba(0, 0, 0, 0.3);
        min-width:200px;
        margin: 14px;
    }

    .dropdown > .dropdown-menu, .dropdown-toggle > .dropdown-menu, .btn-group > .dropdown-menu
    {
        margin-top: -172px;
    }

</style>

<div class="<?= $tab_right ?> side_body p-0">
    <div class="row">
        <div class="col-md-12">
            <div class="side_body_content">
                <div class="tab senedler_tab">
                    <div class="side_body_buttons">
                        <div class="row">
                            <div class="col-md-6">
                                <form id="fiziki_qeydiyyat_form" class="sened dropdown">
                                    <div id="folder"  data-plugin="dropzone" name="sened_fayl[]" class="dropzone file-upload-size"></div>
                                    <a href="javascript:;" style="margin-left: 13px" class="btn default btn-xs green fiziki_qeydiyyat_btn" ><span class="btn-text"> Əlavə et</span></a>
                                    <a href="javascript:;" style="margin-left: 13px" class="btn default btn-xs red fiziki_qeydiyyat_delete_btn" ><span class="btn-text"> Sil</span></a>
                                </form>
                            </div>
                        </div>
                    </div>
                <div class="tab natamam_tab" style="display: none">
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="assets/plugins/unitegallery/js/unitegallery.js"></script>
<link rel="stylesheet" href="assets/plugins/unitegallery/css/unite-gallery.css" type="text/css"/>
<script type="text/javascript" src="assets/plugins/unitegallery/themes/tiles/ug-theme-tiles.js"></script>
    <script src="prodoc\app.js"></script>

<script>

    var fiziki_qeydiyyat_form = "#fiziki_qeydiyyat_form";

    $('.fiziki_qeydiyyat_btn').on('click',function () {

        var fd = new FormData($(fiziki_qeydiyyat_form).get(0));
        getFilesDropzone(fd, 'sened_fayl[]');


        Component.Form.send({
            url: 'prodoc/ajax/fileUpload/save_pdf_photo.php',
            form: fd,
            success: function (res) {
                res = JSON.parse(res);
                if (res.status == "error") {
                    showErrorsWithSwals(res.errors);
                } else {
                    $("#fiziki_errors_list").slideUp();
                    toastr.success('Şəkil uğurla yadda saxlanıldı!');

                }
            }
        });
    })
    $('.fiziki_qeydiyyat_delete_btn').on('click',function () {

        $.confirm({
            title: 'Sorğu',
            content: 'Silmək istədiyinizdən əminsiniz?',
            buttons: {
                formSubmit : {
                    text:'Bəli',
                    btnClass:'btn-green',
                    action:function(){
                        $.post('prodoc/ajax/fileUpload/remove_pdf_photo.php', {'sil':1}, function(result){
                            result = JSON.parse(result);
                            if(result.status == "success")
                            {
                                $("#remove").css("display", "none");
                            }

                            else if(result.status == "error") errorModal(result.errorMsg, 3000, true);
                            else errorModal('Xəta!', 2000, true);
                        });
                    }
                },
                formCancel:{
                    text:'Xeyr',
                    btnClass:'btn-red',
                    action: function () { },
                }
            }
        });
    })

    $(".side_body_nav").on("click",">li",function () {
        $(".side_body_nav>li").removeClass("active");
        $(this).addClass("active")
        var tab = $(this).find('a').data('tab');
        $(".side_body_content").find(".tab").hide();
        $("." + tab).show()
    })

    var nameFiles = '';

    Dropzone.options.folder = {
        dictDefaultMessage: "Şəkil əlavə edin.",
        init: function () {
            this.on("addedfile", function (file) {
                countFiles(nameFiles);
            });
            this.on("removedfile", function (file) {
                countFiles("sened_fayl[]");
                countFiles("qoshma_fayl[]");
                $('.sened_sayi_img>img').attr('src', '');
                $('.qosma_sayi_img>img').attr('src', '');
            });
        }
    };

    $('.file-upload-size').on('click', function(e) {

        var files = $(this).get(0).dropzone.getAcceptedFiles();
        nameFiles = $(this).attr('name');

        if (0 === files.length) {
            return;
        }

        var index = $(e.target).closest('.dz-preview').index() - 1;

        if (!files[index]) {
            return;
        }

        var file = files[index].name;
        var ind = file.lastIndexOf(".");
        var ext = file.substr(ind + 1);
        var exts = ['png', 'jpg', 'jpeg', 'gif', 'rar'];

        if (exts.includes(ext)) {
            getBase64(files[index], $(this));
        }
    });

    function getBase64(file, item) {
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {

            $("#ui_gallery_images").find(".imgHolder").remove();
            $("#ui_gallery_images").html('<img data-image="" src="" alt="" class="imgHolder">');

            if (item.attr('name') === 'sened_fayl[]') {
                name = '.file-upload';
            }

            item.parents("div").eq(5).find('.row:eq(2)').find(name).find(".imgHolder")
                .attr('src', reader.result)
                .attr('data-image', reader.result)
            ;

            $("#ui_gallery_images").unitegallery(
                {
                    tile_enable_shadow: true,
                    tile_link_newpage: false,
                    tile_shadow_color: "#DDDDDD",
                    tile_show_link_icon: true,
                    tile_image_effect_reverse: true,
                    tiles_space_between_cols: 20
                });
        };
        reader.onerror = function (error) {
            console.log('Error: ', error);
        };
    }

    function countFiles(name) {
        let nameFayl = (name == "sened_fayl[]" ? 'sened_sayi' : 'qosma_sayi');

        $('[data-count="' + nameFayl + '"]').html($('[name="' + name + '"]').find('.dz-preview').length);
    }

    Component.Plugin.PluginManager.init($('.side_body_content'));
</script>