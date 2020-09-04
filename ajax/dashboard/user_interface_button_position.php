<?php
session_start();
include_once '../../../class/class.functions.php';

$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$sessionUserId = $_SESSION['erpuserid'];

$all_button_names = get('tabs');
$buttons_key = get('key');


if (isset($all_button_names) && isset($buttons_key)){
    foreach ($all_button_names as $key => $button_names){
         $sql = "
            SELECT
            id
            FROM tb_user_interface_button_position 
            WHERE user_id = $sessionUserId AND 
            [key] = ".DB::quote($buttons_key)." AND
            [button_name] = ".DB::quote($key)."
        ";



        $document = DB::fetch($sql);

        if ($document == true){
            DB::update('tb_user_interface_button_position', [
                'button_name' => $key,
                'button_position'   => $button_names,
                'key'   => $buttons_key,
                'user_id' => $sessionUserId
            ], $document['id']);
        }else{
            DB::insert('tb_user_interface_button_position', [
                'button_name' => $key,
                'button_position'   => $button_names,
                'key'   => $buttons_key,
                'user_id' => $sessionUserId
            ]);
        }
    }
}
?>