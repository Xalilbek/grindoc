<style>
    .cf:after {
        visibility: hidden;
        display: block;
        font-size: 0;
        content: " ";
        clear: both;
        height: 0;
    }

    * html .cf {
        zoom: 1;
    }

    *:first-child+html .cf {
        zoom: 1;
    }

    html {
        margin: 0;
        padding: 0;
    }

    body {
        font-size: 100%;
        margin: 0;
        padding: 1.75em;
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    h1 {
        font-size: 1.75em;
        margin: 0 0 0.6em 0;
    }

    a {
        color: #2996cc;
    }

    a:hover {
        text-decoration: none;
    }

    p {
        line-height: 1.5em;
    }

    .small {
        color: #666;
        font-size: 0.875em;
    }

    .large {
        font-size: 1.25em;
    }
    /**
     * Nestable
     */

    .dd {
        position: relative;
        display: block;
        margin: 0;
        padding: 0;
        max-width: 600px;
        list-style: none;
        font-size: 13px;
        line-height: 20px;
    }

    .dd-list {
        display: block;
        position: relative;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .dd-list .dd-list {
        padding-left: 30px;
    }

    .dd-collapsed .dd-list {
        display: none;
    }

    .dd-item,
    .dd-empty,
    .dd-placeholder {
        display: block;
        position: relative;
        margin: 0;
        padding: 0;
        min-height: 20px;
        font-size: 13px;
        line-height: 20px;
    }

    .dd-handle {
        display: block;
        height: 30px;
        margin: 5px 0;
        cursor: move;
        padding: 5px 10px;
        color: #333;
        text-decoration: none;
        font-weight: 400;
        border: 1px solid #ccc;
        background: #fafafa;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        box-sizing: border-box;
        margin-left: 24px;
    }

    .dd-handle:hover {
        color: #2ea8e5;
        background: #fff;
    }

    .dd-placeholder,
    .dd-empty {
        margin: 5px 0;
        padding: 0;
        min-height: 30px;
        background: #f2fbff;
        border: 1px dashed #b6bcbf;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .dd-empty {
        border: 1px dashed #bbb;
        min-height: 100px;
        background-color: #e5e5e5;
        background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff), -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
        background-image: -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff), -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
        background-image: linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff), linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
        background-size: 60px 60px;
        background-position: 0 0, 30px 30px;
    }

    .dd-dragel {
        position: absolute;
        pointer-events: none;
        z-index: 9999;
    }

    .dd-dragel > .dd-item .dd-handle {
        margin-top: 0;
    }

    .dd-dragel .dd-handle {
        -webkit-box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
        box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
    }
    /**
     * Nestable Extras
     */

    .nestable-lists {
        display: block;
        clear: both;
        padding: 30px 0;
        width: 100%;
        border: 0;
        border-top: 2px solid #ddd;

    }

    #nestable-menu {
        padding: 0;
        margin: 20px 0;
    }

    #nestable-output,
    #nestable2-output {
        width: 100%;
        height: 7em;
        font-size: 0.75em;
        line-height: 1.333333em;
        font-family: Consolas, monospace;
        padding: 5px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    #nestable2 .dd-handle {
        color: #fff;
        border: 1px solid #999;
        background: #bbb;
        background: -webkit-linear-gradient(top, #bbb 0%, #999 100%);
        background: -moz-linear-gradient(top, #bbb 0%, #999 100%);
        background: linear-gradient(top, #bbb 0%, #999 100%);
    }

    #nestable2 .dd-handle:hover {
        background: #bbb;
    }

    @media only screen and (min-width: 700px) {
        .dd {
            overflow: auto;
            float: left;
            width: 95%;
            max-height: 450px;
        }
        .dd + .dd {
            margin-left: 2%;
        }
    }

    .dd-hover > .dd-handle {
        background: #2ea8e5 !important;
    }
    /**
     * Nestable Draggable Handles
     */

    .dd3-content {
        display: block;
        height: 30px;
        margin: 5px 0;
        padding: 5px 10px 5px 40px;
        color: #333;
        text-decoration: none;
        font-weight: bold;
        border: 1px solid #ccc;
        background: #fafafa;
        background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
        background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
        background: linear-gradient(top, #fafafa 0%, #eee 100%);
        -webkit-border-radius: 3px;
        border-radius: 3px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .dd3-content:hover {
        color: #2ea8e5;
        background: #fff;
    }

    .dd-dragel > .dd3-item > .dd3-content {
        margin: 0;
    }


    .dd3-handle {
        position: absolute;
        margin: 0;
        left: 0;
        top: 0;
        cursor: pointer;
        width: 30px;
        text-indent: 100%;
        white-space: nowrap;
        overflow: hidden;
        border: 1px solid #aaa;
        background: #ddd;
        background: -webkit-linear-gradient(top, #ddd 0%, #bbb 100%);
        background: -moz-linear-gradient(top, #ddd 0%, #bbb 100%);
        background: linear-gradient(top, #ddd 0%, #bbb 100%);
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .dd3-handle:before {
        content: '≡';
        display: block;
        position: absolute;
        left: 0;
        top: 3px;
        width: 100%;
        text-align: center;
        text-indent: 0;
        color: #fff;
        font-size: 20px;
        font-weight: normal;
    }

    .dd3-handle:hover {
        background: #ddd;
    }
    .show_tabs{
        display: block;
    }
    .hide_tabs{
        display: none;
    }
</style>
<?php
$userId = (int)$_SESSION['erpuserid'];

$priv = new Privilegiya();

$sql_button_position = "
    SELECT
    button_name,
    button_position
    FROM tb_user_interface_button_position 
    WHERE user_id = $userId ORDER BY button_position
";
$button_position = DB::fetchAll($sql_button_position);

$arr_order_tab = [];
foreach ($button_position as $key => $val){
    if($val['button_name'] == 'icra_edilmeli'){
        $arr_order_tab['icra_edilmeli'] = $val['button_position'];
    }elseif ($val['button_name'] == 'achiq'){
        $arr_order_tab['achiq'] = $val['button_position'];
    }elseif ($val['button_name'] == 'bagli'){
        $arr_order_tab['bagli'] = $val['button_position'];
    }elseif ($val['button_name'] == 'butun_senedler'){
        $arr_order_tab['butun_senedler'] = $val['button_position'];
    }elseif ($val['button_name'] == 'arayis_tipli_senedler'){
        $arr_order_tab['arayis_tipli_senedler'] = $val['button_position'];
    }elseif ($val['button_name'] == 'umumi_shobe'){
        $arr_order_tab['umumi_shobe'] = $val['button_position'];
    }elseif ($val['button_name'] == 'yekun_senedsiz'){
        $arr_order_tab['yekun_senedsiz'] = $val['button_position'];
    }
}

function getTitleById($id)
{
    $sql = "
	SELECT string_id, name
	FROM tb_prodoc_alt_privilegiyalar
";
    $tabsTitle = DB::fetchAllIndexed($sql, 'string_id');


    if (isset($tabsTitle[$id])) {
        return $tabsTitle[$id]['name'];

    }

    return $id;
}
$priv = new Privilegiya();

?>


<div class="cf nestable-lists">
    <div class="dd" id="nestable">
        <ol class="dd-list" id="tabs">
            <li class="dd-item" sira="<?php print $arr_order_tab['icra_edilmeli']?>" data-id="icra_edilmeli">
                <div onclick="hideShowTabs('icra_edilmeli')" id="icra_edilmeli"  class="dd-handle bashliq icra_edilmeli">Açıq</div>
                <ol class="dd-list">
                  <?php
                  foreach ($button_position as $val){
                      $tabs_name = array_intersect($visibleTabs['icra_edilmeli'],$val);
                      foreach ($tabs_name as $val){
                          ?>
                          <li class="dd-item">
                              <div id="<?php print $val; ?>" class="dd-handle">
                                  <?php print getTitleById($val); ?>
                              </div>
                          </li>
                          <?php
                      }
                  }
                  ?>

                </ol>
            </li>
            <li class="dd-item" sira="<?php print $arr_order_tab['achiq']?>" data-id="achiq">
                <div onclick="hideShowTabs('achiq')"  id="achiq" class="dd-handle bashliq achiq">İcrada</div>
                <ol class="dd-list">
                    <?php
                    foreach ($button_position as $val){

                        $tabs_name = array_intersect($visibleTabs['achiq'],$val);

                        foreach ($tabs_name as $val){

                            ?>
                            <li class="dd-item">
                                <div id="<?php print $val; ?>" class="dd-handle">
                                    <?php print getTitleById($val); ?>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ol>
            </li>
            <li class="dd-item" sira="<?php print $arr_order_tab['bagli']?>" data-id="bagli">
                <div onclick="hideShowTabs('bagli')"  id="bagli" class="dd-handle bashliq bagli">Bağlı</div>
                <ol class="dd-list">
                    <?php
                    foreach ($button_position as $val){

                        $tabs_name = array_intersect($visibleTabs['bagli'],$val);

                        foreach ($tabs_name as $val){

                            ?>
                            <li class="dd-item">
                                <div id="<?php print $val; ?>" class="dd-handle">
                                    <?php print getTitleById($val); ?>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ol>
            </li>
            <li class="dd-item"  sira="<?php print $arr_order_tab['butun_senedler']?>" data-id="butun_senedler">
                <div onclick="hideShowTabs('butun_senedler')" id="butun_senedler" class="dd-handle bashliq butun_senedler">Bütün sənədlər</div>
            </li>
            <?php
            if (1 === $priv->getByExtraId('arayis_tipli_senedler')){
               ?>
                <li class="dd-item" sira="<?php print $arr_order_tab['arayis_tipli_senedler']?>" data-id="arayis_tipli_senedler">
                    <div onclick="hideShowTabs('arayis_tipli_senedler')" id="arayis_tipli_senedler" class="dd-handle bashliq arayis_tipli_senedler">Arayış tipli sənədlər</div>
                </li>
            <?php
            }

            if (1 === $priv->getByExtraId('yekun_senedsiz')){
                ?>
                <li class="dd-item" sira="<?php print $arr_order_tab['yekun_senedsiz']?>" data-id="yekun_senedsiz">
                    <div onclick="hideShowTabs('yekun_senedsiz')" id="yekun_senedsiz" class="dd-handle bashliq yekun_senedsiz">Yekun sənədsiz</div>
                </li>
                <?php
            }

            if (1 === $priv->getByExtraId('umumi_shobe')){
                ?>
                <li class="dd-item" sira="<?php print $arr_order_tab['umumi_shobe']?>" data-id="umumi_shobe">
                    <div onclick="hideShowTabs('umumi_shobe')" id="umumi_shobe" class="dd-handle bashliq umumi_shobe">Ümumi şöbə</div>
                </li>
                <?php
            }
            ?>

        </ol>
    </div>
</div>
<div class="modal-footer" style="border-top: 0;">
    <div style="display: inline;">
        <button type="button" class="btn blue btn-circle editBtn" >Düzəliş et</button>
        <button type="button" class="btn default btn-circle" data-dismiss="modal">Bagla</button>
    </div>
</div>
<script>
    var sorted_buttons = []
    $('li[data-id]').each(function (key,value) {
        sorted_buttons[$(value).attr('sira')] = this
    })

    $('#tabs').append(sorted_buttons)

    var tabs = {}

    $( function() {
        $( ".dd-list" ).sortable({
            axis: 'y',
            update: function (event, ui) {
                var order_achiq = 1;
                var order_icrada = 1;
                var order_bagli = 1;
                var order_bashliq = 1;


                $('.dd-list li .bashliq').each(function (key,value) {

                    tabs[value.id] = order_bashliq;
                    order_bashliq++;

                });
                $('.dd-list li[data-id="icra_edilmeli"] ol div').each(function (key,value) {

                    tabs[value.id] = order_achiq;
                    order_achiq++;
                });
                $('.dd-list li[data-id="achiq"] ol div').each(function (key,value) {

                    tabs[value.id] = order_icrada;
                    order_icrada++;
                });
                $('.dd-list li[data-id="bagli"] ol div').each(function (key,value) {

                    tabs[value.id] = order_bagli;
                    order_bagli++;
                });



            }
        });
    });

    $('.editBtn').on('click',function () {
        $.get('prodoc/ajax/dashboard/user_interface_button_position.php',
            {
                'key':'dashboard_tabs',
                tabs: tabs
            },
            function (response) {
            });
        location.reload()
    })


    function hideShowTabs(clas) {
        if ($("." + clas).next().hasClass('hide_tabs')){
            $("." + clas).next().attr('class','show_tabs');
        } else{
            $("." + clas).next().attr('class','hide_tabs');
        }
    }
</script>
