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
$netice = [];
$sessionUserId = $_SESSION['erpuserid'];
$sened_id = $_POST['id'];
$comment = DB::quote($_POST['text']);
$date = date('Y-m-d H:i:s');
$tagUsers = isset($_POST['tag_users']) ? $_POST['tag_users'] : '';
$sened_tip = $_POST['sened_tip'];
$key = $_POST['key'];
$comment_ID = ' ';

if ($sened_id != '' && $comment != '') {
    try {
        $comment_ID = DB::fetchColumn("INSERT INTO tb_prodoc_comments (created_by, document_id, text,created_at) OUTPUT INSERTED.id VALUES ({$sessionUserId}, {$sened_id}, N{$comment},'{$date}')");

        print json_encode(array(
            "status" => "hazir",
            "reyId" => $comment_ID
        ));

    } catch (Exception $e) {
        print json_encode(array(
            "status" => "error"
        ));
    }
    if ($key > -1){
        pdof() -> query( "UPDATE tb_files SET module_entry_id = $comment_ID WHERE created_by = '$sessionUserId' AND module_name = 'comments_fayl'  AND module_entry_id = 0" );

    }

}

if ($tagUsers != NULL) {

    $sUserName = '';
    foreach ($tagUsers as $userId) {

        $sql = "
        SELECT
	      CONCAT ( Adi, ' ', Soyadi ) 
        FROM
	      tb_users 
        WHERE
	      USERID = $sessionUserId
        ";

        $sql_sened = "
            SELECT
                document_number 
            FROM
                tb_daxil_olan_senedler 
            WHERE
                id = $sened_id UNION
            SELECT
                document_number 
            FROM
                v_chixan_senedler 
            WHERE
                id = $sened_id
        ";

        $tagUserName = DB::fetch($sql);
        $document_number = DB::fetch($sql_sened);

        $tagUserName = array_values(array_unique($tagUserName, SORT_REGULAR));

        foreach ($tagUserName as $tagUserNames) {
            $sUserName = $tagUserNames;
        }
        $user->sendNotifications(true, true,
            'comment',
            $sUserName,
            '',
            $sened_id,
            $userId,
            'tag_user',
            "",
            "",
            $comment_ID,
             $document_number['document_number'],
            $sened_tip,
            'comment'
        );
    }
}


?>