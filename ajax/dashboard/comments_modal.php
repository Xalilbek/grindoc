<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';

$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$document_number = '';
$comments = [];
$comment_photo = [];
$erpuserid = [];
$sessionUserId = $_SESSION['erpuserid'];
$ID = DB::quote(get('id'));
$senedTipi= get('senedTipi');
$tip = '';
if ($senedTipi == "daxil_olan_sened"){
    $document = new Document(get('id'));
    $documentInfo = $document->getData();
    $document_number = $documentInfo['document_number'];
    $tip = $document->getTypeTitle($documentInfo['tip']);
}else{
    $document = new OutgoingDocument(get('id'));
    $documentInfo = $document->getData();

    if ($documentInfo['document_number_id'] != ''){
        $sqlChixanSened = "SELECT document_number FROM tb_prodoc_document_number WHERE id = ".$documentInfo['document_number_id']." ";
        $chixanSenedNumber = DB::fetch($sqlChixanSened);
        $document_number =  $chixanSenedNumber['document_number'];
    }else{
        $document_number = '-';
    }
    $tip =  'Çıxan sənəd';
}

$comments_sql = "SELECT
	CONCAT ( Adi, ' ', Soyadi ) AS comment_created_by,
	uphoto,
	text,
	comments.created_at,
	comments.id AS rey_id,
	comments.created_by AS created_by_id ,
	files.file_actual_name  AS comment_photo,
	files.file_original_name  AS original_comment_photo
FROM
	tb_prodoc_comments comments
	LEFT JOIN tb_users users ON users.USERID = comments.created_by 
	LEFT JOIN tb_files files ON files.module_entry_id = comments.id 	AND (module_name = 'comments_fayl' OR module_name  IS NULL)
WHERE
	comments.is_deleted = 0 

	AND document_id = $ID 
ORDER BY
	created_at DESC";

$commentAll = DB::fetchAll($comments_sql);

foreach ($commentAll as $reyler) {
    if ($reyler['comment_created_by'] != '' && $reyler['text'] != '') {
        $comments[] = array(
            "comment_created_by" => htmlspecialchars($reyler['comment_created_by']),
            "comment_created_by_photo" => htmlspecialchars($reyler['uphoto']),
            "text" => $purifier->purify($reyler['text']),
            "created_at" => $reyler['created_at'],
            "rey_id" => $reyler['rey_id'],
            "created_by_id" => $reyler['created_by_id'],
        );
        $comment_photo[] = array(
            "rey_id" => $reyler['rey_id'],
            "original_comment_photo" => htmlspecialchars($reyler['original_comment_photo']),
            "photo_rey" => htmlspecialchars($reyler['comment_photo'])
        );
    }
}



$erpuserid[0]['s_photo'] = htmlspecialchars($user->getUserInfo()['uphoto']);
$erpuserid[0]['session_user_name'] = htmlspecialchars($user->getUserInfo()['user_name']);
$erpuserid[0]['document_number'] = $document_number;
$erpuserid[0]['sened_novu'] = htmlspecialchars($tip);

$comments_uniq = array_values(array_unique($comments, SORT_REGULAR));
$comment_photo_uniq = array_values(array_unique($comment_photo, SORT_REGULAR));

$data = array();

$data['comments'] = $comments_uniq;
$data['comment_photo'] = $comment_photo_uniq;
$data['erpuserid'] = $erpuserid;

print json_encode($data);
?>