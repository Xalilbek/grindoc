<style>
    .headerText{
        border: 1px solid #ddd;
        text-align: center;
    }

    .flexBox{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
    }
    .flexBox button{
        border: 1px solid #ddd;
        background: #fff;
        width: 45%;
        display: inline-block;
        padding: 10px 15px;
        margin-bottom: 16px;
    }
    .flex-wrapper{
        display: flex;
        justify-content: space-between;
    }
    .flex-wrapper > div{
        flex: 1;
        margin-top: 16px;
    }
    .flex-wrapper > span{
        display: block;
        width: 20px
    }

    #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }

    #sortable li{
        border: 1px solid #ddd;
        border-bottom: none;
        padding: 12px;
        height: auto ;
        margin: 0;
        width: 100%;
    }
    #sortable li:focus,#sortable li:active{
        border: 1px solid #ddd !important;
        background: #0b94ea;
        color: #fff;
        cursor: move;

    }
    #sortable li:last-child{
        border-bottom: 1px solid #ddd;
    }
    #sortable li::after{
        content: '≣';
        display: inline-block;
        float: right;
        font-size: 20px;
        font-weight: 600;
        margin-top: -7px;
    }
    .active-li{
        background: #0b94ea;
        color: #fff;
    }

    .btn.active{
        background: #428bca;
        color: mintcream;
    }
</style>
<div class="tab-pane" id="tab_16">
    <div class="row">
        <div class="col-md-6">
            <div class="headerText">
                <h4>İstiqamət</h4>
            </div>
            <div class="flex-wrapper">
                <div class=" left_side">
                    <ul class="list-group mt-3 " id="doc_types">
                        <li class="list-group-item list-group-item-action" key="dos">Daxil olan sənəd</li>
                        <li class="list-group-item" key="ds">Daxili sənəd</li>
                        <li class="list-group-item" key="xos">Xaric olan sənəd</li>
                    </ul>
                </div>
                <span></span>
                <div class="">
                    <ul class="list-group mt-3 istiqamet-right-side">
                        <li class="list-group-item" key="hs">Hüquqi sənəd</li>
                        <li class="list-group-item" key="vm">Vətəndaş müraciəti</li>
                    </ul>
                    <ul class="list-group mt-3 istiqamet-right-side-xos">
                        <li class="list-group-item " key="arayis">Arayış</li>
                        <li class="list-group-item " key="etibarname">Etibarnamə(Yanacaq doldurulması barədə)</li>
                        <li class="list-group-item " key="icra_muddeti">İcra müddətinin dəyişdirilməsi</li>
                        <li class="list-group-item " key="etibarname_esas">Etibarnamə</li>
                        <?php foreach($muracietTipleri as $tip): ?>
                            <?php if ($tip['extra_id'] !== 'icra_muddeti' && $tip['extra_id'] !== 'etibarname' &&
                                    $tip['extra_id'] !== 'etibarname_esas' && $tip['extra_id'] !== 'arayis' ):?>
                                <li class="list-group-item " key="general_<?= htmlspecialchars($tip['extra_id']) ?>">
                                    <?= htmlspecialchars($tip['ad']) ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="list-group mt-3 istiqamet-right-side-ds">
                        <?php foreach($daxiliSenedler as $sened): ?>
                            <li class="list-group-item " key="<?= htmlspecialchars($sened['extra_id']) ?>">
                                <?php if($sened['extra_id'] === 'umumi_forma'): ?>
                                    <?= htmlspecialchars($sened['name']) ?>
                                <li class="list-group-item " key="xidmeti_mektub">Ümumi forma(xidməti məktub)</li>
                                <?php else: ?>
                                    <?= htmlspecialchars($sened['name']) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="melumatlar">
                <div class="headerText">
                    <h4>Məlumatlar</h4>
                </div>
                <div class="flexBox dos_melumat_buttons" >
                    <button type="button" key="icra" class="btn">İcra tipli</button>
                    <button type="button" key="melumat" class="btn">Məlumat tipli</button>
                </div>
                <div class="flexBox umumi_forma_ds">
                    <button type="button" key="icra" class="btn">İcra tipli</button>
                    <button type="button" key="melumat" class="btn">Məlumat tipli</button>
                </div>
                <ul id="sortable">

                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    let keys_dos = {
        'head': '',
        'middle': '',
        'tail': ''
    };
    let keys_xos = {
        'head': '',
        'tail': ''
    };
    let keys_ds_umumi = {
        'head': '',
        'middle': '',
    };
    let keys_ds = {
        'head': '',
        'middle': '',
        'tail': ''
    };

    $('.istiqamet-right-side-xos').hide();
    $('.istiqamet-right-side-ds').hide();
    $('.umumi_forma_ds').hide();

    $('#doc_types li').click(function () {
        $('#doc_types li').removeClass('active');
        $(this).addClass('active');
        $('#sortable').empty();

        if($(this).attr('key') === 'dos'){
            $('.flexBox').show();
            $('.istiqamet-right-side-xos').hide();
            $('.istiqamet-right-side-ds').hide();
            $('.umumi_forma_ds').hide();
            $('.istiqamet-right-side').show();

            if(keys_dos['head'] !== $(this).attr('key')){
                keys_dos['head'] = $(this).attr('key');
            }
        }
        else if ($(this).attr('key') === 'xos'){
            $('.flexBox').hide();
            $('.istiqamet-right-side').hide();
            $('.istiqamet-right-side-ds').hide();
            $('.umumi_forma_ds').hide();
            $('.istiqamet-right-side-xos').show();

            if(keys_xos['head'] !== $(this).attr('key')){
                keys_xos['head'] = $(this).attr('key');
            }
        }
        else if ($(this).attr('key') === 'ds'){
            $('.flexBox').hide();
            $('.istiqamet-right-side').hide();
            $('.istiqamet-right-side-xos').hide();
            $('.istiqamet-right-side-ds').show();

            if(keys_ds_umumi['head'] !== $(this).attr('key')){
                keys_ds_umumi['head'] = $(this).attr('key');
            }

            if(keys_ds['head'] !== $(this).attr('key')){
                keys_ds['head'] = $(this).attr('key');
            }
        }
    });


    $('.istiqamet-right-side li').click(function () {
        $('.istiqamet-right-side li').removeClass('active');
        $(this).addClass('active');

        $('#sortable').empty();
        if(keys_dos['middle'] !== $(this).attr('key')){
            keys_dos['middle'] = $(this).attr('key');
        }
    });

    $('.istiqamet-right-side-xos li').click(function () {
        $('.istiqamet-right-side-xos li').removeClass('active');
        $(this).addClass('active');

        $('#sortable').empty();
        if(keys_xos['middle'] !== $(this).attr('key')){
            keys_xos['middle'] = $(this).attr('key');
        }
        let key = keyGenerator(keys_xos);
        sortEtrafli(key)
    });

    $('.istiqamet-right-side-ds li').click(function () {

        $('#sortable').empty();
        if(keys_ds_umumi['middle'] !== $(this).attr('key')){
            keys_ds_umumi['middle'] = $(this).attr('key');
        }

        $('.istiqamet-right-side-ds li').removeClass('active');
        $(this).addClass('active');

        if($(this).attr('key') === 'umumi_forma'){
            $('.dos_melumat_buttons').hide();
            $('.umumi_forma_ds').show();

            if(keys_ds_umumi['middle'] !== $(this).attr('key')){
                keys_ds_umumi['middle'] = $(this).attr('key');
            }

        }else{
            $('.flexBox').hide();
            if(keys_ds['middle'] !== $(this).attr('key')){
                keys_ds['middle'] = $(this).attr('key');
            }
            let key = keyGenerator(keys_ds);
            sortEtrafli(key)
        }
    });


    // dos ucun icra melumat buttonlari
    $('.dos_melumat_buttons button').click(function () {
        $('.dos_melumat_buttons button').removeClass('active');
        $(this).addClass('active');

        if(keys_dos['tail'] !== $(this).attr('key')){
            keys_dos['tail'] = $(this).attr('key');
        }

        console.log(keys_dos);
        let key_for_sortable = keyGenerator(keys_dos);
        sortEtrafli(key_for_sortable)
    });


    // umumi forma ucun icra melumat buttonlari
    $('.umumi_forma_ds button').click(function () {
        $('.umumi_forma_ds button').removeClass('active');
        $(this).addClass('active');

        if(keys_ds_umumi['tail'] !== $(this).attr('key')){
            keys_ds_umumi['tail'] = $(this).attr('key');
        }

        console.log(keys_ds_umumi);

        let key_for_sortable = keyGenerator(keys_ds_umumi);
        sortEtrafli(key_for_sortable)
    });


    function keyGenerator(keyObj){
        let arr = [];
        for(let key in keyObj){
            if(keyObj.hasOwnProperty(key) && keyObj[key] !== ''){
                arr.push(keyObj[key]);
            }
        }
        return arr.join('_');
    }

    function  loadEtrafli(key_for_sortable){
        $.post( proBundle + 'includes/msk/senedlerin_etraflisi.php', {'load': 1,'key':key_for_sortable},function(result)
        {
            $('#sortable').html('');
            var result = jQuery.parseJSON(result);
            $.each(result.data, function (index, value) {
                $('#sortable').append("<li id='" + value.id + "'>" + "" + value.button_name + "</li>")
            })

        }).fail(function () {
            alert('Something went wrong');
        })
    }

    function sortEtrafli(key){
        loadEtrafli(key);
        $('#sortable').sortable({
            axis: 'y',
            update: function (event, ui) {
                var data = $(this).sortable('serialize');
                var lis = {};

                $('#sortable li').each(function (key, value) {
                    lis[key] = value.id;
                });

                // POST to server using
                $.post( proBundle + 'includes/msk/senedlerin_etraflisi.php', {'load': 0, 'data':lis,'key':key},function(result)
                {
                    var result = jQuery.parseJSON(result);
                    $('#sortable').html('');
                    $.each(result.data, function (index, value) {
                        var button_name = value.button_name;
                        $('#sortable').append("<li id='" + value.id + "'>" + "" + value.button_name + "</li>")
                    })

                }).fail(function () {
                    alert('Something went wrong');
                })
            }
        });
    }

</script>


