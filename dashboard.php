<?php
(defined("SAYTDADI") && isset($modul_privlegiyalari) && (isset($modul_privlegiyalari['prodoc_new']) && $modul_privlegiyalari['prodoc_new'] != 0 ) ||  (isset($modul_privlegiyalari['module=prodoc_new&filter=sifaris'])&& $modul_privlegiyalari['module=prodoc_new&filter=sifaris'] != 0)  ) or die("Olmaz!!!");
$priv = (int)$modul_privlegiyalari['prodoc_new']>0 ? (int)$modul_privlegiyalari['prodoc_new'] : (int)$modul_privlegiyalari['module=prodoc_new&filter=sifaris'];
$userId = (int)$_SESSION['erpuserid'];

require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/dashboard_tabs.php';

//$priv_key = (int)isset($_GET['filter']) ? 'prodoc_new&filter=sifaris' : 'prodoc_new' ;
$doc_type = isset($_GET['filter']) ? $_GET['filter'] : '' ;

$priv = new Privilegiya();


$sql_button_position = "
    SELECT
    button_name,
    button_position,
    [key]
    FROM tb_user_interface_button_position 
    WHERE user_id = $userId ORDER BY button_position
";

$button_position = DB::fetchAll($sql_button_position);
$tab_buttons_name = [];
foreach ($button_position as $key => $value){
    $tab_buttons_name[] = $value['button_name'];
}

$sql = "
	SELECT string_id, name
	FROM tb_prodoc_alt_privilegiyalar
";
$tabsTitle = DB::fetchAllIndexed($sql, 'string_id');

function getTitleById($id)
{
    global $tabsTitle;

    if (isset($tabsTitle[$id])) {
        return $tabsTitle[$id]['name'];
    }

    return $id;
}


$visibleTabs = [];
$visibleTabs = $info_menu_show;
foreach ($tabs as $title => $subTab) {
    $visibleTabs[$title] = [];

    foreach ($subTab as $stringId) {

        if (1 === $priv->getByExtraId($stringId)){
            $visibleTabs[$title][] = $stringId;
        }
    }
}

foreach ($visibleTabs as $key => $val){
    $val[] = $key;
    $result = array_diff($val,$tab_buttons_name);

    if ($result != NULL){
        foreach ($result as $keey => $value){

            $dashboard_tabs = '';
            if ($value == 'etrafli' || $value == 'tarixce' || $value == 'senedler' || $value == 'derkenar_tab' || $value == 'cari_emeliyyatlar' || $value == 'roles'){
                $dashboard_tabs = 'info_menu';
            }else{
                $dashboard_tabs = 'dashboard_tabs';
            }

        DB::insert('tb_user_interface_button_position', [
            'button_name' => $value,
            'button_position'   => $keey + 1,
            'key'   => $dashboard_tabs,
            'user_id' => $userId
        ]);
        }
    }
}

$dos_msk = $priv->getByExtraId('daxil_olan_senedler');
$xos_msk = $priv->getByExtraId('xaric_olan_senedler');
$ds_msk =  $priv->getByExtraId('daxili_senedler');
$butunSenedlerUzre = $priv->getByExtraId('butun_senedler_uzre');
$cari_emeliyyat_msk = $priv->getByExtraId('cari_emeliyyat');
$roles_msk = $priv->getByExtraId('roles');
$dos_msk_roles = $priv->getByExtraId('daxil_olan_senedler_roles');
$xos_msk_roles = $priv->getByExtraId('xaric_olan_senedler_roles');
$ds_msk_roles = $priv->getByExtraId('daxili_senedler_roles');
$senedin_esas_melumatlarinin_tenzimlenmesi = $priv->getByExtraId('senedin_esas_melumatlarinin_tenzimlenmesi');
$tablarin_tenzimlenmesi = $priv->getByExtraId('tablarin_tenzimlenmesi');
$derkenar_left = getConfig('derkenar_left', 'filter');


$cari_emeliyyatlar  = \Service\Option\Option::getOrCreateValue('cari_emeliyyatlar', 0);
$roles  = \Service\Option\Option::getOrCreateValue('roles', 0);

$dos_rol_priv = $priv->getByExtraId('daxil_olan_sened_qeydiyyata_almaq_huququ');
$dos_huquqi_rol = $priv->getByExtraId('huquqi_shexs_qeydiyyata_almaq_huququ');
$dos_vetendash_rol = $priv->getByExtraId('vetendash_muracieti_qeydiyyata_almaq_huququ');
?>

<style>

    .senedler {
        display: none;
        align-items: center;
    }

    .senedler i {
        font-size: 40px;
        display: block;
    }

    .senedler a {
        padding: 8px 12px;
        display: inline-block;
        color: <?= SNCOLOR ?>;
        font-weight: bold;
        text-decoration: none;
    }

    .senedler a:not(:first-child):hover {
        text-decoration: underline;
    }

    .senedler a:nth-child(1) {
        display: flex;
    }

    .derkenar_goster {
        font-size: 28px;
        margin-top: -10px;
        font-weight: 500;
        margin-left: 1px;
        color: darkgrey;
        cursor: pointer;
    }
    .sherh {
        font-size: 28px;
        margin-top: -10px;
        font-weight: 500;
        margin-left: 1px;
        color: darkgrey;
        cursor: pointer;
    }

    .btn-sened {
        font-size: 18px;
        float: left;
        background-color: #2b3643;
        color: #ffffff;
        min-width: 200px;
        line-height: 51px !important;
        text-align: center;
        margin: 20px;
        padding: 48px 0;
        display: block;
    }

    .btn-sened .fa {
        font-size: 32px;
    }

    .darken {
        background-color: <?= SNCOLOR ?>;;
        color: #ffffff;
    }

    .tr-padding tr td,
    .tr-padding tr th {
        padding: 10px 8px !important;
    }

    .prodoc-heading {
        background: #fff;
        margin: -25px 0 0 -20px;
        position: absolute;
        width: 100%;
        padding: 20px 8px;
        z-index: 2;
    }

    .prodoc-heading h2,
    .prodoc-heading a {
        margin: 0;
    }

    .main-navbar {
        margin-right: 40px;
    }

    #sened-elaveler, #sened-elaveler-sol {
        background: #fff;
    }

    #sened-elaveler-body  > * {
        padding: 0 15px;
    }

    #emeliyyat-duymeleri {
        min-height: 60px;
        background: transparent;
        bottom: -38px;
        position: initial;
        text-align: right;
    }

    #emeliyyat-duymeleri .modal-footer {
        padding: 0;
    }

    #emeliyyat-duymeleri .derkenar_yaz {
        background: #F7A522;
        border-color: #F7A522;
    }

    #emeliyyat-duymeleri .btn-info:not(.ishetik), #emeliyyat-duymeleri .editBtn {
        background: #0f14f1 !important;
        border-color: #0f14f1 !important;
    }

    #emeliyyat-duymeleri button.green {
        background: #4DA877 !important;
        border-color: #4DA877 !important;
        color: white
    }

    #emeliyyat-duymeleri .approveBtn{
        background: #4DA877;
        border-color: #4DA877;
        color: white
    }

    #emeliyyat-duymeleri a {
        margin-right: 5px;
    }

    #emeliyyat-duymeleri button {
        margin-right: 5px;
    }

    #emeliyyat-duymeleri div {
        margin: 0 3px;
        font-weight: bold;
        font-family: Arial, Helvetica, sans-serif;
    }

    tr.selected {
        background-color: #d6d6bf !important;
    }

    .dashboard .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus {
        background-color: <?= SNCOLOR ?>;
    }

    .yeni_sened {
        padding: 8px 24px;
        margin-left: 15px !important;
    }

    .yeni_sened, .yeni_sened:hover, .yeni_sened:link, .yeni_sened:active {
        background-color: #009688;
        border-color: #009688;
    }

    #icra_edilmeli .nav-pills > li + li {
        margin-left: 0px;
    }

    #icra_edilmeli ul a:hover {
        text-decoration: none;
        background-color: <?= SNCOLOR ?>;
        color: #FFFFFF;
    }

    #icra_edilmeli ul {
        justify-content: flex-end;
    }

    #icra_edilmeli .form-group {

        padding: 0;

        margin-bottom: 0;
    }

    .prodoc-heading h2 {
        font-weight: bold;
        font-family: "Helvetica Neue", Arial, Helvetica, sans-serif;
    }

    #sened-elaveler .nav-tabs.nav-justified > .active > a, .nav-tabs.nav-justified > .active > a:hover, .nav-tabs.nav-justified > .active > a:focus {
        border: none;
    }

    #sened-elaveler .nav-tabs.nav-justified > li > a {
        border-bottom: none;
        border-radius: 4px 4px 0 0;
        background-color: transparent;
        color: white;
        border: none;
    }

    #sened-elaveler-sol .nav-tabs.nav-justified > li > a {
        border-bottom: none;
        border-radius: 4px 4px 0 0;
        background-color: transparent;
        color: white;
        border: none;
    }

    #sened-elaveler #sened-elaveler-sol .nav-tabs > li > a:hover {
        border-color: transparent;
        background: 0 0;
        border-bottom: 4px solid #36c6d3;
        position: relative;
    }

    #sened-elaveler .nav-tabs > li.active {
        background: 0 0;
        border-bottom: 4px solid <?= getProjectName() === SN ? '#FFF200' : '#36c6d3' ?>;
        position: relative;
    }

    #sened-elaveler-sol .nav-tabs > li.active {
        background: 0 0;
        border-bottom: 4px solid #36c6d3;
        position: relative;
    }

    #sened-elaveler #sened-elaveler-ul, #sened-elaveler-ul-sol {
        background-color: <?= SNCOLOR ?>;
    }

    #sened-elaveler-ul a, #sened-elaveler-ul-sol a {
        padding: 9px 1px 8px;
    }
    #sened-elaveler-ul{
    display: flex;
    height: 38px;
    overflow: hidden;
}
    #sened-elaveler-ul li{
       flex: 1 !important;
    }
    #sened-elaveler-ul li a:hover{
        cursor: move
    }

    #sened-elaveler-body p  {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: bold;
        font-size: 14px;
    }

    #sened-elaveler-body > dl:nth-child(1) {
        margin-top: -10px;
    }

    #sened-elaveler-body, #sened-elaveler-body-sol {
        height: 290px;
        overflow-y: auto;
        margin-bottom: 0;
    }

    .page-header {
        margin: 0;
    }

    .page-header span, #sened-elaveler-body small {
        font-weight: normal;
    }

    #senedler-tbody > tr:hover {
        background-color: #FFF;
    }

    #senedler-tbody {
        height: 531px;
    }

    .table-scroll thead th:last-child {
        width: 156px; /* 140px + 16px scrollbar width */
    }

    .panel-collapse .panel-body label {
        display: block;
    }

    .panel-collapse .panel-body label .radio {
        vertical-align: middle;
    }

    #accordion .panel-body,
    #accordion .panel-heading {
        word-wrap: break-word;
        border: none;
        background: #fff;
    }

    #accordion .panel-heading {
        padding: 0;
    }

    #accordion .panel-title a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        text-transform: uppercase;
        font-weight: bold;
    }

    #accordion .panel-title {
        font-size: 17px;
    }

    #icra_edilmeli {
        display: block !important;
        visibility: visible !important;
    }

    #icra_edilmeli > ul {
        display: none;
        justify-content: center;
    }

    #accordion > div:not(:first-child) {
        display: none;
    }

    .dataTables_scrollBody thead {
        background: transparent;
    }

    .dataTables_scrollBody::-webkit-scrollbar {
        width: 6px;
        background-color: #ffffff;
    }

    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
        background-color: #ddd;
    }

    .filterIcon {
        border-radius: 50% !important;
        border: 4px solid #36c6d3;
        width: 40px;
        height: 40px;
        padding: 5px;
        cursor: pointer;
        float: left;
        margin-right: 5px;
    }

    .filterIcon div {
        height: 2px;
        background-color: black;
        width: 90%;
        margin: 3px auto;
    }

    .filterDerkenar {
        border-radius: 50% !important;
        border: 4px solid #36c6d3;
        width: 40px;
        height: 40px;
        padding: 5px;
        cursor: pointer;
        float: left;
        margin-right: 5px;
        background-color: white;
    }

    .ishe_tik {
        border-radius: 50% !important;
        border: 4px solid #CA3433;
        width: 40px;
        height: 40px;
        padding: 5px;
        cursor: pointer;
        float: left;
        margin-right: 5px;
        background-color: white;
    }

    .rey_muelifi_qebul {
        background: #1c8f5f;
        border-color: #1c8f5f;
    }

    .rey_muelifi_qebul:hover {
        background: #1c8f5f;
        border-color: #1c8f5f;
    }

    @media only screen and (max-width: 850px) {
        .prodoc-body {
            padding-top: 160px !important;
        }

        #icra_edilmeli .form-group {
            padding: 0 15px;
        }

        #icra_edilmeli ul {
            padding: 0 15px;
        }
    }

    #sened_axtar {
        width: 0;
        box-sizing: border-box;
        border: 3px solid #36c6d3;
        border-radius: 4px;
        font-size: 16px;
        background-color: white;
        background-image: url(assets/img/searchicon.png);
        background-position: 8px;
        background-repeat: no-repeat;
        padding: 18px;
        -webkit-transition: width 0.4s ease-in-out;
        transition: width 0.4s ease-in-out;
        border-radius: 25px !important;
        margin-left: 5px;
    }

    #sened_axtar:focus {
        width: 20%;
        padding: 18px 20px 18px 40px;
    }

    .tanish_ol, .tanish_ol:hover, .tanish_ol:active, .tanish_ol:link {
        background: #1C8F5F;
        border-color: #1C8F5F;
        color: white;
    }

    .qebul_et, .qebul_et:hover, .qebul_et:active, .qebul_et:link, .qebul_et:visited {
        background: #1C8F5F;
        border-color: #1C8F5F;
        color: white;
    }

    .tesdiqle-btn, .tesdiqle-btn:hover, .tesdiqle-btn:active, .tesdiqle-btn:link, .tesdiqle-btn:visited {
        background: #1C8F5F;
        border-color: #1C8F5F;
        color: white;
    }

    .probtn{
        background: #1C8F5F !important;
        border-color: #1C8F5F !important;
    }

    .form .form-bordered .form-group .control-label {
        padding-top: 5px;
    }

    .modal-content  {
        -webkit-border-radius: 15px !important;
        -moz-border-radius: 15px !important;
        border-radius: 15px !important;
    }

    .modal-header{
        -webkit-border-radius: 13px 13px 0 0 !important;
        -moz-border-radius: 13px 13px 0 0 !important;
        border-radius: 13px 13px 0 0 !important;

    }

    h2 {
        font-size: 28px;
    }

	.page-content{
		position: relative !important;
	}
    li:hover .fa-exchange {
        -moz-transform: rotate3d(0,1,0,180deg);
        -webkit-transform: rotate3d(0,1,0,180deg);
        -ms-transform: rotate3d(0,1,0,180deg);
        transform: rotate3d(0,1,0,180deg);
        font-size: 15px;
    }
    .change_tabs_position {
        position: relative;
        display: inline-block;
    }

    .change_tabs_position .tooltiptext {
        visibility: hidden;
        width: 120px;
        color: #337ab7;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        right: -52px !important;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }

    .change_tabs_position:hover .tooltiptext {
        visibility: visible;
    }
    [role='presentation']{
        margin-left:0px !important;
    }
    #accordionWrapper{
        display: none;
    }

</style>
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link href="prodoc/asset/dashboard/roles/general_roles.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="asset/global/plugins/uniform/css/uniform.default.css?v1"/>
<script type="text/javascript" src="assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datepicker/css/datepicker.css"/>

<link rel="stylesheet" href="assets/datatables/css/datatables.min.css">
<link rel="stylesheet" href="assets/datatables/css/scroller.datatables.min.css">
<div class="dashboard prodoc-main-page">
    <div class="row prodoc-heading">
        <div class="col-md-1 col-sm-1">
            <h2 class="text-bold"><?= getProjectName() === SN ? 'Səhiyyə Nazirliyi' : ((int)isset($_GET['filter'])? 'Sifariş' : 'ProDoc') ?></h2>
        </div>
        <div class="col-md-3 col-sm-5">
            <a class="btn btn-success btn-circle yeni_sened" href="javascript:"><i class="fa fa-plus"></i> <?= dsAlt('2616qeydiyyat_penceleri_yeni', 'Yeni') ?> </a>
            <div class="senedler">
                <?php if($doc_type!=''): ?>
                    <a href="javascript:void(0)"><i class="fa fa-times-circle"></i></a>
                    <a href="?hide_header=1&dsType=<?php print $doc_type ?>&module=prodoc_daxili_senedler">
                        <?= dsAlt('2616qeydiyyat_pencereleri_sifarish_yarat', "Sifariş yarat")?>
                    </a>
<!--                    <a href="index.php?module=prodoc_daxil_olan_senedler">DAXİL OLAN</a>-->
                <?php else: ?>
                    <a href="javascript:void(0)"><i class="fa fa-times-circle"></i></a>

                <?php if( !($dos_rol_priv == 0 || ($dos_huquqi_rol == 0 && $dos_vetendash_rol == 0)) ): ?>
                    <a href="index.php?module=prodoc_daxil_olan_senedler"><?= dsAlt('2616qeydiyyat_penceleri_do', 'DAXİL OLAN'); ?></a>
                <?php endif; ?>
                    <a href="index.php?module=prodoc_xaric_olan_senedler"><?= dsAlt('2616qeydiyyat_penceleri_xo', 'XARIC OLAN'); ?></a>
                    <?php if (getProjectName() !== SN): ?>
                        <a href="index.php?module=prodoc_daxili_senedler"><?= dsAlt('2616qeydiyyat_penceleri_daxili', 'DAXILI'); ?></a>
                    <?php endif; ?>
    <!--                --><?php //if (getProjectName() === TS): ?>
    <!--                    <a href="index.php?module=prodoc_daxili_senedler&sened=satin_alma">Sifariş forması</a>-->
    <!--                --><?php //endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="panel-header" style="padding: 0">
            <ul class="nav nav-pills navbar-right main-navbar">
                <?php
                $array_buttons = [];
                $array_top_buttons = [];
                foreach ($button_position as $key => $value){
                    $array_buttons[] = $value['button_name'];
                }
                ?>
                <?php foreach ($visibleTabs as $key => $visibleTab): ?>
                    <?php
                    $array_top_buttons[] = $key;
                    ?>
                <?php endforeach;; ?>
                <?php
                $tabs_name = array_intersect($array_buttons,$array_top_buttons);

                foreach ($tabs_name as $val){
                    if ($val != 'info_menu'){
                        if (($priv->getByExtraId($val) === 1) ){
                            ?>
                            <li role="presentation">
                                <a class="btn-circle" data-toggle="tab" href="#<?php print $val; ?>">
                                    <?php print dsAlt('2616'.$val, getTitleById($val)); ?>
                                </a>
                            </li>
                            <?php
                        }
                    }
                }
                ?>
                <?php
                if ($tablarin_tenzimlenmesi == 1){
                    ?>
                       <li role="presentation" class="change_tabs_position" style="width: 19px;"><i style="margin-top: 13px;color: #337ab7;font-size: 17px" class="fa fa-exchange">
                           </i><span class="tooltiptext"><?= dsAlt('2616tooltip_tablar', 'Tabların tənzimlənməsi'); ?></span>
                       </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="row prodoc-body" style="padding-top: 80px">
        <div class="col-md-12">
            <div class="tab-content" id="sub_navs" style="padding: 0 15px 0 0;">
                <div id="icra_edilmeli" class="tab-pane row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group" style="margin-left:15px;">
                                <div class="filterIcon" data-toggle="tooltip" data-placement="top" title="<?= dsAlt('2616tooltip_filterler', 'Filterlər')?>" >
                                    <div></div>
                                    <div style="width: 70%"></div>
                                    <div style="width:50%;"></div>
                                    <div style="width:25%;"></div>
                                </div>
                                <div class="filterDerkenar" data-toggle="tooltip" data-placement="top" title="<?= dsAlt('2616tooltip_derkenar', 'Dərkənar')?>"  >
                                    <div class="derkenar_goster" >D</div>
                                </div>
                                <div class="ishe_tik" data-toggle="tooltip" data-placement="top" title="
                                    <?php getProjectName()===TS ? print dsAlt('2616tooltip_sherhle_bagla', "Şərhlə bağla") : print  dsAlt('2616tooltip_ishe_tik', "İşə tik") ?>" >
                                    <div class="sherh" ><i class="fa fa-paperclip" style="margin-left: -2px;margin-top: 14px;"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" placeholder="Sənəd axtarışı" data-toggle="tooltip" data-placement="top" title="<?= dsAlt('2616sened_axtar', 'Sənəd axtar') ?>" id="sened_axtar">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <ul class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 nav nav-pills"
                                data-link="icra_edilmeli">
                                <?php
                                $len = count($visibleTabs['icra_edilmeli']);
                                $key = 0;

                                foreach ($button_position as $value){
                                    $tabs_name = array_intersect($visibleTabs['icra_edilmeli'],$value);

                                    foreach ($tabs_name as $val){
                                        if ($key<5){

                                        ?>
                                        <li role="presentation">
                                            <a class="btn-circle" data-toggle="tab"
                                               href="#<?php print $val; ?>">
                                                <?= dsAlt('2616'.$val, getTitleById($val)); ?>

                                                <span class="count_container">
                                                    <span class="badge count" style="display: none;"></span>
                                                    <span>
                                                        <i class="loading fa fa-spinner fa-spin font-custom" style="display: none;"></i>
                                                    </span>
                                                </span>
                                            </a>
                                        </li>
                                <?php
                                        }else{
                                            if ($key==5){

                                                print '   <li class="dropdown">  
                                                    <a href="#" id="icra_edilmeli_link" class="btn-circle dropdown-toggle"  data-toggle="dropdown">
                                                    '.($len-5).'
                                                    <i class="fa fa-angle-down"></i>
                                                    </a>
                                                     <ul class="dropdown-menu" role="menu" aria-labelledby="icra_edilmeli_link" style="min-width: 220px;">';
                                            }
                                                print ' 
                                                <li role="presentation">
                                                    <a role="menuitem" class="btn-circle" data-toggle="tab"
                                                       href="#'.$val.'">
                                                        '.dsAlt('2616'.$val, getTitleById($val)).'
                                                        <span class="count_container">
                                                            <span class="badge count pull-right" style="display: none;">
                                                            </span>
                                                            <span class="pull-right">
                                                                <i class="loading fa fa-spinner fa-spin font-custom" style="display: none;"></i>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>';
                                        }
                                        $key++;
                                    }
                                }
                                ?>
                                   </ul>
                                </li>
                                <span class="badge count badge-success all_count" style=""></span>
                            </ul>
                            <ul class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 nav nav-pills"
                                data-link="achiq">
                                <?php
                                $len = count($visibleTabs['achiq']);
                                $key = 0;
                                foreach ($button_position as $value){
                                    $tabs_name = array_intersect($visibleTabs['achiq'],$value);

                                    foreach ($tabs_name as $val){
                                        if ($key<4){
                                            ?>
                                            <li role="presentation">
                                                <a class="btn-circle" data-toggle="tab"
                                                   href="#<?php print $val; ?>">
                                                    <?php print dsAlt('2616'.$val, getTitleById($val)); ?>

                                                    <span class="count_container">
                                                <span class="badge count" style="display: none;">
                                                </span>
                                                <span>
                                                        <i class="loading fa fa-spinner fa-spin font-custom" style="display: none;"></i>
                                                </span>
                                            </span>
                                                </a>
                                            </li>
                                            <?php
                                        }else{
                                            if ($key==4){

                                                print '<li class="dropdown">
                                                           <a href="#" id="achiq_link" class="btn-circle dropdown-toggle" data-toggle="dropdown"> '.($len-4).'
                                                              <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="achiq_link" style="min-width: 220px;">';
                                            }
                                            print ' 
                                                 <li role="presentation">
                                                    <a role="menuitem" class="btn-circle" data-toggle="tab"
                                                       href="#'.$val.'">'.dsAlt('2616'.$val, getTitleById($val)).'<span class="count_container">
															<span class="badge count pull-right" style="display: none;">
															</span>
															<span class="pull-right">
																<i class="loading fa fa-spinner fa-spin font-custom" style="display: none;"></i>
															</span>
														</span>
                                                    </a>
                                                </li>';
                                        }
                                        $key++;
                                    }
                                }
                                ?>
                            </ul>

                        </li>
                            <span class="badge count badge-success all_count_achiq" style=""></span>
                    </ul>
<!--                            <ul class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 nav nav-pills" data-link="achiq">-->
<!--                                --><?php //foreach ($visibleTabs['achiq'] as $visibleTab): ?>
<!--                                    <li role="presentation">-->
<!--                                        <a class="btn-circle" data-toggle="tab" href="#--><?php //print $visibleTab; ?><!--">-->
<!--                                            --><?php //print getTitleById($visibleTab); ?>
<!--											<span class="count_container">-->
<!--												<span class="badge count" style="display: none;">-->
<!--												</span>-->
<!--												<span>-->
<!--													<i class="loading fa fa-spinner fa-spin font-custom" style="display: none;"></i>-->
<!--												</span>-->
<!--											</span>-->
<!--										</a>-->
<!--                                    </li>-->
<!--                                --><?php //endforeach; ?>
<!--                            </ul>-->
                            <ul class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 nav nav-pills" data-link="bagli">
                                <?php
                                foreach ($button_position as $value){
                                    $tabs_name = array_intersect($visibleTabs['bagli'],$value);

                                    foreach ($tabs_name as $val){

                                        ?>
                                        <li role="presentation">
                                            <a class="btn-circle" data-toggle="tab"
                                               href="#<?php print $val; ?>">
                                                <?php print dsAlt('2616'.$val, getTitleById($val)); ?>

                                                <span class="count_container">
                                                <span class="badge count" style="display: none;">
                                                </span>
                                                <span>
                                                        <i class="loading fa fa-spinner fa-spin font-custom" style="display: none;"></i>
                                                </span>
                                            </span>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <ul class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 nav nav-pills"
                                data-link="butun_senedler">

                            </ul>
                            <ul class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 nav nav-pills"
                                data-link="yekun_senedsiz">

                            </ul>
                            <ul class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 nav nav-pills"
                                data-link="umumi_shobe">

                            </ul>
                            <ul class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 nav nav-pills"
                                data-link="arayis_tipli_senedler">

                            </ul>
                            <div id="emeliyyat-duymeleri" class="col-md-4">

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-3 derkenar_sol" style="display: none;">
            <div id="sened-elaveler-sol">
                <ul class="nav nav-tabs nav-justified" id="sened-elaveler-ul-sol">
                    <li id="derkenar_tab_sol" role="presentation" ><a data-toggle="tab"
                                                                                       href="#sened-derkenarlar">Dərkənarlar</a>
                    </li>
                </ul>
                <div id="sened-elaveler-body-sol" class="tab-pane fade in">
                </div>
            </div>
        </div>
        <?php require_once DIRNAME_INDEX . 'prodoc/includes/dashboardFilterTemplate.php'; ?>

        <div class="col-md-8" id="tableScrollerWrapper">
            <div id="tableScroll">
                <table class="table table-responsive tr-padding table-scroll" style="border-collapse: collapse" id="tableScroller">
                    <thead class="darken">
                    <tr>
                        <th>№</th>
                        <th><?php print dsAlt('2616nomre', 'Nömrə') ?></th>
                        <th><?php print dsAlt('2616qeydiyyat_tarixi', 'Qeydiyyat tarixi') ?></th>
                        <th><?php print dsAlt('2616son_icra_tarixi_sutun', 'Son icra tarixi') ?></th>
                        <th style="display: none;"><?php print dsAlt('2616qisa_mezmun_sutun', 'Qısa məzmun') ?></th>
                    </tr>
                    </thead>
                    <tbody id="senedler-tbody">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div id="sened-elaveler">
                <ul class="nav nav-tabs nav-justified" id="sened-elaveler-ul">
                    <?php
                    foreach ($button_position as $value){
                        $tabs_name = array_intersect($visibleTabs['info_menu'],$value);

                        foreach ($tabs_name as $val){
                            ?>
                            <li sira="<?php print $value['button_position'] ?>" id="<?php print $val ?>" role="presentation"><a data-toggle="tab" href="<?php print $info_href[$val] ?>"><?php print $info_tabs_name[$val] ?></a></li>

                            <?php
                        }
                    }
                    ?>

                </ul>

                <div id="sened-elaveler-body" class="tab-pane fade in">
                </div>

            </div>
        </div>
    </div>

</div>


<script src="prodoc/asset/widget/fileUpload.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="assets/plugins/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="asset/global/plugins/uniform/jquery.uniform.min.js?v=1"></script>

<script type="text/javascript" src="assets/plugins/unitegallery/js/unitegallery.js"></script>
<link rel="stylesheet" href="assets/plugins/unitegallery/css/unite-gallery.css" type="text/css"/>
<script type="text/javascript" src="assets/plugins/unitegallery/themes/tiles/ug-theme-tiles.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="prodoc/asset/js/underscore.string.min.js"></script>
<script type="text/javascript" src="prodoc/asset/js/underscore_mixin.js"></script>

<script src="assets/datatables/js/datatables.min.js"></script>
<script src="assets/datatables/js/scroller.datatables.min.js"></script>
<script src="prodoc/app.js"></script>

<script>
    let pendingRequest = '';

    $('.change_tabs_position').on('click',function () {

        templateYukle('tablarin_tenzimlenmesi','Tabların tənzimlənməsi',{'tip': 'tablarin_tenzimlenmesi'});

    })

    if ("<?php print $senedin_esas_melumatlarinin_tenzimlenmesi === 1?>"){
        $( function() {
            $( "#sened-elaveler-ul" ).sortable({
                axis: 'x',
                update: function (event, ui) {
                    var order = 1;
                    var obj = {};
                    $("#sened-elaveler-ul li").each(function (key,value) {
                        obj[value.id] = order;
                        order++;
                    });

                    $.get('prodoc/ajax/dashboard/user_interface_button_position.php',
                        {
                            'key':'info_menu',
                            tabs: obj
                        },
                        function (response) {
                        });

                }
            });
        } );
    }


    function imtinaModalYarat() {
        return modal_yarat(
            "İmtina et",

            "<form class='form-horizontal form-bordered'>" +
            "<div class='form-body'>" +
            "<div class='form-group'>" +
            "<label class='col-md-4 control-label'>Səbəb</label>" +
            "<div class='col-md-6'>" +
            "<textarea class='form-control' placeholder='Səbəbi daxil edin' maxlength='500' limit></textarea>" +
            "</div>" +
            "</div>" +
            "</div>" +
            "</form>",

            "<button class='btn btn-danger tesdiqle'>İmtina et</button> <button class='btn default' data-dismiss='modal'>Bağla</button>",

            "btn-danger", "", true);
    }

    function transitionPage() {
        $(this).hide();
        $(".senedler").css('display', 'flex');
    }

    $(".senedler").find("i").click(function () {
        $('.senedler').hide();
        $(".yeni_sened").show();
    });

    var derkenar_left = '<?= $derkenar_left; ?>';

    if(derkenar_left == 'derkenar')
    {
        $(document).ready(function(){
            $(".filterDerkenar").trigger('click');
        });

        $("#tableScrollerWrapper").addClass("col-md-5").removeClass("col-md-8");
    }else if(derkenar_left == 'filter'){
        $("#accordionWrapper").show();
        $("#tableScrollerWrapper").addClass("col-md-6").removeClass("col-md-8");
    }

    var cari_emeliyyatlar = +'<?= $cari_emeliyyatlar ?>';

    if(cari_emeliyyatlar)
    {
        $('#cari_emeliyyatlar').show();
    }

	function refreshActiveDocument() {
		$('#senedler-tbody').find('tr.selected td:first').trigger('click');
	}
    function refreshFirstDocument() {
        $('#senedler-tbody').find('tr').eq(0).find('td').trigger('click');
    }

    function removeActiveDocument() {
        $('#senedler-tbody').find('tr.selected').remove();
    }

    function getActiveTr() {
        return $("#senedler-tbody tr.selected");
    }

    function imtinaEtmekIsteyinizeEminsinizi(callbackOnOk, text, hideTextArea,hideFileAttach) {
        if (typeof text === "undefined") {
            text = 'İmtina etmək istədiyinizə əminisnizmi?';
        }

        var senedAtach = hideFileAttach ? "" : '<div class="add-file-btn"><i class="fa fa-paperclip font-green-meadow" style="margin-left: 22px;"></i><button type="button" class="btn btn-link font-dark" style="padding: 6px;"><span style="font-weight: 500;">Sənəd əlavə et</span></button></div><div class="list-of-files" style="margin-left: 22px;"></div>';
        var imtinasebebi = hideTextArea ? "" : $("#imtina_sebebi").html();
        var mn2 = modal_yarat("Əminsiniz?", "<p style='padding-left: 20px;'>" + text + "</p>" + imtinasebebi+senedAtach, "<button class='btn btn-danger btn-circle testiqle'> Bəli</button> <button class='btn default btn-circle cancel' data-dismiss='modal'>Xeyr</button>", "btn-danger", "");
        $("#bosh_modal" + mn2).attr("style", "z-index: 10051 !important");
        $("#bosh_modal" + mn2 + " button.testiqle").unbind("click").click(function () {

            if (hideTextArea == true){
                callbackOnOk($("#bosh_modal" + mn2));
                $("#bosh_modal" + mn2 + " button.cancel").trigger('click');
            }else{
                if(!_.isEmpty($("#bosh_modal" + mn2).find('.sebeb').val())) {
                    callbackOnOk($("#bosh_modal" + mn2));
                    $("#bosh_modal" + mn2 + " button.cancel").trigger('click');
                }
                else {
                    $("#bosh_modal" + mn2).find('.sebeb').css('border','1px dashed red');
                }
            }
        });

        $("#bosh_modal" + mn2).find('.modal-body').fileUpload({
            name: 'sened'
        });
    }
    function silmekIsteyinizeEminsinizi(callbackOnOk, text, hideTextArea) {
        if (typeof text === "undefined") {
            text = 'Silmək istədiyinizə əminisnizmi?';
        }


        var mn2 = modal_yarat("Əminsiniz?", "<p style='padding-left: 20px;'>" + text + "</p>" , "<button class='btn btn-danger btn-circle testiqle'> Bəli</button> <button class='btn default btn-circle cancel' data-dismiss='modal'>Xeyr</button>", "btn-danger", "");
        $("#bosh_modal" + mn2).attr("style", "z-index: 10051 !important");
        $("#bosh_modal" + mn2 + " button.testiqle").unbind("click").click(function () {

            callbackOnOk($("#bosh_modal" + mn2));
            // $("#bosh_modal" + mn2 + " button.cancel").trigger('click');
            location.reload();

        });
    }

    $("#emeliyyat-duymeleri").on('click', '.sened_testiq', function () {
        var self = $(this);
        sherhYazilsinFileIle(function(modal) {
            var id = self.closest('[data-id]').attr('data-id');

            if (!_.isNull(modal)) {
                var fd = Component.Form.collectData({form: modal});
            } else {
                var fd = new FormData();
            }

            fd.append('id', id);

            Component.Form.send({
                form: fd,
                url: 'prodoc/ajax/testiqleme/testiq_et.php',
                success: function(res) {
                    res = JSON.parse(res);
                    if (res.status === "error") {
                        Component.Form.showErrors(modal, [res.error_msg]);
                    } else {
                        refreshActiveDocument();
                    }
                }
            });
        }, $(this).text());
    });


    var showConfirmationModal = <?= ( getProjectName()===TS || getProjectName() === AZERCOSMOS ) ? 1 : 0 ?>;
	function sherhYazilsin(callbackOnOk, text, hideTextArea, forceConfirmation) {
		if (!showConfirmationModal && forceConfirmation !== true) {
			callbackOnOk('');
			return;
		}

		if (typeof text === "undefined") {
			text = 'Yadda saxla';
		}

		var imtinasebebi = hideTextArea ? "" : $("#sherh_yaz").html();
		var mn2 = modal_yarat("Şərh", imtinasebebi, "<button class='btn btn-circle btn-success testiqle' data-dismiss='modal'> " + text + "</button> <button class='btn default cancel' data-dismiss='modal'>Bağla</button>", "btn-success", "");
		$("#bosh_modal" + mn2).attr("style", "z-index: 10051 !important");
		$("#bosh_modal" + mn2 + " button.testiqle").unbind("click").click(function () {
			var t = $("#bosh_modal" + mn2).find('.sebeb').val();
			callbackOnOk(t);
		});
	}

    function sherhYazilsinFileIle(callbackOnOk, text, hideTextArea, forceConfirmation) {
        if (!showConfirmationModal && forceConfirmation !== true) {
            callbackOnOk(null);
            return;
        }

        if (typeof text === "undefined") {
            text = 'Yadda saxla';
        }

        var imtinasebebi = hideTextArea ? "" : $("#sherh_yaz_file_ile").html();
        var mn2 = modal_yarat("Şərh", imtinasebebi, "<button class='btn btn-circle btn-success testiqle' data-dismiss='modal'> " + text + "</button> <button class='btn default cancel' data-dismiss='modal'>Bağla</button>", "btn-success", "");
        $("#bosh_modal" + mn2).attr("style", "z-index: 10051 !important");

        // file init
        $("#bosh_modal" + mn2).find('.file-upload').fileUpload({
            name: 'sened'
        });

        $("#bosh_modal" + mn2 + " button.testiqle").unbind("click").click(function () {
            var t = $("#bosh_modal" + mn2).find('.sebeb').val();
            callbackOnOk($("#bosh_modal" + mn2));
        });
    }

    function StrDesign(str) {
        return str.toLowerCase().split(' ').join("-");
    }

    $("#accordion").on("click", ".panel-title", function () {
        if ($(this).find('i').hasClass('fa-chevron-up')) {
            $("#accordion .panel-title").each(function (item) {
                $(this).find("i").addClass('fa-chevron-down').removeClass('fa-chevron-up')
            })
            $(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');

        }
        else {
            $("#accordion .panel-title").each(function (item) {
                $(this).find("i").addClass('fa-chevron-down').removeClass('fa-chevron-up');
            });
            $(this).find('i').addClass('fa-chevron-up').removeClass('fa-chevron-down');
            collectForFilters($(this));


        }
    })

    var istiqametID = "";


    $("#icra_edilmeli .dropdown").on("click", function () {
        $(this).find(".dropdown-menu").toggle()
    })

    $(".main-navbar").on("click", "a", function () {
        var link = $(this).attr("href").slice(1);

        $("#icra_edilmeli").find("ul").hide();
        $("#icra_edilmeli").find("ul[data-link=" + link + "]").css("display", "flex");
    });


    var heightTable  = (window.innerHeight - 357),
        scrollHeight = heightTable+"px";

    $('#sened-elaveler-body').height(heightTable-8);

    var tableScroller = $('#tableScroller').DataTable({
        "bLengthChange": false,
        "searching": false,
        "paging": false,
        "scrollY": scrollHeight,
        "scrollCollapse": true,
        "ordering": false,
        "autoWidth": false,
        info: false,
        "columnDefs": [
            {
                "width": "7%", "targets": [0]
            },
            {
                "width": "31%", "targets": "_all"
            },
        ]
    });

    $(".dataTables_scrollHeadInner").css("width", "100%", "important")
    $(".dataTables_scrollHeadInner table").css("width", "inherit", "important")

    $('#senedler-tbody').on('click', 'tr[sened-id] td', function () {
        if (!($(this).find('.sened_nezaret_et').length > 0)) {
            $("#senedler-tbody tr.selected").removeClass('selected');
            let tr =  $(this).parents('tr:eq(0)');
            tr.addClass('selected');

            var sened_id = tr.attr('sened-id');
            var tip = tr.attr('data-tip');
            var executor = tr.attr('executor');

            sehifeLoading(1);
            $.get('prodoc/ajax/dashboard/emeliyyat_duymeleri.php',
                {
                    id: sened_id,
                    tip: tip,
                    executor:executor
                },
                function (response) {
                    sehifeLoading(0);
                    $("#emeliyyat-duymeleri").html(response);

                    if ("<?php print $senedin_esas_melumatlarinin_tenzimlenmesi === 1?>"){
                        $('#sened-elaveler ul li[sira="1"]').find('a').trigger('click');
                    }else{
                        $('#sened-elaveler').find('a[href="#sened-etrafli"]').trigger('click');
                    }

                });
        }
    });

    $("#emeliyyat-duymeleri").on('click', '.rey_muelifi_qebul', function () {

        var tr = getActiveTr();
        var id = tr.attr('sened-id');
        var tip = tr.attr('tip');

        $.post('prodoc/ajax/change_status.php?action=rey_muelifi_qebul', {
            tip: tip,
            id: id
        }, function () {

            refreshActiveDocument();
            toastr["success"]('Qəbul olundu');
        });

    });

    <?php if(getProjectName()===TS): ?>
    $("#emeliyyat-duymeleri").on('click', '.yoxlayici_qebul', function () {
        var tr = getActiveTr();
        var id = tr.attr('sened-id');
        var tip = tr.attr('tip');
        sherhYazilsin(function(sebeb) {
            $.post('prodoc/ajax/change_status.php?action=yoxlayici_qebul', {
                tip: tip,
                id: id,
                note: sebeb
            }, function () {
                $('#senedler-tbody').find('tr.selected td').trigger('click');
                toastr["success"]('Qəbul olundu');
                refreshActiveDocument();
            });
        }, $(this).text());
    });
    <?php else: ?>
    $("#emeliyyat-duymeleri").on('click', '.yoxlayici_qebul', function () {
        var tr = getActiveTr();
        var id = tr.attr('sened-id');
        var tip = tr.attr('tip');

        $.post('prodoc/ajax/change_status.php?action=yoxlayici_qebul', {
            tip: tip,
            id: id
        }, function () {
            $('#senedler-tbody').find('tr.selected td').trigger('click');
            toastr["success"]('Qəbul olundu');
        });
    });
    <?php endif; ?>

    $("#emeliyyat-duymeleri").on('click', '.derkenar_qebul_et', function () {

        var id = $(this).attr('data-id');

        $.post('prodoc/ajax/derkenar/qebul_et.php', {
            id: id
        }, function () {
            refreshActiveDocument();
            toastr["success"]('Dərkənar uğurla qəbul olundu');
        });

    });

    var myEfficientFn = _.debounce(function(id,senedTipi) {
        templateYukle('dashboard_comments','', {'id': id,'senedTipi':senedTipi}, 0, true);
    }, 300);

    $('#senedler-tbody').on('click','tr th .comment_btn',function () {
        if ($(this).hasClass('deactive')){
            errorModal('Sizin rəy yazmaq hüququnuz yoxdur.', 1000, true);
        }else{
            var myElement = $(this);
            var tr = $(this).parents('tr').eq(0);
            var id = tr.attr('sened-id');
            var senedTipi = tr.attr('data-tip');

            myElement.attr("disabled",'disabled');
            setTimeout(() => {
                myElement.attr("disabled",false);
            }, 1000);
            myEfficientFn(id,senedTipi);
        }
    })


    $('#senedler-tbody').on('click','tr td .sened_nezaret_et',function () {
        // var tr = getActiveTr();
        var tr = $(this).parents('tr').eq(0);
        var id = tr.attr('sened-id');

        $.post('prodoc/ajax/derkenar/nezaret_et.php', {
            id: id
        }, function (res) {
            res = JSON.parse(res);

            if (!_.isNull(res.error)) {
                // tr.attr('style', 'background: #ffdfdf !important');
                tr.find('#warningIcon').css('color', 'red');
            }
            else {
                tr.find('#warningIcon').css('color', 'gray');
                // tr.removeAttr('style');
            }

        });
    });

    $("#emeliyyat-duymeleri").on('click', '.derkenar_imtina_et', function () {

        var tr = getActiveTr();
        var id = tr.attr('sened-id');
        var tip = tr.attr('tip');

        imtinaEtmekIsteyinizeEminsinizi(function (bm) {
            var sebeb = bm.find('.sebeb').val();

            $.post('prodoc/ajax/derkenar/imtina_et.php', {
                tip: tip,
                id: id,
                sebeb: sebeb
            }, function () {
                refreshActiveDocument();
                toastr["success"]('Dərkənar imtina olundu');
            });
        });
    });

    $("#emeliyyat-duymeleri").on('click', '.sened_imtina', function () {

        var tr = getActiveTr();
        var sened_id = tr.attr('sened-id');

        imtinaEtmekIsteyinizeEminsinizi(function (bm) {
            var fd = Component.Form.collectData({form: bm});
            var id = $("#emeliyyat-duymeleri").find('[data-id]').attr('data-id'),
             sebeb = bm.find('.sebeb').val();


            fd.append('id', id);
            fd.append('sebeb', sebeb);
            fd.append('sened_id', sened_id);

            Component.Form.send({
                form: fd,
                url: 'prodoc/ajax/testiqleme/imtina_et.php',
                success: function () {
                    refreshActiveDocument();
                }
            });
        });
    });

    $("#emeliyyat-duymeleri").on('click', '.chixan_sened_gonder', function () {
        var tr = getActiveTr();
        var id = tr.attr('sened-id');

        $.post('prodoc/ajax/testiqleme/chixan_sened_gonder.php', {
            id: id
        }, function () {
            refreshActiveDocument();
        });
    });

	$("#emeliyyat-duymeleri").on('click', '.chixan_sened_cavab_gozlenilmir', function () {
		var tr = getActiveTr();
		var id = tr.attr('sened-id');

		sherhYazilsin(function(sebeb) {
			$.post('prodoc/ajax/testiqleme/chixan_sened_cavab_gozlenilmir.php', {
				id: id,
				note: sebeb
			}, function (res) {
				if (res.status === "success" && res.affected_docs.length) {
					showClosedOrToBeClosedDocuments(res.affected_docs);
				}

				refreshActiveDocument();
			}, 'json');
		}, $(this).text(), undefined, true);
	});

    $("#emeliyyat-duymeleri").on('click', '.alt_derkenar', function (e) {
        var executor = $('.selected').attr('executor');
        var id = $(this).attr('data-parentTaskId');
        var self = $(this);

        $.post('prodoc/ajax/dashboard/emeliyyat_duymeleri/alt_derkenar.php', {
            id: id,
            executor:executor
        }, function (response) {
            if (response.status == 'error') {
                swals('', 'Alt dərkənar daha öncə yazılıb!', 'error');
                e.preventDefault();
                return false;
            } else {
                location.href = self.attr('href');
            }
        }, 'json');

        e.preventDefault();
    });

    $("#emeliyyat-duymeleri").on('click', '.daxil_olan_sened_sened_hazirla', function (e) {

        var bildirishiGoster = +$(this).data('bildirish-goster');

        if (bildirishiGoster) {
            e.preventDefault();
            var href = $(this).attr('href');
            imtinaEtmekIsteyinizeEminsinizi(function () {
                location.href = href;
            }, 'Bu əməliyyat seçildiyi halda "İşə tikilsin" əməliyyatı ləğv olunacaq.', true);
        }

    });

    $("#emeliyyat-duymeleri").on('click', '.chixan_sened_legv_et', function () {

        var tr = getActiveTr();
        var id = tr.attr('sened-id');

        imtinaEtmekIsteyinizeEminsinizi(function (bm) {

            var fd = Component.Form.collectData({form: bm});
            var sebeb = bm.find('.sebeb').val();

            fd.append('id', id);
            fd.append('sebeb', sebeb);

            Component.Form.send({
                form: fd,
                url: 'prodoc/ajax/chixan_sened/legv_et.php',
                success: function (res) {
                    if (res.status === "success" && res.affected_docs.length) {
                        showClosedOrToBeClosedDocuments(res.affected_docs);
                    }

                    refreshActiveDocument();
                }
            });
        }, 'Ləğv etmək istədiyinizə əminsinizmi?');

    });

    $("#emeliyyat-duymeleri").on('click', '.chixan_sened_sil', function () {

        var tr = getActiveTr();
        var id = tr.attr('sened-id');

        imtinaEtmekIsteyinizeEminsinizi(function (bm) {
            $.post('prodoc/ajax/chixan_sened/sil.php', {
                id: id
            }, function () {
                removeActiveDocument();
                toastr["success"]('Sənəd silindi');
                $('a[href="#butun_senedler"]').trigger('click')
            });
        }, 'Sənədi silmək istədiyinizə əminsinizmi?',true,true);

    });

    $("#emeliyyat-duymeleri").on('click', '.sened_legv_et', function () {


        var tr = getActiveTr();
        var id = tr.attr('sened-id');

        imtinaEtmekIsteyinizeEminsinizi(function (bm) {
            var fd = Component.Form.collectData({form: bm});
            var sebeb = bm.find('.sebeb').val();

            fd.append('id', id);
            fd.append('sebeb', sebeb);

            Component.Form.send({
                form: fd,
                url: 'prodoc/ajax/legv_et.php',
                success: function () {
                    refreshActiveDocument();
                    toastr["success"]('Sənəd ləğv olundu');
                }
            });
        }, 'Ləğv etmək istədiyinizə əminsinizmi?');
    });

    $("#emeliyyat-duymeleri").on('click', '.sened_sil', function () {
        var tr = getActiveTr();
        var id = tr.attr('sened-id');

        silmekIsteyinizeEminsinizi(function (bm) {

            $.post('prodoc/ajax/sil.php', {
                id: id
            }, function (res) {
                refreshActiveDocument();
                toastr["success"]('Sənəd silindi');
            }, 'json');
        }, 'Silmək istədiyinizə əminsinizmi?');
    });

    $("#sened-elaveler").find('a[href]').click(function () {
        var tr = getActiveTr();
        var id = tr.attr('sened-id');
        var tip = tr.attr('data-tip');
        var sened_novu = tr.attr('sened-novu');

        var data_tip_fayl = {};
        var file_name = !(tip in data_tip_fayl) ? 'umumi' : data_tip_fayl[tip];

        if ('<?php print $cari_emeliyyat_msk ?>' == 0) {
            $("#cari_emeliyyatlar").hide();
        } else if (sened_novu == 'dos' && '<?php print $dos_msk ?>' == 1) {
            $('#cari_emeliyyatlar').show();
        } else if (sened_novu == 'ds' && '<?php print $ds_msk ?>' == 1) {
            $('#cari_emeliyyatlar').show();
        } else if (sened_novu == 'xos' && '<?php print $xos_msk ?>' == 1) {
            $('#cari_emeliyyatlar').show();
        } else {
            $('#cari_emeliyyatlar').hide();
        }

        if ('<?php print $roles_msk ?>' == 0) {
            $("#roles").hide();
        } else if (sened_novu == 'dos' && '<?php print $dos_msk_roles ?>' == 1) {
            $('#roles').show();
        } else if (sened_novu == 'ds' && '<?php print $ds_msk_roles ?>' == 1) {
            $('#roles').show();
        } else if (sened_novu == 'xos' && '<?php print $xos_msk_roles ?>' == 1) {
            $('#roles').show();
        } else {
            $('#roles').hide();
        }
        if ("daxil_olan_sened" === tip) {
            $("#derkenar_tab").show();
        } else {
            $("#derkenar_tab").hide();
        }

        var sened_elaveler_inf = {
            'sened-etrafli': 'prodoc/ajax/dashboard/etrafli/' + file_name + '.php',
            'sened-tarixce': 'prodoc/ajax/dashboard/tarixce/' + file_name + '.php',
            'sened-senedler': 'prodoc/ajax/dashboard/senedler/' + file_name + '.php',
            'roles'         : 'prodoc/ajax/dashboard/roles/' + file_name + '.php',
            'sened-derkenarlar': 'prodoc/ajax/dashboard/derkenarlar/derkenarlar.php',
            'cari_emeliyyatlar': 'prodoc/ajax/dashboard/cari_emeliyyatlar/cari_emeliyyatlar.php',
        };

        var path = sened_elaveler_inf[$(this).attr('href').slice(1)];

        // Remove downloaded gallery div from DOM -- sened-senedler
        $('.ug-gallery-wrapper.ug-lightbox').remove();

        $.post(path, {
            'sened_id': id,
            'tip': tip
        }, function (response) {
            response = JSON.parse(response);

            $("#sened-elaveler-body").html(response.html);
            if(pendingRequest.readyState != 4){
                pendingRequest.abort();
                showTabsCount();
            }
        });


        var derkenar_sol = $(".derkenar_sol:visible").length;

        if (derkenar_sol > 0 && id > 0) {
            var path = 'prodoc/ajax/dashboard/derkenarlar/derkenarlar.php';
            $.post(path, {
                'sened_id': id,
                'tip': tip
            }, function (response) {
                response = JSON.parse(response);
                $("#sened-elaveler-body-sol").html(response.html);
            });
        }

        <?php if (getProjectName() === TS): ?>
        if ($("#tableScrollerWrapper").hasClass("col-md-8")) {
            NoteHide();
        }
        <?php endif; ?>
    });

    function NoteHide(param = true) {
        var showHide  = param ? 'show' : 'hide',
            withTable = param ? '28%' : '48%';

        $('.darken tr').find('th:eq(4)')[showHide]();
        $('#senedler-tbody tr').each(function () {
            $(this).find('td:eq(4)')[showHide]();
            $(this).find('td:eq(2)').width(withTable);
        })
    }

    $(".filterIcon").on("click", function () {
        var tab = '';
        if ($("#accordionWrapper").css("display") == "block") {
            $("#tableScrollerWrapper").addClass("col-md-8").removeClass("col-md-6");
            $("#accordionWrapper").hide()
            tab = 'none'
            <?php if (getProjectName() === TS): ?>
            NoteHide();
            <?php endif; ?>
        }
        else {
            var derkenar_sol = $(".derkenar_sol:visible").length;
            if(derkenar_sol > 0) {
                $(".derkenar_sol").hide();
                $("#tableScrollerWrapper").addClass("col-md-6").removeClass("col-md-5");
            }
            else
            {
                $("#tableScrollerWrapper").addClass("col-md-6").removeClass("col-md-8");

            }
            $("#accordionWrapper").show()

            tab = 'filter'

            <?php if (getProjectName() === TS): ?>
            NoteHide(false);
            <?php endif; ?>
        }

        $.post('prodoc/ajax/derkenar_left.php', {
            'tab' : tab
        }, function () {
            refreshActiveDocument();
        });

    });

    $(".filterDerkenar").on("click", function () {
        var tab = '';

        if ($(".derkenar_sol").css("display") == "block") {
            $(".derkenar_sol").hide();
            $("#tableScrollerWrapper").addClass("col-md-8").removeClass("col-md-5");
            tab = 'none'
        }
        else {
            tab = 'derkenar'

            var filter = $("#accordionWrapper:visible").length;

            if(filter > 0) {
                $("#accordionWrapper").hide();
                $("#tableScrollerWrapper").addClass("col-md-5").removeClass("col-md-6");
            }
            else
            {
                $("#tableScrollerWrapper").addClass("col-md-5").removeClass("col-md-8");
            }
            $(".derkenar_sol").show();
        }

        $.post('prodoc/ajax/derkenar_left.php', {
            'tab' : tab
        }, function () {
            refreshActiveDocument();
        });
    });

    function tabsCountRec(tabs, counter = 0, doc_type, tabsLi){
        additionalData.tabsForCount = tabs[counter];
        pendingRequest = $.ajax({
            url: 'prodoc/ajax/dashboard/documents.php',
            data: additionalData,
            method: "POST",
            datatype: 'json',
            success: function (res) {
                if(counter !== tabs.length-1){
                    counter++;
                    tabsCountRec(tabs, counter, doc_type, tabsLi);
                }
                res = JSON.parse(res);
                if (res.status === "success") {
                    var count = res.count;
                    var url = 'index.php?module=prodoc_new&doc_type=' + doc_type;
                    history.pushState('', '', url);

                    tabsLi.each(function(i, e) {
                        var tabName = $(e).find('a[href]').attr('href').replace(/^#/, '');
                        if (!_.isUndefined(count[tabName])) {
                            $(e).find('a[href] .loading').hide();
                            $(e).find('a[href] .count').show().text(count[tabName]);

                            $(e).find('a[href] .count')
                                .removeClass('badge-default')
                                .removeClass('badge-success')
                                .removeClass('badge-danger')
                            ;

                            if (count[tabName] === 0) {
                                $(e).find('a[href] .count').addClass('badge-default');
                            } else if (count[tabName] > 99) {
                                $(e).find('a[href] .count').addClass('badge-danger');
                            } else {
                                $(e).find('a[href] .count').addClass('badge-success');
                            }
                        }

                    });
                    var a = 0;

                    $("ul[aria-labelledby=\"icra_edilmeli_link\"] li .count_container .badge").each(function(index, elem){
                        var countTabs = $(this).text();
                        var intcountTabs =  parseInt(countTabs)
                        a += intcountTabs
                    });
                    $("ul[data-link=\"icra_edilmeli\"] .all_count").text(a)


                    // location.href=window.location.href.split('&')[0];
                    history.pushState(null, '', window.location.href.split('&')[0]);
                }
            }
        });

    }

    var additionalData = collectValues();

    function showTabsCount(container)
    {
        var doc_type = '<?= $doc_type ?>';
        additionalData.module = doc_type;
        if (_.isUndefined(container)) {
            container = $("#sub_navs").find('ul[data-link]:visible');
        }

        var tabsLi = container.find(' > li[role=presentation]');
        if (!tabsLi.length) {
            return;
        }

        var tabs = [];
        tabsLi.each(function(i, e) {
            tabs.push($(e).find('a[href]').attr('href').replace(/^#/, ''));
        });


        if (_.isUndefined(additionalData)) {
            additionalData = {};
        }

        tabsLi.find('.count_container .count').hide();
        tabsLi.find('.count_container .loading').show();

        tabsCountRec(tabs,0,doc_type, tabsLi);
    }

    var activeTab = 'yeni';
    $(document).ready(function () {


        var dashboard = $('.dashboard');

        dashboard.find('.yeni_sened').click(transitionPage);

        $('#accordion input:radio').uniform({radioClass: 'checker'});

        var url = new URL(window.location.href);
        var id = url.searchParams.get("id");

        $(window).on('load', function () {
            if (id) {
                $('.dashboard .main-navbar').find('a[href="#butun_senedler"]').trigger('click');

                setTimeout(function(){
                    var reyId = <?php print $id = (isset($_GET['comment_id']) ? $_GET['comment_id'] : 0); ?>;
                    var senedId = <?php print $senedId = (isset($_GET['id']) ? $_GET['id'] : 0); ?>;

                    if (reyId > 0){
                        $(document).find('[sened-id="'+senedId+'"] .comment_btn' ).trigger('click')
                    }
                }, 900);

            } else {
                //$('.dashboard .main-navbar').find('a:last').trigger('click');
                // $('.dashboard #icra_edilmeli').find('a[href="#yeni"]').trigger('click');
            }
        });

        $('.prodoc-heading .nav li, #sub_navs .nav li[role=presentation]').on('click', 'a', function () {
            var sub_nav_id = $(this).attr('href').slice(1),
                sub_nav_e = 'div[id="' + sub_nav_id + '"]';

            if ($(this).closest('.main-navbar').length && (sub_nav_id !== "butun_senedler" && sub_nav_id !== "yekun_senedsiz" && sub_nav_id !== "umumi_shobe" && sub_nav_id !== "arayis_tipli_senedler" )) {
                return;
            }

            if (!sub_nav_id) return;

            if ($('#sub_navs').find(sub_nav_e).length === 0) {
                activeTab = sub_nav_id;
                filterRequest();
            } else {
                $('#sub_navs').slideDown();
            }
        });

        var dashboard = $('.dashboard');
        dashboard.find('ul.nav.nav-pills.navbar-right.main-navbar li a').on('click', function () {
            var href = $(this).attr('href').slice(1);

            dashboard.find('ul[data-link=' + href + '] li:first a').trigger('click');
            setTimeout(function () {
				showTabsCount();
			});
        });


        $('.dataTables_scrollBody').on("scroll", function() {
            var rowCount    = $('#senedler-tbody tr').length;

            if (parseInt(this.scrollHeight - this.scrollTop) ===  parseInt($(this).height())) {
                sehifeLoading(1);
                filterRequest(rowCount);
            }
        });


        dashboard.find('#icra_edilmeli_link').on('click', function() {
            showTabsCount($(this).next());
		});
        dashboard.find('#achiq_link').on('click', function() {
            console.log('aciq link')
            showTabsCount($(this).next());
		});

        let searchParams = new URLSearchParams(window.location.search);
        if (!searchParams.has('id')) {
            $('a[href="#icra_edilmeli"]').trigger('click');
        }

        refreshFirstDocument();


        showTabsCount($("#icra_edilmeli_link").next());

        // Ishe tik modal fast click opens two modals problem soultion with unbinding click then binding click again
        // after some time like .5 mseconds
        function showTemplateIsheTik() {
            $('.ishe_tik .sherh').unbind('click');

            templateYukle('ishe_tik','<?php getProjectName()===TS ? print "Şərhlə bağla" : print  "İşə tik" ?>',{'tip': 'daxil_olan_sened','all_operation':1 },40,true,'green-meadow');

            // $('.ishe_tik .sherh').bind('click', showTemplateIsheTik);
            setTimeout(function() {
                $('.ishe_tik .sherh').on('click', showTemplateIsheTik);
            }, 500);
        }

        $('.ishe_tik .sherh').on('click', showTemplateIsheTik);
    });

    // history.pushState(null, '', window.location.href.split('&')[0]);

</script>
<script type="text/template" id="imtina_sebebi">
    <div class="row" style="margin-bottom: 10px; margin-top: 20px">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label col-md-2" style="padding-left: 20px;">Səbəb:</label>
                <div class="col-md-7">
					<textarea id="imtina_sebeb" class="form-control sebeb" maxlength=2000
                              placeholder="Səbəb"></textarea>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/template" id="sherh_yaz">
    <div class="row" style="margin-bottom: 10px; margin-top: 20px">
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-offset-1 col-md-10">
					<textarea
                            id="sherh_yaz"
                            class="form-control sebeb"
                            maxlength=2000
                            placeholder="Şərh"
                            style="height: 147px;"
                    ></textarea>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="sherh_yaz_file_ile">
    <div class="row" style="margin-bottom: 10px; margin-top: 20px">
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-offset-1 col-md-10">
					<textarea
                            name="note"
                            class="form-control sebeb"
                            maxlength=2000
                            placeholder="Şərh"
                            style="height: 147px;"
                    ></textarea>
                </div>
            </div>

            <div class="form-group file-upload">
                <div class="add-file-btn">
                    <i class="fa fa-paperclip font-green-meadow" style="margin-left: 64px;"></i>
                    <button type="button" class="btn btn-link font-dark" style="padding: 6px;">
                        <span style="font-weight: 500;">Sənəd əlavə et</span>
                    </button>
                </div>
                <div class="list-of-files" style="margin-left: 64px;">
                </div>
            </div>
        </div>
    </div>
</script>