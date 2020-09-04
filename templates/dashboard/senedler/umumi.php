<!--suppress ALL -->
<style>
    .gallery-form .control-label {
        padding-top: 2px;
        top: -8px;
        font-weight: bold;
        color: #004080;
        font-size: 18px;
        position: relative;
    }
    .gallery-form .badge {
        top: -11px;
        position: relative;
    }
</style>
<div class="form gallery-form">
    <form class="form-horizontal form-bordered form-row-stripped">
        <div class="form-body">
            <div class="form-group">
                <div class="header-fail-container col-md-12">
                    <div class="form-froup col-md-10">
                        <span class="badge count badge-success all_count_senedler" style=""></span>
                        <label class="control-label"><?= dsAlt('2616senedler', "SƏNƏDLƏR")?> </label>
                    </div>
                    <div class="col-md-2">
                        <i class="fa fa-chevron-up" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="center-fail-container">
                    <div class="col-md-12" buraYazSenedler><?=  $senedlerHtml ?></div>
                    <div class="col-md-12" id="gallery"><?= $shekillerHtmlSened ?></div>
                </div>
            </div>

            <div class="form-group">
                <div class="header-fail-container col-md-12">

                    <div class="form-froup col-md-10">
                        <span class="badge count badge-success all_count_qoshmalar" style=""></span>
                        <label class="control-label"><?= dsAlt('2616_senedler_qoshma', "QOŞMALAR")?> </label>
                    </div>
                    <div class="col-md-2">
                        <i class="fa fa-chevron-up" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="center-fail-container">
                    <div class="col-md-12" buraYazQoshma><?=  $qoshmaHtml ?></div>
                    <div class="col-md-12" id="galleryQoshma"><?= $shekillerHtmlQoshma ?></div>
                </div>
            </div>

            <div class="form-group">
                <div class="header-fail-container col-md-12">

                    <div class="form-froup col-md-10">
                        <span class="badge count badge-success all_count_ixrac_formalari" style=""></span>
                        <label class="control-label"><?= dsAlt('2616_senedler_icrac', "İXRAC FORMASI")?> </label>
                    </div>
                    <div class="col-md-2">
                        <i class="fa fa-chevron-up" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="center-fail-container">
                    <div class="col-md-12" buraYazIxracFormasi>
                        <?php
                        require_once DIRNAME_INDEX . 'prodoc/includes/fileExportForm.php';
                        ?>
                        </div>
                </div>
            </div>

            <div class="form-group">
                <div class="header-fail-container col-md-12">

                    <div class="form-froup col-md-10">
                        <span class="badge count badge-success all_count_esas_sened" style=""></span>
                        <label class="control-label"><?= dsAlt('2616_senedler_esas', "ƏSAS SƏNƏD")?></label>
                    </div>
                    <div class="col-md-2">
                        <i class="fa fa-chevron-up" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="center-fail-container">
                    <div class="col-md-12" buraYaz buraYazEsasSenedler><?= $esasSenedlerHtml ?></div>
                    <div class="col-md-12" id="galleryBasic"><?= $esasShekillerHtml ?></div>
                </div>
            </div>

            <div class="form-group">
                <div class="header-fail-container col-md-12">
                    <div class="form-froup col-md-10">
                        <span class="badge count badge-success all_count_serhle_bagla" style=""></span>
                        <label class="control-label" ><?= dsAlt('2616_senedler_sherhle', "ŞƏRHLƏ BAĞLA")?></label>
                    </div>
                    <div class="col-md-2">
                        <i class="fa fa-chevron-up" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="center-fail-container" >
                    <div class="col-md-12" buraYaz SerhleBagla ><?= $iseTikHtml ?></div>
                    <div class="col-md-12" id="galleryIsetik"><?= $shekillerHtmlIsetik ?></div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="assets/plugins/unitegallery/js/unitegallery.js"></script>
<link rel="stylesheet" href="assets/plugins/unitegallery/css/unite-gallery.css" type="text/css"/>
<script type="text/javascript" src="assets/plugins/unitegallery/themes/tiles/ug-theme-tiles.js"></script>
<script>

    var docInfo = <?= json_encode($docInfo) ?>;

    $( document ).ready(function() {
        $('.header-fail-container').on('click','.fa-chevron-up',function () {

            if ($(this).parents('.form-group').find('.center-fail-container').hasClass('active')) {

                $(this).parents('.form-group').find('.center-fail-container').hide();
                $(this).parents('.form-group').find('.center-fail-container').removeClass('active');
                $(this).css('transform','');

            } else {

                $(this).parents('.form-group').find('.center-fail-container').addClass('active');
                $(this).parents('.form-group').find('.center-fail-container').show();
                $(this).css('transform','rotate(180deg)');

            }
        })

        setTimeout(function(){
            $('#gallery > div.ug-tiles-wrapper.ug-tiletype-columns.ug-tiles-transit > div').each(function (i) {
                if(<?= +$checkTrash ?>){

                    var key =$(this).find('img').attr('src').split('/')[$(this).find('img').attr('src').split('/').length-1];
                    $(this).append('<i class="fa fa-trash photoTrash" data-toggle="tooltip" data-placement="right" title="Sənədi sil" style="color:red; cursor: pointer; float:right;" ></i>');
                    $(this).find('.photoTrash').on('click',(event)=>{
                        photoTrash(key,$(this));
                        event.stopPropagation();
                    })
                }
            })
            $('#galleryQoshma > div.ug-tiles-wrapper.ug-tiletype-columns.ug-tiles-transit > div').each(function (i) {
                if(<?= +$checkTrash?>){

                    var key =$(this).find('img').attr('src').split('/')[$(this).find('img').attr('src').split('/').length-1];
                    $(this).append('<i class="fa fa-trash photoTrash" data-toggle="tooltip" data-placement="right" title="Sənədi sil" style="color:red; cursor: pointer; float:right;" ></i>');
                    $(this).find('.photoTrash').on('click',(event)=>{

                        photoTrash(key,$(this));
                        event.stopPropagation();
                    })
                }
            })

            $('#galleryBasic > div.ug-tiles-wrapper.ug-tiletype-columns.ug-tiles-transit > div').each(function (i) {
                if(<?= +$checkTrash?>){

                    var key =$(this).find('img').attr('src').split('/')[$(this).find('img').attr('src').split('/').length-1];
                    $(this).append('<i class="fa fa-trash photoTrash" data-toggle="tooltip" data-placement="right" title="Sənədi sil" style="color:red; cursor: pointer; float:right;" ></i>');
                    $(this).find('.photoTrash').on('click',(event)=>{

                        photoTrash(key,$(this));
                        event.stopPropagation();
                    })
                }
            })

            $('#galleryIsetik > div.ug-tiles-wrapper.ug-tiletype-columns.ug-tiles-transit > div').each(function (i) {
                if(<?= +$checkTrash?>){

                    var key =$(this).find('img').attr('src').split('/')[$(this).find('img').attr('src').split('/').length-1];
                    $(this).append('<i class="fa fa-trash photoTrash" data-toggle="tooltip" data-placement="right" title="Sənədi sil" style="color:red; cursor: pointer; float:right;" ></i>');
                    $(this).find('.photoTrash').on('click',(event)=>{

                        photoTrash(key,$(this));
                        event.stopPropagation();
                    })
                }
            })
            $('.center-fail-container').hide()
        }, 500);
        $('#gallery').find('img').each(function () {
            // $(this).parent('div').append('<a>asdasdasdas</a>')
        })
    });

    if($("#galleryQoshma").html()!="") {
        $("#galleryQoshma").unitegallery(
            {
                tile_enable_shadow: true,
                tile_link_newpage: false,
                tile_shadow_color: "#DDDDDD",
                tile_show_link_icon: true,
                tile_image_effect_reverse: true,
                tiles_space_between_cols: 20
            });
    }

    if($("#gallery").html()!="")
    {
        $("#gallery").unitegallery(
            {
                tile_enable_shadow:true,
                tile_link_newpage:false,
                tile_shadow_color:"#DDDDDD",
                tile_show_link_icon:true,
                tile_image_effect_reverse:true,
                tiles_space_between_cols:20
            });


    }

    if($("#galleryIsetik").html()!="") {
        $("#galleryIsetik").unitegallery(
            {
                tile_enable_shadow: true,
                tile_link_newpage: false,
                tile_shadow_color: "#DDDDDD",
                tile_show_link_icon: true,
                tile_image_effect_reverse: true,
                tiles_space_between_cols: 20
            });
    }


    if($("#galleryBasic").html() != "")
    {
        $("#galleryBasic").unitegallery(
            {
                tile_enable_shadow:true,
                tile_link_newpage:false,
                tile_shadow_color:"#DDDDDD",
                tile_show_link_icon:true,
                tile_image_effect_reverse:true,
                tiles_space_between_cols:20
            });
    }



    $('.docTrash').on('click',function () {

        var fileId=$(this).parent('div').find('a').attr('file-id');
        var doc=$(this).parent('div');

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
            $.post('prodoc/ajax/dashboard/senedler/trash_doc.php',{ 'fileId':fileId}, function (result) {
                result=JSON.parse(result);
                if (result.status=='success'){
                    doc.remove();
                    toastr.success('Sənəd silindi');
                }else {
                    toastr.error('Sənəd silinmədi');
                }
            })
        });

    })
    function photoTrash(key,div){

        var mn2 = modal_yarat(
            "Əminsiniz?",
            "<p style='padding-left: 20px;'>Silmək istədiyinizə əminsiniz?</p>",
            "<button class='btn btn-danger testiqFayl' data-dismiss='modal'> Bəli</button> <button class='btn default cancel' data-dismiss='modal'>Xeyir</button>",
            "btn-danger",
            "",
            true
        );


        $("#bosh_modal"+mn2+" button.testiqFayl").unbind("click").click(function()
        {
            docInfo.forEach(function (element) {
                if(element.key==key){
                    $.post('prodoc/ajax/dashboard/senedler/trash_doc.php',{ 'fileId':element.id}, function (result) {
                        result=JSON.parse(result);
                        if (result.status=='success'){
                            div.remove();
                            toastr.success('Sənəd silindi');
                        }else {
                            toastr.error('Sənəd silinmədi');
                        }

                    })

                }
            })
        });

        console.log(key);
    }
    function countDocument(sened,sened2,div){
        var senedler_count_sened  = $(sened + ' div').length;
        var senedler_count_sekil  = $(sened2 + ' img').length;

        $(div).text(senedler_count_sened  + senedler_count_sekil);

        if($(div).html() == 0){
            $(div).parents('.form-group').hide()
        }
    }

    countDocument('.gallery-form [burayazsenedler]','#gallery','.all_count_senedler')
    countDocument('.gallery-form [burayazqoshma]','#galleryQoshma','.all_count_qoshmalar')
    countDocument('.gallery-form [burayazIxracFormasi]','#galleryIxracFormalari','.all_count_ixrac_formalari')
    countDocument('.gallery-form [buraYazEsasSenedler]','#galleryBasic','.all_count_esas_sened')
    countDocument('.gallery-form [SerhleBagla]','#galleryIsetik','.all_count_serhle_bagla')



    // $("ul[aria-labelledby=\"icra_edilmeli_link\"] li .count_container .badge").each(function(index, elem){
    //     var countTabs = $(this).text();
    //     var intcountTabs =  parseInt(countTabs)
    //     a += intcountTabs
    // });
    // $("ul[data-link=\"icra_edilmeli\"] .all_count").text(a)

    $('.photoTrash').on('click',function () {
        console.log($(this).parent('div').find('img'));
    })

    setTimeout(function () {
        $('[data-toggle="tooltip"]').tooltip();
    },1000);
</script>