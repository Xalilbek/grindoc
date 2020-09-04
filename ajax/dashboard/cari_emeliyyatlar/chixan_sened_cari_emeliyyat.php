<?php
use View\Helper\Proxy;

include_once '../../../../class/class.functions.php';
$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$outgoingDocumnetID = getRequiredPositiveInt('sened_id');
$tip = get('tip');

$outgoingDocument = new OutgoingDocument($outgoingDocumnetID);
$proxy            = new Proxy($outgoingDocument);

$cari_emeliyyatlar  = [];
$hamiTestiqleyendenSonra = getUsersUmumiShobe();
$isCanceled              = getUserIsCanceled();
$xosOperationsUsers      = $outgoingDocument->isCanceled() ? [] : getCurrentApprovingUsers($outgoingDocument);
$xosOperations           = array_merge($xosOperationsUsers, $hamiTestiqleyendenSonra, $isCanceled);
$users                   = getAllUserName(array_merge(array_column($xosOperationsUsers, 'user_id'),
                                                      array_column($hamiTestiqleyendenSonra, 'user_id'),
                                                      array_column($isCanceled, 'user_id')));

foreach ($xosOperations as $xosOperationsUser)
{
    $xosOperationsUserInfo = getUserInfoById($xosOperationsUser['user_id']);
    getUserInfoByTip($xosOperationsUser, $xosOperationsUserInfo);
}

ob_start();
require_once DIRNAME_INDEX . 'prodoc/templates/default/dashboard/cari_emeliyyatlar.php';
$html = ob_get_clean();

print json_encode(array("status" => "success", "html" => $html));

function getUserInfoByTip($outgoingDocumentUser, $xosOperationsUserInfo)
{
    global $proxy, $cari_emeliyyatlar;

    $tip      = $outgoingDocumentUser['tip'];
    $order    = $outgoingDocumentUser['order'];

    $userInfoBT = array('Təsdiq', ' / İmtina');
    $userInfo   = $proxy->getProxyNameByPrincipal($outgoingDocumentUser['user_id'], $xosOperationsUserInfo['user_ad']) . ' (' . $xosOperationsUserInfo['vezife'] . ') - ';

    switch ($tip)
    {
        case 'rey_muelifi':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   => dsAlt('2616rey_muellifi_dos', "Rəy müəllifi"),
                'userName'    =>  $userInfo
            ];
            break;
        case 'imtina':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  array('Düzəliş', ' / Sil'),
                'emeliyyat'   => dsAlt('2616icrachi_dos', "İcraçı"),
                'userName'    =>  $userInfo
            ];
            break;
        case 'razilashdiran':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  array('Razılaşdır', ' / İmtina et'),
                'emeliyyat'   => dsAlt('2616rollar_viza', "Viza/Razılaşdıran şəxs"),
                'userName'    =>  $userInfo
            ];
            break;
        case 'kim_gonderir':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   => dsAlt('2616rollar_imzalayan', "İmzalayan şəxs"),
                'userName'    =>  $userInfo
            ];
            break;
        case 'kurator':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   => dsAlt('2616rollar_icraya_nezaret_eden', "İcraya nəzarət edən şəxs"),
                'userName'    =>  $userInfo,
                'number'      =>  getNumberRelationXO($outgoingDocumentUser['user_id'])
            ];
            break;
        case 'ishtrakchi':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   => 'Həmicraçı',
                'userName'    =>  $userInfo,
                'number'      =>  getNumberRelationXO($outgoingDocumentUser['user_id'])
            ];
            break;
        case 'umumi_shobe':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $order == 6 ? array('Qeydiyyatdan keçirt', ' / İmtina') : $userInfoBT,
                'emeliyyat'   => dsAlt('2616rollar_umumi_shobe', "İcraya nəzarət edən şəxs"),
                'userName'    =>  $userInfo
            ];
            break;
        case 'sedr':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   =>  dsAlt('2616rollar_sedr', 'Sədr'),
                'userName'    =>  $userInfo
            ];
            break;
        case 'hami_testiqleyenden_sonra':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  array('Göndər'),
                'emeliyyat'   => dsAlt('2616rollar_umumi_shobe', "İcraya nəzarət edən şəxs"),
                'userName'    =>  $userInfo
            ];
            break;
        case 'hami_testiqleyenden_sonra_icraci':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  array('Ləğv et'),
                'emeliyyat'   =>  dsAlt('2616rollar_icrachi', 'İcraçı'),
                'userName'    =>  $userInfo
            ];
            break;
        case 'hami_testiqleyenden_sonra_cavab_gozlenilmir':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  array('Ləğv et', ' / Cavab gozlənilmir'),
                'emeliyyat'   =>  dsAlt('2616rollar_icrachi', 'İcraçı'),
                'userName'    =>  $userInfo
            ];
            break;
        case 'mesul_shexs':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   =>  dsAlt('2616rollar_icrachi', 'İcraçı'),
                'userName'    =>  $userInfo
            ];
            break;
    }
}

function getUsersUmumiShobe()
{
    global $outgoingDocument;
    $hamiTestiqleyendenSonra = $outgoingDocument->getSenders();
    $hamiTestiqleyendenSonraIcraci = $outgoingDocument->legvEdeBilenler();
    $emeliyyat = [];

    if($hamiTestiqleyendenSonra)
    {
        foreach ($hamiTestiqleyendenSonra as $gdUser)
        {
            $emeliyyat[] = [
                'user_id' => $gdUser,
                'tip'     => 'hami_testiqleyenden_sonra',
                'order'   => 0
            ];
        }
    }

    if($hamiTestiqleyendenSonraIcraci && !$outgoingDocument->canChangeAnswerIsNotRequired())
    {

        foreach ($hamiTestiqleyendenSonraIcraci as $gdUser)
        {
            $emeliyyat[] = [
                'user_id' => $gdUser,
                'tip'     => 'hami_testiqleyenden_sonra_icraci',
                'order'   => 0
            ];
        }
    }

    if($outgoingDocument->canChangeAnswerIsNotRequired())
    {
        $emeliyyat[] = [
            'user_id' =>  $outgoingDocument->data['created_by'],
            'tip'     => 'hami_testiqleyenden_sonra_cavab_gozlenilmir',
            'order'   => 0
        ];
    }


    return $emeliyyat;
}

function getUserIsCanceled()
{
    global $outgoingDocument;
    $isCanceled = $outgoingDocument->isCanceled();
    $emeliyyat = [];

    if($isCanceled)
    {
        $emeliyyat[] = [
            'user_id' =>  $outgoingDocument->data['created_by'],
            'tip'     => 'imtina',
            'order'   => 0
        ];
    }

    return $emeliyyat;
}

function getNumberRelationXO($userID)
{
    global $outgoingDocumnetID;
    $number = [];
    $dosNumber = "";

    if(empty($number))
    {
        $sql = "
            SELECT
              DISTINCT tb3.user_id,
              tb4.document_number
            FROM v_prodoc_outgoing_document_relation tb1
            LEFT JOIN tb_derkenar tb2 ON tb2.daxil_olan_sened_id = tb1.daxil_olan_sened_id
            LEFT JOIN tb_derkenar_elave_shexsler tb3 ON tb3.derkenar_id IN (tb2.id)
            LEFT JOIN tb_daxil_olan_senedler tb4 ON tb4.id = tb1.daxil_olan_sened_id
            where tb1.outgoing_document_id = $outgoingDocumnetID";

        $number = DB::fetchAll($sql);
    }

    if(!empty($number) && is_array($number))
    {

        foreach ($number as $numberUserID)
        {
            if($numberUserID['user_id'] == $userID)
            {
                $dosNumber .= " / " . $numberUserID['document_number'];
            }
        }
    }

    return $dosNumber;
}