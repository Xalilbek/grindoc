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

    if(isset($_POST['structor_id']) && isset($_POST['text']) && is_numeric($_POST['structor_id']) && is_string($_POST['text']) && $_POST['text']!=""
        && isset($_POST['structor']) && is_numeric($_POST['structor']) && isset($_POST['position']) && is_numeric($_POST['position']))
    {
        $position = (int)$_POST['position'];
        $structor = (int)$_POST['structor'];
        $ad = $user->tmzle($_POST['text']);
        if($_POST['structor_id']==0)
        {
            $query = DB::fetchColumn("SELECT * FROM tb_structor_position_shablons WHERE shablon=N'$ad'");
            $query1 = DB::fetchColumn("SELECT * FROM tb_structor_position_shablons WHERE structor_id='$structor' AND position_id ='$position' ");
            if($query > 0|| $query1>0)
            {
                print "error";
            }
            else
            {
                $query = pdof()->query("INSERT INTO tb_structor_position_shablons (shablon,structor_id,position_id) VALUES (N'$ad','$structor','$position')");
                $getid = DB::fetchColumn("SELECT MAX(id) FROM tb_structor_position_shablons");
                print($getid);
            }
        }
        else
        {
            $structor_id = (int)$_POST['structor_id'];

            $query = DB::fetchColumn("SELECT * FROM tb_structor_position_shablons WHERE ad=N'$ad' AND id<>'$structor_id'");
            $query1 = DB::fetchColumn("SELECT * FROM tb_structor_position_shablons WHERE structor_id='$structor' AND position_id ='$position' ");

            if($query > 0)
            {
                print "error";
            }
            else
            {
                $query = pdof()->query("UPDATE tb_structor_position_shablons SET shablon=N'$ad',position_id='$position',structor_id='$structor' WHERE id='$structor_id'");
                print $structor_id;
            }
        }
    }
}
else
{
    $structor_id = (int)$_POST['tibb_muessisei_id'];
    $query = pdof()->query("DELETE FROM tb_structor_position_shablons WHERE id='$structor_id'");
}