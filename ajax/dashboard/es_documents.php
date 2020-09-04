<?php

use Model\Dashboard\DashboardFilter;

session_start();
include_once '../../../class/class.functions.php';

$user = new User();

$userId = $user->getSessionUserId();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$filtersSql = [[], [], []];

$tarix1 = date("Y-m-d", strtotime(get('tarix1', '')))=='1970-01-01'?'':date("Y-m-d", strtotime(get('tarix1', '')));
$tarix2 = date("Y-m-d", strtotime(get('tarix2', '')))=='1970-01-01'?'':date("Y-m-d", strtotime(get('tarix2', '')));
if (isset($_POST['Input']) && $_POST['Input'] != "") {
    $InputData = $user->tmzle($_POST['Input']);

    foreach ($InputData as $datas) {

        switch ($datas) {
            case "filter_daxili_senedler":
                $filtersSql[0][] = " document.tip_id = 3 ";
                break;
            case "filter_daxil_olan_senedler":
                $filtersSql[0][] = " document.tip_id = 2 OR document.tip_id = 1 ";
                break;
            case "filter_xaric_olan_senedler":
                $filtersSql[0][] = " document.tip = 'chixan_sened' ";
                break;
            case "collapseTwo_hüquqi-sənəd":
                $filtersSql[1][] = " document.tip_id =  ".Document::TIP_HUQUQI;
                break;
            case "collapseTwo_vətəndaş-müraciəti":
                $filtersSql[1][] = " document.tip_id =  ".Document::TIP_FIZIKI;
                break;
            case "collapseTwo_ümumi-forma":
                $filtersSql[1][] = " document.extra_id = 'umumi_forma' ";
                break;
            case "collapseTwo_i̇cra-edən-şəxsin-dəyişdirilməsi":
                $filtersSql[1][] = " document.extra_id = 'icra_sexsin_deyisdirilmesi' ";
                break;
            case "collapseTwo_i̇cra-müddətinin-dəyişdirilməsi":
                $filtersSql[1][] = " document.extra_id = 'icra_muddeti_deyisdirilmesi' ";
                break;
            case "collapseTwo_xidmət-və-mal-sifarişi-forması":
                $filtersSql[1][] = " document.extra_id = 'satin_alma' ";
                break;
            case "collapseTwo_arayış":
                $filtersSql[1][] = " document.extra_id = 'arayish' ";
                break;
            case "collapseTwo_təqdimat":
                $filtersSql[1][] = " document.extra_id = 'teqdimat' ";
                break;
            case "collapseTwo_e-etibarnamə":
                $filtersSql[1][] = " document.extra_id = 'power_of_attorney' ";
                break;
            case "collapseTwo_sorğu":
                $filtersSql[1][] = " document.muraciet_tip_extra_id = 'sorgu' ";
                break;
            case "collapseTwo_yönləndirmə":
                $filtersSql[1][] = " document.muraciet_tip_extra_id = 'yonlendirme' ";
                break;
            case "collapseTwo_cavab-məktubu":
                $filtersSql[1][] = " document.muraciet_tip_extra_id = 'cavab_mektubu' ";
                break;
            case "collapseTwo_arayış-":
                $filtersSql[1][] = " document.muraciet_tip_extra_id = 'arayis' ";
                break;
            case "collapseTwo_etibarnamə-(yanacaq-doldurulması-barədə)":
                $filtersSql[1][] = " document.muraciet_tip_extra_id = 'etibarname' ";
                break;
            case "collapseTwo_i̇cra-müddətinin-dəyişdirilməsi-":
                $filtersSql[1][] = " document.muraciet_tip_extra_id = 'icra_muddeti' ";
                break;
            case "collapseTwo_etibarnamə":
                $filtersSql[1][] = " document.muraciet_tip_extra_id = 'etibarname_esas' ";
                break;
            case "collapseFive_təsdiqləmə":
                $filtersSql[2][] = "  document.related_key = 'document' AND document.operation = 'yoxlayan_testiq' ";
                $filtersSql[2][] = " document.related_key = 'document' AND document.operation = 'rey_muelifi_testiq' ";
                $filtersSql[2][] = " document.operation = 'approve_rey_muelifi' ";
                $filtersSql[2][] = " document.operation = 'approve_ishtrakchi' ";
                $filtersSql[2][] = " document.operation = 'approve_mesul_shexs' ";
                $filtersSql[2][] = " document.operation = 'approve_kurator' ";
                $filtersSql[2][] = " document.operation = 'approve_tesdiqleme' ";
                $filtersSql[2][] = " document.operation = 'approve_kim_gonderir' ";
                $filtersSql[2][] = " document.operation = 'approve_umumi_shobe' ";
                $filtersSql[2][] = " document.operation = 'approve_sedr' ";
                break;
            case "collapseFive_dərkənar":

                $filtersSql[2][] = " document.operation = 'task_registration' ";
                break;
            case "collapseFive_alt-dərkənar":
                $filtersSql[2][] = " document.operation = 'alt_derkenar' ";
                break;
            case "collapseFive_qeydiyyatdan-keçirtdiyim":
                $filtersSql[2][] = " document.operation = 'outgoing_document_registration' ";
                $filtersSql[2][] = " document.operation = 'document_registration' ";
                $filtersSql[2][] = " document.operation = 'appeal_registration' ";
                break;
            case "collapseFive_i̇mtina":
                $filtersSql[2][] = " document.operation = 'document_state_changed_to_4_by_rey_muellifi' ";
                $filtersSql[2][] = " document.operation = 'document_state_changed_to_4_by_yoxlayan_shexs' ";
                $filtersSql[2][] = " document.operation = 'reject' ";
                $filtersSql[2][] = " document.operation = 'cancel_kim_gonderir' ";
                $filtersSql[2][] = " document.operation = 'cancel_razilashdiran' ";
                $filtersSql[2][] = " document.operation = 'cancel_umumi_shobe' ";
                $filtersSql[2][] = " document.operation = 'cancel_mesul_shexs_testiq' ";
                $filtersSql[2][] = " document.operation = 'cancel_kurator' ";
                $filtersSql[2][] = " document.operation = 'cancel_ishtrakchi' ";
                break;
            case "collapseFive_ləğv-et":
                $filtersSql[2][] = " document.operation = 'legv_et' ";
                break;
            case "collapseFive_tanış-ol":
                $filtersSql[2][] = " document.operation = 'approve_melumatlandirma' ";
                $filtersSql[2][] = " document.operation = 'approve_tanish_ol' ";
                break;
            case "collapseFive_düzəliş":
                $filtersSql[2][] = " document.operation = 'document_edit' ";
                $filtersSql[2][] = " document.operation = 'edit' ";
                $filtersSql[2][] = " document.operation = 'outgoing_document_edit' ";
                break;
            case "collapseFive_şərhlə-bağla":
                $filtersSql[2][] = " document.operation = 'appeal_registration' ";
                break;
            case "collapseFive_razılaşdırma":
                $filtersSql[2][] = " document.operation = 'approve_razilashdiran' ";
                break;
            case "collapseFive_nəticəni-qeyd-et":
                $filtersSql[2][] = " document.operation = 'approve_umumi_shobe_netice' ";
                $filtersSql[2][] = " document.operation = 'approve_qeydiyyatchi_netice' ";
                break;
            case "collapseFive_i̇mzalama":
                $filtersSql[2][] = " document.operation = 'approve_kim_gonderir' ";
                break;
            case "collapseFive_göndər":
                $filtersSql[2][] = " document.operation = 'chixan_sened_gonder' ";
                break;
        }
    }
}

$orFilters1 = implode(' OR ', $filtersSql[0]);
$orFilters2 = implode(' OR ', $filtersSql[1]);
$orFilters3 = implode(' OR ', $filtersSql[2]);
$andFilters = '';
$date_filter = '';
if ($tarix1 != '' && $tarix2 != ''){
    $date_filter = "AND CAST(document.created_at AS date)  >= '$tarix1' AND CAST(document.created_at AS date)  <= '$tarix2'";
}elseif ($tarix1 != '' && $tarix2 == ''){
    $date_filter = "AND CAST(document.created_at AS date)  >= '$tarix1'";
}elseif ($tarix1 == '' && $tarix2 != ''){
    $date_filter = "AND CAST(document.created_at AS date)  <= '$tarix2'";
}


if ($orFilters3 == "") {
    $orFilters3 = "  1 = 1";
}
if (count($filtersSql[0]) >= 1 && count($filtersSql[1]) < 1) {
    $andFilters = " AND ($orFilters1) AND ($orFilters3)";
} else if (count($filtersSql[0]) < 1) {
    $andFilters = "";
} else if (count($filtersSql[0]) >= 1 && count($filtersSql[1]) >= 1) {
    $andFilters = "AND ($orFilters1) AND ($orFilters2) AND ($orFilters3)";
}

$rowCount = get('rowCount', 0);
$senedler = getDocumentsNezaret($andFilters,$date_filter);

ob_start();
require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/es_documents.php';
$html = ob_get_clean();

print json_encode(array("status" => "success", "html" => $html));


function getDocumentsNezaret($filter,$date_filter)
{
    global $userId, $rowCount;
    $myDocuments = "document.operator_id = $userId";
    $priv = new Privilegiya();
    $butun_senedler = $priv->getByExtraId('butun_senedler_emeliyyat');
    $priv_oz_senedleri = $priv->getByExtraId('aid_oldugu_senedler_emeliyyat');


    if(($priv_oz_senedleri == 1 && $butun_senedler == 0) == false){
        $myDocuments = "1=1";
    }

    $limitScroll = "OFFSET " . $rowCount . " ROWS FETCH NEXT 40 ROWS ONLY";

    $sql = "
        SELECT
	* 
    FROM
	(
	SELECT
		* ,
		(
		CASE
				
            WHEN tip_id = 1 THEN N'Hüquqi şəxs' 
            WHEN tip_id = 3 THEN tb1.name 
            WHEN tip_id = 2 THEN N'Vətəndaş müraciəti' 
            WHEN tb1.related_key = 'outgoing_document' THEN N'Çıxan sənəd' 
        END 
        ) AS nov,
        (
        CASE
                
            WHEN tip_id = 1 THEN 'daxil_olan_sened' 
            WHEN tip_id = 3 THEN 'daxil_olan_sened' 
            WHEN tip_id = 2 THEN 'daxil_olan_sened' 
            WHEN tb1.related_key = 'outgoing_document' THEN 'chixan_sened' 
        END 
        ) AS tip 
        FROM
            (
            SELECT
                ( CASE WHEN derkenar_senedler.tip  > 0 THEN derkenar_senedler.tip WHEN related_key = 'appeal' THEN 3 ELSE documents.tip END ) AS tip_id,
                history.related_record_id,
                history.related_key,
                history.operator_id,
                internal_doc_type.extra_id,
                internal_doc_type.name ,
                chixan.muraciet_tip_extra_id ,
                (
                    CASE
                        WHEN derkenar.parentTaskId > 0 AND related_key = 'task' AND operation = 'registration' THEN 'alt_derkenar' 
                        WHEN history.operation= 'registration' 
                            OR history.operation= 'edit' OR history.operation= 'state_changed_to_4_by_rey_muellifi' OR history.operation= 'state_changed_to_4_by_yoxlayan_shexs' 
                            THEN CONCAT ( history.related_key, '_', history.operation ) ELSE history.operation 
                    END 
                ) AS operation,
                (
                    CASE
                        WHEN ( history.related_key = 'document' ) THEN documents.document_number 
                        WHEN history.related_key = 'outgoing_document' THEN chixan.document_number 
                        WHEN history.related_key = 'appeal' THEN documents.document_number 
                        WHEN history.related_key= 'task' THEN documents.document_number 
                    END 
                ) AS document_number,
                (
                    CASE
                        WHEN history.related_key = 'document' THEN
                        documents.id 
                        WHEN history.related_key = 'task' THEN
                        derkenar.daxil_olan_sened_id 										
                        WHEN history.related_key = 'appeal' THEN
                        muraciet.daxil_olan_sened_id 
                        WHEN history.related_key = 'outgoing_document' THEN
                        chixan.id 
                    END 
                ) AS id,
                
                history.created_at AS created_at,
                documents.belong_to,
                documents.created_by,
                documents.rey_muellifi,
                documents.state,
                documents.yoxlayan_shexs,
                documents.umumi_tip
                
                FROM
                    tb_prodoc_history history
                    LEFT JOIN tb_derkenar derkenar ON derkenar.id = history.related_record_id 
                    AND history.related_key = 'task'
                    LEFT JOIN tb_daxil_olan_senedler derkenar_senedler ON derkenar_senedler.id = derkenar.daxil_olan_sened_id
                    LEFT JOIN tb_prodoc_muraciet muraciet ON muraciet.id = history.related_record_id 
                    AND history.related_key = 'appeal'
                    LEFT JOIN v_daxil_olan_senedler_dashboard documents ON ( history.related_record_id = documents.id AND history.related_key = 'document' ) 
                    OR ( history.related_record_id = derkenar.id AND history.related_key = 'task'    AND documents.id = derkenar.daxil_olan_sened_id ) 
                    OR ( history.related_record_id = muraciet.id AND history.related_key = 'appeal' AND documents.id = muraciet.daxil_olan_sened_id )
                    LEFT JOIN v_chixan_senedler chixan ON history.related_record_id = chixan.id 
                    AND history.related_key = 'outgoing_document'
                    LEFT JOIN tb_prodoc_inner_document_type internal_doc_type ON documents.internal_document_type_id = internal_doc_type.id 
            ) tb1 
    ) document 
        WHERE $myDocuments 
             AND operation NOT IN( 'create_approve_group_redakt_eden',	'create_approve_group_visa_veren','create_approve_group_razilashdiran',
            'create_approve_group_chap_eden','create_approve_group_umumi_shobe','create_approve_group_umumi_shobe_nomre','create_approve_group_rey_muelifi','create_approve_group_kim_gonderir',
            'create_approve_group_hesabat_ver_pts','create_approve_group_tesdiqleme','create_approve_group_derkenar','create_approve_group_neticeni_qeyd_eden_sexs',
            'create_approve_group_qiymetlendirme','create_approve_group_melumatlandirma','create_approve_group_sedr','create_approve_group_mesul_shexs_testiq','create_approve_group_kurator',
            'create_approve_group_ishtrakchi','send_general_department','create_approve_group_umumi_shobe_netice','create_approve_group_qeydiyyatchi_netice','document_yoxlamaya_gonderildi',
            'document_rey_muelife_gonderildi','document_rey_muelife_gonderildi', 'create_approve_group_tanish_ol','outgoing_document_registration','create_approve_group_mesul_shexs','rey_muelife_gonderildi',
            'yoxlamaya_gonderildi','state_changed_to_4_by_yoxlayan_shexs ','state_changed_to_4_by_rey_muellifi','rey_muelifi_testiq','ishe_tikilsin') $filter $date_filter ORDER BY created_at DESC $limitScroll";
    return makeTableForOperationDashboard(DB::fetchAllGroupped($sql,'id'));

}



function makeTableForOperationDashboard($data){
    $uniqueData = [];
    foreach ($data as $key => $values){
        $uniqueData[$key] = [];
        $uniqueData[$key]['created_at'] = [];
        $uniqueData[$key]['operation'] = [];
        foreach ($values as $valueKey => $value){
            $uniqueData[$key]['tip'] = $value['tip'];
            $uniqueData[$key]['id'] = $value['id'];
            $uniqueData[$key]['related_record_id'] = $value['related_record_id'];
            $uniqueData[$key]['nov'] = $value['nov'];
            $uniqueData[$key]['document_number'] = $value['document_number'];
            array_push($uniqueData[$key]['created_at'],$value['created_at']);
            array_push($uniqueData[$key]['operation'],$value['operation']);
        }
    }
    return array_filter($uniqueData);
}

