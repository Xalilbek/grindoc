<?php
session_start();
include_once '../../../class/class.functions.php';
$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}


if(isset($_POST['gun']) && isset($_POST['calendarId']) && (isset($_POST['add']) || isset($_POST['sil'])))
{
    $gun        = json_decode($_POST['gun'], true);
    $calendarId = $_POST['calendarId'];

    if(isset($_POST['add'])) {
        pdof()->query("INSERT INTO tb_prodoc_weekend (day_of_the_week, calendar_id) VALUES ('$gun', '$calendarId')");
    }
    else {
        pdof()->query("DELETE FROM tb_prodoc_weekend WHERE day_of_the_week='$gun' AND calendar_id='$calendarId'");
    }
}
elseif ($_POST['show'])
{
    $calendarId = $_POST['calendarId'];

    $day = pdof()->query("SELECT * FROM tb_prodoc_weekend WHERE calendar_id = '$calendarId'")->fetchAll(PDO::FETCH_ASSOC);

    print json_encode($day);
}