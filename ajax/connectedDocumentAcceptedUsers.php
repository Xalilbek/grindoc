<?php
use PowerOfAttorney\PowerOfAttorney;

session_start();
include_once '../../class/class.functions.php';

$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$id = get('id');
$derkenarId = getInt('derkenarId');
$document = new Document($id);
$documentParticipants = $document->getParticipants('tip',['derkenarId'=> $derkenarId]);
$role_users_id = columnArrayUsers($documentParticipants,'USERID');

$sql = "SELECT
	mesul_shexs
FROM
	tb_derkenar 
WHERE
	id = (SELECT parentTaskId FROM tb_derkenar WHERE id = ".$derkenarId.")";

$derkenarIcracisi = DB::fetch($sql);

if ($derkenarIcracisi['mesul_shexs'] != NULL){
    $role_users_id[] = $derkenarIcracisi['mesul_shexs'];
}

$userIds = [];

$powerOfAttorney = new PowerOfAttorney(
    $document,
    $id,
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


$html = '';
foreach ($role_users_id as $role_users){
    $html .= '
    <div class="col-md-12 user-header-container">
        <div style="top: 5px;position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">'.viewPhoto($document_base_roles[$role_users]['uphoto'], $document_base_roles[$role_users]['uphotoName']).'

        </div>
        <div  class="col-md-9 base-username">
            <label>
                '.htmlspecialchars($document_base_roles[$role_users]['Adi'] . ' ' . $document_base_roles[$role_users]['Soyadi']).'
                /
                '.htmlspecialchars($document_base_roles[$role_users]['struktur_bolmesi']) .'
                /
                '.htmlspecialchars($document_base_roles[$role_users]['vezife']).'

            </label>
        </div>
    </div>';
}

if (!empty($from_poa_users)){
    $html .= '<div class=" col-md-12 user-info-container"><h1 class="head-username">Vəkalətnamə üzrə:</h1></div>';
}

foreach ($from_poa_users as $toUser => $fromUserArray) {
    $html .= '
    <div class="col-md-12 user-header-container">
        <div style="top: 5px;position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">' . viewPhoto($document_base_roles[$toUser]['uphoto'], $document_base_roles[$toUser]['uphotoName']) . '
        </div>
        <div  class="col-md-19 base-username">
            <label>
                ' . htmlspecialchars($document_base_roles[$toUser]['Adi'] . ' ' . $document_base_roles[$toUser]['Soyadi']) . '
                /
                ' . htmlspecialchars($document_base_roles[$toUser]['struktur_bolmesi']) . '
                /
                ' . htmlspecialchars($document_base_roles[$toUser]['vezife']) . '
            </label>';

    foreach ($fromUserArray as $fromUser) {
        $html .= '(       
                ' . htmlspecialchars($document_base_roles[$fromUser]['Adi'] . ' ' . $document_base_roles[$fromUser]['Soyadi']) . '
                /
                ' . htmlspecialchars($document_base_roles[$fromUser]['struktur_bolmesi']) . '
                /
                ' . htmlspecialchars($document_base_roles[$fromUser]['vezife']) . ')
                     </div>
    </div>';
    }
}

print $html;

function viewPhoto($photoUrl, $baseWord){
    if(file_exists(UPLOADS_DIR_PATH .'profile-images/'. $photoUrl) && $photoUrl != NULL){
        return  "<span class='avatar_image' style='height: 28px; width: 28px; border-radius: 15px !important; background-image : url(".UPLOADS_DIR_WEB_PATH.'profile-images/'. htmlspecialchars($photoUrl).")'></span>";
    }else{
        return "<div style=\"background: white\" class=\"user-empty-photo\">". $baseWord."</div>";
    }
}

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

