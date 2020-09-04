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
$removeCommentId = get('removeCommentId');

try {
    DB::update('tb_prodoc_comments', [
        'is_deleted' => 1
    ], $removeCommentId);

    print json_encode(array(
        "status" => "remove"
    ));

} catch (Exception $e) {
    print json_encode(array(
        "status" => "error"
    ));
}