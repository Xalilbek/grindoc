<?php

$ds      = ProdocInternal::getCurrentApprovingUsers($daxil_olan_sened_id);
$users   = getAllUserName(array_merge(array_column($ds, 'user_id')));

foreach ($ds as $dsUser)
{
    $dsUserInfo = getUserInfoById($dsUser['user_id']);
    getDaxiliUserInfoByTip($dsUser, $dsUserInfo);
}

function getDaxiliUserInfoByTip($dsOperationUser, $dsOperationsUserInfo)
{
    global $proxy, $cari_emeliyyatlar;

    $tip        = $dsOperationUser['emeliyyat_tip'];

    $userInfoBT = array('Təsdiq', ' / İmtina');
    $userInfo   = $proxy->getProxyNameByPrincipal($dsOperationsUserInfo['USERID'], $dsOperationsUserInfo['user_ad']) . ' (' . $dsOperationsUserInfo['vezife'] . ') - ';


    switch ($tip)
    {
        case 'viza':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  array(dsAlt('2616razilashdir', "Razılaşdır"), ' / '.dsAlt('2616imtina', "İmtina")),
                'emeliyyat'   =>  dsAlt('2616rollar_viza', "Viza/Razılaşdıran şəxs"),
                'userName'    =>  $userInfo
            ];
            break;
        case 'kurator':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   =>  dsAlt('2616rollar_icraya_nezaret_eden', "İcraya nəzarət edən şəxs"),
                'userName'    =>  $userInfo,
                'number'      =>  getNumberRelationDS($dsOperationsUserInfo['USERID'])
            ];
            break;
        case 'ishtrakchi':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   =>  dsAlt('2616rollar_hemicraci', "Həmicraçı"),
                'userName'    =>  $userInfo,
                'number'      =>  getNumberRelationDS($dsOperationsUserInfo['USERID'])
            ];
            break;
        case 'tesdiqleme':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>  $userInfoBT,
                'emeliyyat'   =>  dsAlt('2616rollar_tesdiqleme', "Təsdiqləmə"),
                'userName'    =>  $userInfo
            ];
            break;
        case 'umumi_shobe_netice':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>   array('Nəticə qeyd et'),
                'emeliyyat'   =>   dsAlt('2616rollar_umumi_shobe', "Ümumi şöbə"),
                'userName'    =>   $userInfo
            ];
            break;
        case 'mesul_shexs_testiq':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>   $userInfoBT,
                'emeliyyat'   =>   dsAlt('2616icrachi_dos', "İcraçı"),
                'userName'    =>   $userInfo
            ];
            break;
        case 'melumatlandirma':
            $cari_emeliyyatlar[] = [
                'emeliyyatBt' =>   ['Tanış ol'],
                'emeliyyat'   =>    dsAlt('2616rollar_melumatlandirma', "Məlumatlandırma"),
                'userName'    =>   $userInfo
            ];
            break;
    }
}

function getNumberRelationDS($userID)
{
    global $daxil_olan_sened_id;
    $number = [];
    $dsNumber = "";


    if(empty($number))
    {
        $sql = "
            SELECT
              DISTINCT tb3.user_id,
              tb4.document_number
            FROM v_prodoc_muraciet tb1
            LEFT JOIN tb_derkenar tb2 ON tb2.daxil_olan_sened_id = tb1.daxil_olan_sened_id
            LEFT JOIN tb_derkenar_elave_shexsler tb3 ON tb3.derkenar_id IN (tb2.id)
            LEFT JOIN tb_daxil_olan_senedler tb4 ON tb4.id = tb1.daxil_olan_sened_id
            where tb1.related_document_id = $daxil_olan_sened_id";

        $number = DB::fetchAll($sql);
    }

    if(!empty($number) && is_array($number))
    {
        foreach ($number as $numberUserID)
        {
            if($numberUserID['user_id'] == $userID)
            {
                $dsNumber .= " / " . $numberUserID['document_number'];
            }
        }
    }

    return $dsNumber;
}