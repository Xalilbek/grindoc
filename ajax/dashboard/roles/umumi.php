<?php

use PowerOfAttorney\PowerOfAttorney;

session_start();
include_once '../../../../class/class.functions.php';
include_once '../../../model/Document.php';
include_once '../../../model/OutgoingDocument.php';
include_once '../../../model/InternalDocument.php';
require_once DIRNAME_INDEX . 'prodoc/includes/user_info.php';
require_once DIRNAME_INDEX . 'prodoc/functions/documentStatusColors.php';
$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$tip = get('tip');
$id  = get('sened_id');
$isComment  = get('isComment');
$filter = '1 = 1';

if ($tip == 'daxil_olan_sened'){
    $filter = ' related_document_id = '. $id ;
}else{
    $filter = ' outgoing_document_id = '. $id ;
}

$sql = "
SELECT
	muraciet.derkenar_id 
FROM
	tb_prodoc_muraciet muraciet
	LEFT JOIN tb_prodoc_appeal_outgoing_document appeal  ON appeal.appeal_id = muraciet.id 
WHERE" . $filter;

$derkenarId = DB::fetch($sql);

if($tip=='daxil_olan_sened'){
    require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
    $dosConnectedDos = getAllConnectedIncomingDocumentId($id);
    $document = new Document($id);
    $documentInfo = $document->getData();
    $internal_document_type_key= InternalDocument::getExtraIdById($documentInfo['internal_document_type_id']);
    $documentParticipants = $document->getParticipants('tip');
    $statusTitle = Document::getStatusTitle($document->getStatus());
    $statusDisplay = 'left: 15px';
    $connectedDocumentStyle = 'display : none';

    if(!$isComment){
        $statusDisplay = ' display : none ';
    }
    if (!$isComment && !empty($dosConnectedDos[0])){
        $connectedDocumentStyle = 'display : block';
    }

    $role_users = columnArrayUsers($documentParticipants,'USERID');
    $role_users[] = $documentInfo['rey_muellifi'];
    $role_users[] = $documentInfo['created_by'];
    $role_users[] = $documentInfo['yoxlayan_shexs'];

    $powerOfAttorney = new PowerOfAttorney(
        $document,
        $user->getId(),
        new User()
    );

    $poas = $powerOfAttorney->getPowerOfAttorneysAsDirectPrincipal(array_filter($role_users));
    $from_poa_users = [];
    $outGoingDocumentPoaUsers = [];

    foreach ($poas as $poa) {
        $from_poa_users[$poa['to_user_id']][] = $poa['from_user_id'];
        $role_users[] = $poa['from_user_id'];
        $role_users[] = $poa['to_user_id'];
    }

    $document_base_roles = users_info(array_filter($role_users));

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/dashboard/roles/document_roles.php';
    $html = ob_get_clean();

}else if($tip=='chixan_sened'){
    require_once DIRNAME_INDEX . 'prodoc/includes/outgoing_document.php';
    $xosConnectedDos = xosunDostlari($id);
    $statusDisplay = 'left: 15px';
    $connectedDocumentStyle = 'display : none';

    if(!$isComment){
        $statusDisplay = ' display : none ';
    }
    if (!$isComment && !empty($xosConnectedDos)){
        $connectedDocumentStyle = 'display : block';
    }

    $outGoingDocument = new OutgoingDocument($id);
    $outGoingDocumentInfo = $outGoingDocument->getData();
    $role_users = $outGoingDocument->getParticipantsOutgoingDocument($id);

    $statusTitle = $outGoingDocument->getStatusTitle();

    $role_users_id = columnArrayUsers($role_users,'user_id');

    $userIds = [];

    $powerOfAttorney = new PowerOfAttorney(
        $outGoingDocument,
        $user->getId(),
        new User()
    );

    $poas = $powerOfAttorney->getPowerOfAttorneysAsDirectPrincipal($role_users_id);
    $from_poa_users = [];
    $outGoingDocumentPoaUsers = [];

    foreach ($poas as $poa) {
        $from_poa_users[$poa['to_user_id']][] = $poa['from_user_id'];
        $role_users_id[] = $poa['from_user_id'];
        $role_users_id[] = $poa['to_user_id'];
    }
    $document_base_roles = users_info(array_filter($role_users_id));

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/dashboard/roles/outgoingdocument_roles.php';
    $html = ob_get_clean();
}


print json_encode(array("status"=>"success","html"=>$html));

function columnArrayUsers($other_executors = array(),$specificValue){
    $role_users = [];
    foreach ($other_executors as $participantInfo) {
        foreach ($participantInfo as $participant) {
            if (isset($participant[$specificValue])) {
                $role_users[] = $participant[$specificValue];
            }
        }
    }
    return $role_users;
}
function viewPhoto($photoUrl, $baseWord){
    if(file_exists(UPLOADS_DIR_PATH .'profile-images/'. $photoUrl) && $photoUrl != NULL){
        return  "<span class='avatar_image' style='height: 28px; width: 28px; border-radius: 15px !important; background-image : url(".UPLOADS_DIR_WEB_PATH.'profile-images/'. htmlspecialchars($photoUrl).")'></span>";
    }else{
        return "<div class=\"user-empty-photo\">". $baseWord."</div>";
    }
}