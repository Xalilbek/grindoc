<?php
session_start();
include_once '../../../class/class.functions.php';
$user = new User();


if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

$pr = (int)$user->checkPrivilegia("msk:msk_prodoc_umumi_forma_sened_novu");
if ($pr !== 2)
{
    print json_encode(array("status"=>"hazir","template"=>htmlspecialchars('<div>Olmaz!</div>',ENT_QUOTES)));
    exit();
}

if(isset($_POST['tid']) && isset($_POST['ne']) && $_POST['ne']=='sil')
{
    $tid = (int)$_POST['tid'];
    pdof()->query("DELETE FROM tb_prodoc_sened_novu_rol WHERE tip = '$tid'");
}

else if(isset($_POST['tid']) && is_numeric($_POST['tid']) && $_POST['tid']>=0 && isset($_POST['tip']) && isset($_POST['rollar']) && !empty($_POST['rollar']))
{
    $tid    = (int)$_POST['tid'];
    $tip    = $_POST['tip'];
    $rollar = $_POST['rollar'];

    if($tid>0)
    {
        pdof()->query("DELETE FROM tb_prodoc_sened_novu_rol WHERE tip = '$tid'");

        $rol = explode(",", $rollar);

        foreach ($rol as $val)
        {
            pdof()->query("INSERT INTO tb_prodoc_sened_novu_rol (tip, rol) VALUES ('$tip',".DB::quote($val).")");
        }

    }
    else
    {
        $tekrarYoxla = pdof()->query("SELECT 1 FROM tb_prodoc_sened_novu_rol WHERE tip='$tip'")->fetch();
        if($tekrarYoxla)
        {
            print "error";
            exit();
        }

        $rol = explode(",", $rollar);

        foreach ($rol as $val)
        {
            pdof()->query("INSERT INTO tb_prodoc_sened_novu_rol (tip, rol) VALUES ('$tip',".DB::quote($val).")");
        }
        $tid = $tip;

    }

    print $tid;
}
