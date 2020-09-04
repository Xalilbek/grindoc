<?php
session_start();
include_once '../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';

$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$comment = DB::quote($_POST['text']);
$commentId = $_POST['commentId'];


    try {
        DB::update('tb_prodoc_comments', [
            'text' => $comment
        ], $commentId);

        print json_encode(array(
            "status" => "update_hazir"
        ));

    } catch (Exception $e) {
        print json_encode(array(
            "status" => "error"
        ));
    }

?>