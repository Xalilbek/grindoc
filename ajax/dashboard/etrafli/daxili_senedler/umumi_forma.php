<?php

use View\Helper\Proxy;

include_once DIRNAME_INDEX  . '/class/class.grouparray.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/etrafli/son_emeliyyat.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       SELECT
       top 1
               tb3.id as sened_id, tb2.document_id,
            ( CASE tb3.sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' ELSE N'Qeyd olunmayıb' END ) AS senedin_tipi,
           tb3.sened_tip ,
            ( SELECT name FROM tb_sened_novu WHERE id = tb2.sened_novu ) AS senedin_novu,
            ( SELECT name FROM tb_prodoc_nazalogiya WHERE id = tb2.qisa_mezmun ) AS qisa_mezmun,
            ( SELECT id FROM tb_sened_novu WHERE id = tb2.sened_novu AND [key] = 'xidmeti_mektub' AND deleted = 0 ) AS sened_nov_xidmeti_mektub,
            tb2.senedin_tarixi,
            tb2.qeyd,
            tb3.rey_muellifi_ad,
            tb3.icra_edilme_tarixi,
            tb3.son_icra_tarixi,
            derkenar.derkenar_metn_ad AS derkenar_metn_ad_edit,
	( CASE WHEN derkenar.derkenar_metn_id IS NOT NULL AND derkenar.derkenar_metn_id != 0 THEN ( SELECT name FROM tb_derkenar_metnler WHERE id = basic_document.derkenar_metn_id ) ELSE derkenar.diger_derkenar_metn END ) AS derkenar_metn_ad 
        FROM
            v_daxil_olan_senedler as tb3
            LEFT JOIN tb_daxil_olan_senedler as basic_document ON basic_document.id = tb3.id
            LEFT JOIN (SELECT * FROM v_derkenar) derkenar
                      ON derkenar.daxil_olan_sened_id = tb3.id	
            LEFT JOIN tb_umumi_forma as tb2 ON tb3.id= tb2.document_id 
        WHERE tb2.id = ".$sid ."ORDER BY derkenar_metn_ad desc";

    $poa = DB::fetch($sql);

    $sql = "SELECT
                * 
            FROM
                (
                SELECT DISTINCT
                    ( CASE WHEN tb1.user_id > 0 THEN tb1.user_id ELSE tb4.user_id END ) AS USERID,
                    (
                    CASE
                            
                            WHEN tb1.user_id > 0 THEN
                            ( SELECT CONCAT ( Adi, ' ', Soyadi, ' ', AtaAdi ) FROM v_users WHERE USERID = tb1.user_id ) ELSE ( SELECT CONCAT ( Adi, ' ', Soyadi, ' ', AtaAdi ) FROM v_users WHERE USERID = tb4.user_id ) 
                        END 
                        ) AS user_ad,
                        tb1.tip 
                    FROM
                        tb_daxil_olan_senedler_elave_shexsler AS tb1
                        LEFT JOIN v_user_adlar tb2 ON tb2.USERID = tb1.user_id
                        LEFT JOIN tb_prodoc_group tb3 ON tb3.id = tb1.group_id
                        LEFT JOIN tb_prodoc_group_user tb4 ON tb4.group_id = tb1.group_id 
                    WHERE
                        daxil_olan_sened_id = ".$poa['document_id']." 
                    ) dd 
                WHERE
                USERID NOT IN ( SELECT user_id FROM tb_daxil_olan_senedler_elave_shexsler WHERE daxil_olan_sened_id = ".$poa['document_id']." AND tip = 'mesul_shexs' ) 
                OR tip = 'mesul_shexs'";

    $daxilOlanSenedlerElaveShexsler = DB::fetchAll($sql);

    $incomingDocument = new Document($poa['document_id']);

    $relatedTasks = Task::getAllTasks($incomingDocument);

    $confirmation = new Service\Confirmation\Confirmation($incomingDocument);
    $approvingUsers = $confirmation->getApprovingUsers();


    $taskIds = [];
    $relatedSubTasks  = [];
    foreach ($relatedTasks as $relatedTask) {
        $taskIds[] = $relatedTask['id'];
        $relatedSubTasks[] = $relatedTask;
    }

    $documentHasNoTasks = count($relatedTasks) === 0;

    $userId = [];

    if (count($taskIds)) {
        $sql = "SELECT
					v_user_adlar.user_ad,
					v_user_adlar.USERID,
					v_derkenar_elave_shexsler.tip
				FROM
					v_derkenar_elave_shexsler
				LEFT JOIN v_user_adlar ON v_user_adlar.USERID = v_derkenar_elave_shexsler.user_id
				WHERE
					derkenar_id IN (%s) AND USERID IS not NULL 
				GROUP BY v_user_adlar.USERID, v_user_adlar.user_ad, v_derkenar_elave_shexsler.tip";

        $userId = DB::fetchAll(sprintf($sql, implode(',', $taskIds)));

    }
    $confirmation = new Service\Confirmation\Confirmation($incomingDocument);
    $approvingUsersGroup = $confirmation->getApprovingUsersOfGroup(null, 'tanish_ol');

    $formTesdiq = $listOfPrincipals = [];

    $sql = "SELECT
                v_user_adlar.user_ad,
                v_user_adlar.USERID,
                tb2.emeliyyat_tip
            FROM
                tb_prodoc_formlar_tesdiqleme as tb2
            LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.user_id
            WHERE
                tb2.daxil_olan_sened_id = {$poa['document_id']} AND tb2.tip = 'umumi_forma'
          ";
    $formTesdiq = DB::fetchAll(sprintf($sql));

    $sql =  "SELECT 
					tb3.name AS netice_ad
				FROM
					tb_daxil_olan_senedler as tb2
				LEFT JOIN tb_prodoc_neticeler AS tb3 ON tb2.netice = tb3.id
				WHERE
					tb2.id = {$poa['document_id']}
			";
    $netice = DB::fetchColumn(sprintf($sql));

    foreach ($relatedSubTasks as $relatedSubTask) {
        $listOfPrincipals[] = $relatedSubTask['mesul_shexs'];
    }

    foreach ($approvingUsers as $approvingUser) {
        $listOfPrincipals[] = $approvingUser['user_id'];
    }

    foreach ($formTesdiq as $formTesdiqs) {
        $listOfPrincipals[] = $formTesdiqs['USERID'];
    }

    foreach ($relatedTasks as $relatedTask) {
        $listOfPrincipals[] = $relatedTask['mesul_shexs'];
    }

    $proxy = new Proxy($incomingDocument);
    $proxy->setListOfPrincipals($listOfPrincipals);

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';
    require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';

    $key = '';

    if($poa['sened_tip'] == 2){
        $key = 'ds_umumi_forma_icra';
    }elseif ($poa['sened_tip'] == 1){
        if (!is_null($poa['sened_nov_xidmeti_mektub'])){
            $key = 'ds_xidmeti_mektub';
        }else{
            $key = 'ds_umumi_forma_melumat';
        }
    }
    $elementler = getButtonPositionKeys($key);
    $priv = new Privilegiya();
    $senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/umumi_forma/umumi_forma.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}