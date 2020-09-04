<?php
session_start();
include_once '../../../class/class.functions.php';
$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

if(!isset($_POST['tibb_muessisei_sil']))
{
    if(isset($_POST['tibb_muessisei_id']) && isset($_POST['tibb_muessisesi']) && is_numeric($_POST['tibb_muessisei_id']) && is_string($_POST['tibb_muessisesi']) && $_POST['tibb_muessisesi']!=""
        && isset($_POST['dovlet']) && is_numeric($_POST['dovlet']) && ($_POST['dovlet']==1 || $_POST['dovlet']==0) && isset($_POST['region']) && is_numeric($_POST['region']))
    {
        $region = (int)$_POST['region'];
        $dovlet = (int)$_POST['dovlet'];
        $ad = $user->tmzle($_POST['tibb_muessisesi']);
        if($_POST['tibb_muessisei_id']==0)
        {
            $query = DB::fetchColumn("SELECT * FROM tb_prodoc_tibb_muessiseleri WHERE ad=N'$ad'");
            if($query > 0)
            {
                print "error";
            }
            else
            {
                $query = pdof()->query("INSERT INTO tb_prodoc_tibb_muessiseleri (ad,dovlet,region) VALUES (N'$ad','$dovlet','$region')");
                $getid = DB::fetchColumn("SELECT MAX(id) FROM tb_prodoc_tibb_muessiseleri");
                print($getid);
            }
        }
        else
        {
            $tibb_muessisei_id = (int)$_POST['tibb_muessisei_id'];

            $query = DB::fetchColumn("SELECT * FROM tb_prodoc_tibb_muessiseleri WHERE ad=N'$ad' AND id<>'$tibb_muessisei_id'");
            if($query > 0)
            {
                print "error";
            }
            else
            {
                $query = pdof()->query("UPDATE tb_prodoc_tibb_muessiseleri SET ad=N'$ad',region='$region',dovlet='$dovlet' WHERE id='$tibb_muessisei_id'");
                print $tibb_muessisei_id;
            }
        }
    }
}
else
{
    $tibb_muessisei_id = (int)$_POST['tibb_muessisei_id'];
    $query = pdof()->query("DELETE FROM tb_prodoc_tibb_muessiseleri WHERE id='$tibb_muessisei_id'");
}