<?php
session_start();
include_once '../../../class/class.functions.php';
$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

$pr = (int)$user->checkPrivilegia("msk:msk_prodoc_muraciet_alt_tipleri");
if ($pr !== 2)
{
    print json_encode(array("status"=>"hazir","template"=>htmlspecialchars('<div>Olmaz!</div>',ENT_QUOTES)));
    exit();
}

if(isset($_POST['tid']) && is_numeric($_POST['tid']) && isset($_POST['ne']) && $_POST['ne']=='sil')
{
    $tid = (int)$_POST['tid'];
    pdof() -> query("UPDATE tb_prodoc_muraciet_alt_tipler SET is_deleted='1' WHERE id='$tid'");
}

else if(isset($_POST['tid']) && is_numeric($_POST['tid']) && $_POST['tid']>=0 && isset($_POST['ad']) && is_string($_POST['ad']) && trim($_POST['ad'])!="" && isset($_POST['fildType']))
{
    $tid = (int)$_POST['tid'];
    $ad = $user->tmzle(trim($_POST['ad']));
    $fildType = (int)$_POST['fildType'];
    $tekrarYoxla = pdof() -> query("SELECT 1 FROM tb_prodoc_muraciet_alt_tipler WHERE is_deleted<>'1' AND ad=N'$ad' ".($tid>0?" AND id<>'$tid'":"")." ")->fetch();
    if($tekrarYoxla)
    {
        print "error";
        exit();
    }
    if($tid>0)
    {
        pdof() -> query("UPDATE tb_prodoc_muraciet_alt_tipler SET ad=N'$ad', parent_id = '{$fildType}' WHERE id='$tid'");
    }
    else
    {
        $userTenantId = $user->getActiveTenantId();
        pdof() -> query("INSERT INTO tb_prodoc_muraciet_alt_tipler (ad,parent_id, is_deleted, TenantId) VALUES (N'$ad', '{$fildType}', '0', '$userTenantId')");
        $tid = pdof() -> query("SELECT MAX(id) FROM tb_prodoc_muraciet_alt_tipler")->fetch();
        $tid = $tid[0];
    }
    print $tid;
}
