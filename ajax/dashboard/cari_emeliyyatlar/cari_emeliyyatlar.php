<?php
session_start();

use Service\Confirmation\Confirmation;
use View\Helper\Proxy;

include_once '../../../../class/class.functions.php';
include_once DIRNAME_INDEX . '/prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';

$user = new User();

use PowerOfAttorney\PowerOfAttorney;

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

if (isset($_POST['outgoingDocumentId'])) {
    require_once DIRNAME_INDEX . 'prodoc/includes/outgoing_document.php';
    $_POST['sened_id'] = xosToDos((int)$_POST['outgoingDocumentId'], true);
}

$daxil_olan_sened_id = getRequiredPositiveInt('sened_id');
$tip = get('tip');

$document = new Document($daxil_olan_sened_id);
$proxy    = new Proxy($document);

$cari_emeliyyatlar = [];
$ds            = [];
$users         = [];
$derkenarUsers = [];
$senedYazirla  = [];
$isheTik       = [];
$imtinaUsers   = [];
$duzelish      = [];

$document_type = (int)DB::fetchColumn("SELECT internal_document_type_id FROM tb_daxil_olan_senedler WHERE id=".$daxil_olan_sened_id);

//XOS
if($tip == 'chixan_sened')
{
    require_once DIRNAME_INDEX . 'prodoc/ajax/dashboard/cari_emeliyyatlar/chixan_sened_cari_emeliyyat.php';
    return;
}

//DAXILI SENED
if ($document_type > 0)
{
    require_once DIRNAME_INDEX . 'prodoc/ajax/dashboard/cari_emeliyyatlar/daxili_sened_cari_emeliyyat.php';
}

$derkenarUsers = $document->derkenarYazaBilenShexshler();
$senedYazirla  = $document->senedHazirlayaBilenUserler();
$isheTik       = $document->isheTikeBilenUserler();
$imtinaUsers   = $document->getUsersWhoCanCancel();
$duzelish      = $document->getEditors();
$dosOperations = getCurrentApprovingUsers($document);

$html = '';

$users = getAllUserName(array_merge($derkenarUsers, $senedYazirla, $imtinaUsers, $duzelish, $isheTik,
    array_column($dosOperations, 'user_id'),
    array_column($ds, 'user_id')));

$sql = " 
    SELECT
        tb1.state,
        tb1.status,
        tb1.rey_muellifi,
        tb1.yoxlayan_shexs,
        tb1.created_by,
        tb1.document_number_id
    from v_daxil_olan_senedler tb1
    WHERE tb1.id = $daxil_olan_sened_id
  ";

$dos = DB::fetch($sql);


//DOS STATE

if ($dos['state'] == Document::STATE_INSPECTED) {
    $reyMuellifiUserInfo = getAllUserName([$dos['rey_muellifi']]);
    $cari_emeliyyatlar[] = [
        'emeliyyatBt' => array('İmtina', ' / Qəbul et'),
        'emeliyyat' => 'Rəy müəllifi',
        'userName' => $proxy->getProxyNameByPrincipal($dos['rey_muellifi'], $reyMuellifiUserInfo[$dos['rey_muellifi']]['user_ad'])
            . ' (' . $reyMuellifiUserInfo[$dos['rey_muellifi']]['vezife'] . ') - '
    ];
} elseif ($dos['state'] == Document::STATE_IN_INSPECTION) {
    $yoxlayanUserInfo = getAllUserName([$dos['yoxlayan_shexs']]);
    $cari_emeliyyatlar[] = [
        'emeliyyatBt' => array('Təsdiq', ' / İmtina', ' / Düzəliş'),
        'emeliyyat' => 'Yoxlayan şəxs',
        'userName' => $proxy->getProxyNameByPrincipal($dos['yoxlayan_shexs'], $yoxlayanUserInfo[$dos['yoxlayan_shexs']]['user_ad'])
            . ' (' . $yoxlayanUserInfo[$dos['yoxlayan_shexs']]['vezife'] . ') - '
    ];
} elseif ($dos['state'] == Document::STATE_CANCELED) {
    $createdUser = getAllUserName([$dos['created_by']]);

    if (array_search($dos['created_by'], $duzelish) !== false) {
        $cari_emeliyyatlar[] = [
            'emeliyyatBt' => $dos['document_number_id'] > 0 ? array('Ləğv et / Düzəliş') : array('Sil / Düzəliş'),
            'emeliyyat' => 'Qeydiyyatçı',
            'userName' => $proxy->getProxyNameByPrincipal($dos['created_by'], $createdUser[$dos['created_by']]['user_ad'])
                . ' (' . $createdUser[$dos['created_by']]['vezife'] . ') - '
        ];
    } else {
        $cari_emeliyyatlar[] = [
            'emeliyyatBt' => $dos['document_number_id'] > 0 ? array('Ləğv et') : array('Sil'),
            'emeliyyat' => 'Qeydiyyatçı',
            'userName' => $proxy->getProxyNameByPrincipal($dos['created_by'], $createdUser[$dos['created_by']]['user_ad'])
                . ' (' . $createdUser[$dos['created_by']]['vezife'] . ') - '
        ];
    }

} elseif ((int)$dos['state'] === Document::STATE_AUTHOR_ACCEPTED) {

    $confirmationService = new Service\Confirmation\Confirmation($document);
    $confirmingUsers = $confirmationService->getCurrentApprovingUsers();
    $user_id = [];

    foreach ($confirmingUsers as $taskOperationUser) {
        $user_id[] = $taskOperationUser['user_id'];
    }

    $getAllUserName = getAllUserName($user_id);

    foreach ($confirmingUsers as $taskOperationUser) {
        $user_ad = $getAllUserName[$taskOperationUser['user_id']]['user_ad'];
        $vezife = $getAllUserName[$taskOperationUser['user_id']]['vezife'];
        $tipUser = $taskOperationUser['tip'];

        $cari_emeliyyatlar[] = [
            'emeliyyatBt' => array(TestiqleyecekShexs::getBtnNameByType($tipUser)),
            'emeliyyat' => getTipUser($tipUser),
            'userName' => $proxy->getProxyNameByPrincipal($taskOperationUser['user_id'], $user_ad) . ' (' . $vezife . ') - '
        ];
    }

    $sql = sprintf("
            SELECT t1.id
            FROM v_prodoc_muraciet AS t1
            WHERE t1.daxil_olan_sened_id IN (%s)
        ", $document->getId());

    foreach (DB::fetchColumnArray($sql) as $appealId) {
        $appeal = new Appeal($appealId);

        $confirmationService = new Service\Confirmation\Confirmation($appeal);
        $currentConfirmingUsers = $confirmationService->getCurrentApprovingUsers();

        foreach ($currentConfirmingUsers as $currentConfirmingUser) {
            $isheTikUserInfo = getAllUserName([$currentConfirmingUser['user_id']]);

            $cari_emeliyyatlar[] = [
                'emeliyyatBt' => array('Təsdiqlə / İmtina et'),
                'emeliyyat' => getTipUser($currentConfirmingUser['tip']),
                'userName' => $proxy->getProxyNameByPrincipal($currentConfirmingUser['user_id'], $isheTikUserInfo[$currentConfirmingUser['user_id']]['user_ad']) . ' (' . $isheTikUserInfo[$currentConfirmingUser['user_id']]['vezife'] . ') - '
            ];
        }
    }
}

// DOS Əməliyyat duymələri

foreach ($derkenarUsers as $derkenarUser) {
    $derkenarUserInfo = getUserInfoById($derkenarUser);
    $cari_emeliyyatlar[] = [
        'emeliyyatBt' => getOperationSide($derkenarUser, (int)$dos['rey_muellifi']),
        'emeliyyat' => (int)$dos['rey_muellifi'] == (int)$derkenarUser ? 'Rəy müəllifi' : 'İcraçi',
        'userName' => $proxy->getProxyNameByPrincipal($derkenarUser, $derkenarUserInfo['user_ad']) . ' (' . $derkenarUserInfo['vezife'] . ') - '
    ];
}

foreach ($senedYazirla as $senedYazirlaUser) {
    $senedYazirlaUserInfo = getUserInfoById($senedYazirlaUser);
    if (array_search($senedYazirlaUser, $derkenarUsers) === false && array_search($senedYazirlaUser, $isheTik)) {
        $cari_emeliyyatlar[] = [
            'emeliyyatBt' => array('Sənəd hazirla / Şərhlə bağla'),
            'emeliyyat' => 'Alt dəkənar icraçısı',
            'userName' => $proxy->getProxyNameByPrincipal($senedYazirlaUser, $senedYazirlaUserInfo['user_ad']) . ' (' . $senedYazirlaUserInfo['vezife'] . ') - '
        ];
    } elseif (array_search($senedYazirlaUser, $derkenarUsers) === false) {
        $cari_emeliyyatlar[] = [
            'emeliyyatBt' => array('Sənəd hazirla'),
            'emeliyyat' => 'Alt dəkənar icraçısı',
            'userName' => $proxy->getProxyNameByPrincipal($senedYazirlaUser, $senedYazirlaUserInfo['user_ad']) . ' (' . $senedYazirlaUserInfo['vezife'] . ') - '
        ];
    }
}

if ($dos['state'] != Document::STATE_NONE) {
    foreach ($duzelish as $duzelishUser) {
        $duzelishUserInfo = getUserInfoById($duzelishUser);

        if (array_search($duzelishUser, $derkenarUsers) === false && $dos['state'] != Document::STATE_CANCELED && $duzelishUser != $dos['yoxlayan_shexs']) {
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' => array('Düzəliş'),
                'emeliyyat' => ($duzelishUser == $dos['yoxlayan_shexs'] ? 'Yoxlayan şəxs' : 'Qeydiyyatçı'),
                'userName' => $proxy->getProxyNameByPrincipal($duzelishUser, $duzelishUserInfo['user_ad']) . ' (' . $duzelishUserInfo['vezife'] . ') - '
            ];
        }
    }
}

if ($dos['state'] != Document::STATE_NONE) {
    foreach ($duzelish as $duzelishUser) {
        $duzelishUserInfo = getUserInfoById($duzelishUser);

        if ($dos['state'] == Document::STATE_AUTHOR_ACCEPTED && $duzelishUser == $dos['yoxlayan_shexs']) {
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' => array('Düzəliş'),
                'emeliyyat' => ($duzelishUser == $dos['yoxlayan_shexs'] ? 'Yoxlayan şəxs' : 'Qeydiyyatçı'),
                'userName' => $proxy->getProxyNameByPrincipal($duzelishUser, $duzelishUserInfo['user_ad']) . ' (' . $duzelishUserInfo['vezife'] . ') - '
            ];
        }
    }
}
$taskUsersInfo = getTaskUsers($document);

if($taskUsersInfo != null){

    foreach ($taskUsersInfo as $userInfo) {

        $user_ad = $userInfo['user_ad'];
        $vezife = $userInfo['vezife'];
        $tipUser = $userInfo['tip'];
        $cari_emeliyyatlar[] = [
            'emeliyyatBt' => $dos['status'] == Document::STATUS_BAGLI && isset($tipUser) && $tipUser != 'tanish_ol' ? array('Nəticə qeyd et') : array('Tanış ol'),
            'emeliyyat' => $dos['status'] == Document::STATUS_BAGLI && isset($tipUser) && $tipUser != 'tanish_ol' ? 'Ümumi şöbə' : getTipUser($tipUser),
            'userName' => $proxy->getProxyNameByPrincipal($userInfo['USERID'], $user_ad) . ' (' . $vezife . ') - '
        ];
    }
}

ob_start();
require_once DIRNAME_INDEX . 'prodoc/templates/default/dashboard/cari_emeliyyatlar.php';
$html .= ob_get_clean();
print json_encode(array("status" => "success", "html" => $html));

function getAllUserName($array)
{
    if(empty($array))
    {
        return [];
    }

    $sql = "
            SELECT 
                tb1.USERID,
                tb1.user_ad,
                tb2.vezife
            FROM v_user_adlar AS tb1
              LEFT JOIN tb_vezifeler AS tb2 ON tb2.id = (SELECT tb3.PositionId FROM TenantUserAuthentication AS tb3 WHERE tb3.TenantUserId = tb1.USERID) AND tb2.silinib = 0
            where tb1.USERID IN (".implode(',',$array).")
           ";

    return DB::fetchAllIndexed($sql, 'USERID');
}

function getUserInfoById($userID)
{
    global $users;

    $userInfo =  array_key_exists($userID, $users);

    if($userInfo)
    {
        return $users[$userID];
    }
}

function getCurrentApprovingUsers($obj)
{

    $confirmation = new Service\Confirmation\Confirmation($obj);

    return $confirmation->getCurrentApprovingUsers();
}

function getOperationSide($derkenarUser, $reyMuellifiUser)
{
    $emeliyyatlar = [];
    global $senedYazirla, $isheTik, $imtinaUsers, $duzelish;

    if($derkenarUser == $reyMuellifiUser)
    {
        $emeliyyatlar[] = dsAlt('2616derkenar', "Dərkənar icraçıları");
    }
    else
    {
        $emeliyyatlar[] = dsAlt('2616qeydiyyat_pencereleri_alt_derkenar', "Alt dərkənar");
    }

    if(array_search($derkenarUser, $senedYazirla) !== false)
    {
        $emeliyyatlar[] = ' / '.dsAlt('2616sened_hazirla', "Sənəd hazırla");
    }

    if(array_search($derkenarUser, $isheTik) !== false)
    {
        $emeliyyatlar[] = ' / '.dsAlt('2616sherhle_bagla', "Şərhlə bağla");
    }

    if(array_search($derkenarUser, $imtinaUsers) !== false)
    {
        $emeliyyatlar[] = ' /'. dsAlt('2616imtina', "imtina etdi");
    }

    if(array_search($derkenarUser, $duzelish) !== false)
    {
        $emeliyyatlar[] = ' /'. dsAlt('2616duzelish', "Düzəliş");
    }

    return $emeliyyatlar;
}

function getTaskUsers($document){
    $tasks = $document->getRelatedTasks(true);

    if(count($tasks) == 0){
        return [];
    }

    $userIds = [];
    $userDetails = [];

    $taskIds = implode(',', $tasks);

    $sql = "
            SELECT
            tb2.id,
            tb1.tip,
            tb1.user_id,
            (SELECT status FROM tb_prodoc_testiqleyecek_shexs WHERE user_id = tb1.user_id AND related_record_id = tb1.derkenar_id) as status
              FROM tb_derkenar_elave_shexsler AS tb1
              LEFT JOIN tb_derkenar tb2 ON tb1.derkenar_id = tb2.id
            WHERE  tb2.id IN ($taskIds) 
        ";

    $taskUsers = DB::fetchAll($sql);

    foreach ($taskUsers as $info){
        if ($info['status'] == 0){
            $userIds[] = $info['user_id'];
        }
        $userDetails[$info['user_id']] = $info['tip'];
    }

    $userInfo = getAllUserName($userIds);

    foreach ($userInfo as $id => $info){
        $info['tip'] = $userDetails[$id];
        $userInfo[$id] = $info;
    }

    return $userInfo;
}

function getTipUser($tip)
{
    $tipName = "";

    switch ($tip)
    {
        case 'kurator':
            $tipName = dsAlt('2616rollar_icraya_nezaret_eden', "İcraya nəzarət edən şəxs");
            break;
        case 'razilashdiran':
            $tipName = dsAlt('2616rollar_viza', "Viza/Razılaşdıran şəxs");
            break;
        case 'melumat':
            $tipName = dsAlt('2616rollar_melumatlandirilan', "Məlumatlandırılan şəxs");
            break;
        case 'mesul_shexs':
            $tipName = dsAlt('2616icrachi_dos', "İcraçı");
            break;
        case 'umumi_shobe_netice':
            $tipName = dsAlt('2616umumi_shobe', "Ümumi şöbə");
            break;
        default:
            $tipName = dsAlt('2616rollar_hemicraci',  "Həmicraçı");
    }

    return $tipName;
}