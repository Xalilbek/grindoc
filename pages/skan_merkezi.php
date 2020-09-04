<?php
session_start();
include_once '../../class/class.functions.php';
$user = new User();
if(!$user->get_session())
{
    header("Location: ../../login.php?ref=" . base64_encode($_SERVER["QUERY_STRING"]));
    exit;
}

define("SAYTDADI", true);
define("PAGE",true);
$userId = (int)$_SESSION['erpuserid'];
$getUserInfo = DB::fetch("SELECT * FROM tb_users WHERE USERID='$userId'");
$userGroup = (int)$getUserInfo['Groupp'];

$language = $user->getLang();
$dill = new dilStclass($language);

$getGroupPrivs = DB::fetch("SELECT * FROM tb_groups WHERE group_id='$userGroup'");
$privs = is_array(@json_decode(@base64_decode($getGroupPrivs['group_privs']), true)) ? @json_decode(@base64_decode($getGroupPrivs['group_privs']), true) : array();

$getSOffGroups = DB::fetchAll("SELECT group_id FROM tb_groups WHERE group_loginOn='0'");
$sQruplar = array();
while($qq = array_shift($getSOffGroups))
{
    $sQruplar[] = $qq[0];
}
$sQruplar = implode(",", $sQruplar);

?>
<!DOCTYPE html>
<!--
App Name: Prospect
Version: 2.0
Author: Pronet MMC
Website: http://www.prospect.az/
Contact: office@pronet.az
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <base href="../../">
    <meta charset="utf-8"/>
    <title>PROSPECT | Business Resourcing & Reporting by PRONET</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="asset/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="asset/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="asset/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="asset/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="asset/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
    <link href="asset/global/plugins/errorModal/css/errorModal.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link rel="stylesheet" type="text/css" href="asset/global/plugins/bootstrap-toastr/toastr.min.css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="asset/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="asset/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="asset/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="asset/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico"/>
    <script src="asset/global/plugins/jquery.min.js?v=1" type="text/javascript"></script>
    <script src="asset/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js?v=1" type="text/javascript"></script>
    <script type="text/javascript">
        var userId =  <?php print $userId;?>,
            userAd = "<?php print htmlspecialchars($getUserInfo['Soyadi'].' '.$getUserInfo['Adi'], ENT_QUOTES);?>",
            userAdTam = "<?php print htmlspecialchars($getUserInfo['Soyadi'].' '.$getUserInfo['Adi'].' '.$getUserInfo['AtaAdi'], ENT_QUOTES);?>",
            userImza = "<?php print htmlspecialchars(mb_substr($getUserInfo['Soyadi'],0,1,'UTF-8').'.'.$getUserInfo['Adi'],ENT_QUOTES);?>";
    </script>
</head>
<!-- END HEAD -->
<body class="">
<?php
include "../modules/scan_merkezi.php";
?>
<div class="modal fade" id="bosh_modal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div style="position: absolute; width: 100%; height: 100%; background-color: #FFF; z-index: 999; opacity: 0.5; display: none;" vezife="loading"><img src="assets/img/ajax-loading.gif" style="padding-left: 46%; margin-top: 12%;"></div>
            <div class="modal-header btn btn-info" style="width: 100%;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body form">

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
<a href="#sistem_baqla" data-toggle="modal" id="chixish_modal_btn" style="display: none;"></a>
<div class="modal fade" id="sistem_baqla" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div style="position: absolute; width: 100%; height: 100%; background-color: #FFF; z-index: 999; opacity: 0.5; display: none;" data-v="loading"><img src="assets/img/ajax-loading.gif" style="padding-left: 46%; margin-top: 12%;"></div>
            <div class="modal-header btn red" style="width: 100%;">
                <h4 class="modal-title">Sistemdən çıxarılmısız.</h4>
            </div>
            <div class="modal-body" style="padding: 20px; font-size: 14px;">
                Sistemdə uzun müddət hərəkətsiz qaldıqınız üçün avtomatik olaraq sistemnən çıxarılmısınız. Zəhmət olmasa səhifəni yeniliyərək yenidın sistemı daxil olun.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn green" onclick="location.href = location.href.replace(/\#.+/,'');"><i class="icon-refresh"></i> Səhifəni yenilə</button>
            </div>
        </div>
    </div>
</div>
<script>
    function daxilOlmayibModal()
    {
        $("#chixish_modal_btn").click();
    }
    function sehifeLoading(veziyyet)
    {
        if(veziyyet==0)
        {
            $("#sehifeLoading").fadeOut(200,function(){$(this).remove();});
        }
        else
        {
            if($("#sehifeLoading").length==0)
            {
                $("body").append("<div id='sehifeLoading' style='position:fixed;top:0;left:0;width:100%;height:100%;z-index:999999999;background: rgba(0,0,0,0.4);'><div style='width: 100px; height: 40px; position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; margin: auto; background: none repeat scroll 0% 0% rgb(238, 238, 238); text-align: center; line-height: 38px; border: 1px solid rgb(221, 221, 221); vertical-align: middle; border-radius: 5px ! important; color: rgb(131, 131, 131);'><img src='assets/img/loader.gif'> Yüklənir...</div></div>");
                $("body>div:last").hide().fadeIn(200);
            }
        }
    }
    function sifir_sal(b)
    {
        return b>9?b:"0"+b;
    }
</script>
<script src="asset/global/plugins/jquery-migrate.min.js?v=1" type="text/javascript"></script>
<script src="asset/global/plugins/bootstrap/js/bootstrap.min.js?v=1" type="text/javascript"></script>
<script src="asset/global/plugins/uniform/jquery.uniform.min.js?v=1" type="text/javascript"></script>
<script src="asset/global/plugins/errorModal/js/errorModal.js" type="text/javascript"></script>
<script src="asset/global/scripts/metronic.js?v=1" type="text/javascript"></script>
<script src="asset/admin/layout/scripts/layout.js?v=1" type="text/javascript"></script>
<script src="asset/admin/layout/scripts/quick-sidebar.js?v=1" type="text/javascript"></script>
<script src="assets/plugins/template_chixart/templ.js?v=4" type="text/javascript"></script>
<script src="asset/global/plugins/bootstrap-toastr/toastr.min.js?v=1"></script>
<script src="asset/admin/pages/scripts/ui-toastr.js?v=1"></script>
<script>
    jQuery(document).ready(function()
    {
        Metronic.init();
        Layout.init();
        QuickSidebar.init();
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });
</script>
</body>
</html>