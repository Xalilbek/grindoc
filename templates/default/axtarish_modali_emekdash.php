<style>
    .probtn{
        background: #1C8F5F !important;
        border-color: #1C8F5F !important;
    }
    .scrollbox {
        width: 570px;
        height: 350px;
        padding: -1px 20px;
        font-size: 1.2em;
        line-spacing: 20px;
        line-height: 1.7em;
        overflow: auto;
    }
    .scrollbox div:hover {
        background-color: #1C8F5F !important;
        color: white;
    }
    svg {
        width: 37px;
        margin-left: 90% !important;
        margin-top: -38px !important;
        display: block;
    }
    .title_down{
        height: 3px;
        width: 100%;
        background-color: #1C8F5F;
    }
    .checked{
        display: block !important;
    }
    .checkedd{
        background-color:lavender
    }
    .search{
        width: 94%;
        margin-left: 6%;
    }
    .white polyline{
        stroke: lavender;
    }


</style>
<div class="modal-body form">
    <form class="form-horizontal">
        <div class="form-body">
            <i style="color:dimgrey;margin-top: 10px;font-size: 27px;position: absolute;" class="fa fa-search"></i>
            <div class="form-group">
                <div class="col-md-12">
                    <input class="form-control search"  placeholder="<?= dsAlt('2616qeydiyyat_pencereleri_axtarish', 'Axtarış')?>">
                </div>
            </div>
            <h3><?= dsAlt('2616qeydiyyat_pencereleri_emekdashlari_secin', "Əməkdaşları seçin")?>:</h3>
            <div class="scrollbox">
                <div class="before"></div>
                <div class="after"></div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="color: red; display: inline;" vezife="error"></div>
    <div style="display: inline;">
        <button type="button" class="btn green btn-circle probtn" ><?= dsAlt('2616qeydiyyat_pencereleri_elave', "Əlavə et")?></button>
        <button type="button" class="btn default btn-circle" data-dismiss="modal"><?= dsAlt('2616qeydiyyat_pencereleri_imtina', "İmtina et")?></button>
    </div>
</div>

<script>
    $('.scrollbox').on('click', 'div', function () {
        var user_id = $(this).attr('id');

        if ($('#' + user_id).hasClass('checkedd')) {
            $('#'+user_id).attr('class','checkk');
            $('#'+user_id + ' svg').attr('class','white');
            $('.checkk').insertAfter($('.after'));
        }else {
            $('#'+user_id + ' svg').attr('class','checked');
            $('#'+user_id).attr('class','checkedd');
            $('.checkedd').insertBefore($('.before'));
        }
    });
    var input_class = <?php echo json_encode($class) ?>;
    var tip = <?php echo json_encode($tip) ?>;
    var input_id = <?php echo json_encode($input_id) ?>;
    var input_name = <?php echo json_encode($input_name) ?>;
    var sened_novu = <?php echo json_encode($sened_novu) ?>;
    var sened_id = <?php echo json_encode($sened_id) ?>;
    var extra_emekdash = <?php echo json_encode($extra_emekdash) ?>;
    var cari_emekdash = <?php echo json_encode($cari_emekdash) ?>;


    var executorsList = '';

    $.post('prodoc/includes/plugins/axtarish.php',{ 'ne': 'derkenar_icrachilari','a':' ','sened_tipi': tip, 'sened_id': sened_id,'sened_novu':sened_novu,'extra_emekdash':extra_emekdash, 'cari_emekdash':cari_emekdash}, function (result) {
        result = JSON.parse(result);

        result['results'].forEach(function (key,value) {

                executorsList= '<div class="' + (tip+" checkk") + '" style="width:98%;border-bottom: 1px solid aliceblue;padding: 5px;" id="' + key.id + '">' + key.text + '<svg class="check white"  version="1.1" viewBox="0 0 130.2 130.2"><polyline class="path" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/></svg></div>';

                $('.scrollbox .before').after(executorsList);

            checkedUsers(input_class,input_name)

        })
    });

    function checkedUsers(input_class,inputname){

        $('.' +input_class).find('[name="' + inputname + '"]').each(function (mesulShexsler, index) {

            if ($(index).select2('data')){
                var user_id = $(index).select2('data').id
                $('#'+user_id).attr('class','checkedd');
                $('#'+user_id + ' svg').attr('class','checked');
                $('.checkedd').insertBefore($('.before'));
            }
        })
    }

    function selectedUsers(array,input_class,inputname){

        $('.' +input_class).find('div [data-function=action-remove]').trigger('click');
        $('.' +input_class).find('input[name="' + inputname + '"]:first').select2('data', '');

        array.forEach(function (mesulShexsler, index) {
            if (index > 0) {
                $('.' +input_class).find('div [data-function=action-add]').trigger('click');
            }
            var lastKurator = $('.' +input_class).find('input[name="' + inputname + '"]:first');

            if (mesulShexsler.text != null) lastKurator.select2('data', mesulShexsler);
        });
    }

    $(".search").on("keyup", function() {
        var value = $(this).val().toLowerCase();

        $(".scrollbox div").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $('.probtn').on('click',function () {
        var users =  '';
        var userID = '';
        var mesul_shexsler = [];

        $('.checkedd').each(function (key,value) {
            userID = value.id;
            users = $('#' + userID).text()
            mesul_shexsler.push({'id':userID,'text':users});
        });

        selectedUsers(mesul_shexsler,input_class,input_name)


        $('button[data-dismiss="modal"]').trigger('click');
    })



</script>
