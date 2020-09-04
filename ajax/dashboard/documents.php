<?php

use Model\Dashboard\DashboardFilter;

session_start();
include_once '../../../class/class.functions.php';
include_once '../../includes/DashboartFilter.php';
require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/Dashboard/DashboardFilter.php';

$user = new User();

$userId = $user->getSessionUserId();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$doc_type = get('doc_type', '');

$search_document = get('sened_axtar', '');
$checkedFilters = get('InputData', '');
$nezaretFilters = get('Input', '');
$getNezaret = get('nezaretde', '');
$search_document_nezaret = get('nezaret_sened_axtar', '');
$rowCount = get('rowCount', 0);

$search_value = " ";
if ($search_document != "") {
    $search_document = DB::quote('%' . $search_document . '%');
    $search_value .= " AND ( document_number LIKE $search_document OR dd.nov LIKE N$search_document OR dd.created_at LIKE $search_document OR dd.qeyd LIKE N$search_document) ";
}

$search_value_nezaret = " ";
if ($search_document_nezaret != "") {
    $search_document_nezaret = DB::quote('%' . $search_document_nezaret . '%');
    $search_value_nezaret .= " AND ( document_number LIKE $search_document_nezaret OR 
(CASE WHEN tip = 1 THEN N'Hüquqi şəxs' WHEN tip = 3 THEN internal_doc_type.name ELSE N'Vətəndaş müraciəti' END) LIKE $search_document_nezaret OR 
created_at LIKE $search_document_nezaret) ";
}

$bolme = isset($_POST['checkNotify']) && is_string($_POST['checkNotify']) ? $_POST['checkNotify'] : "dd.created_at";
$forNotify = isset($_POST['forNotify']) && is_string($_POST['forNotify']) ? $_POST['forNotify'] : "dd.created_at";
$orderBy = "";
$whereElaqeliSened = "";
$whereChixanSened = "";
switch ($bolme) {
    case "protask":
    case "neticeni_qeyd_eden_sexs_satin_alma":
    case "ds_imtina_qeydiyyatciya_satin_alma":
    case "prodoc_sened_qeydiyyatdan_kecib":
    case "prodoc_yoxlamaya_gonderilib":
    case "yoxlayici_imtina_etdi":
    case "derkenar_gonderdi":
    case "derkenar_gonderildi":
    case "icraya_gonderildi":
    case "alt_derkenar_gonderdi":
    case "alt_derkenar_gonderildi":
    case "sorguya_cavab":
    case "ishtirakchi":
    case "ishtirakchi_derkenar":
    case "melumat":
    case "ishe_tik":
    case "ishe_tik_qeydiyyatci":
    case "kurasiya":
    case "sorguya_cavab_kurator":
    case "prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra_nomre":
    case "senedi_baglandi":
    case "senedi_baglaya_bilersiz":
    case "tesdiqleme":
    case "tesdiqleme_satin_alma":
    case "derkenar":
    case "viza":
    case "melumatlandirma":
    case "qiymetlendirme":
    case "neticeni_qeyd_eden_sexs":
    case "tesdiq_sifaris":
    case "task_command_kime":
    case "mesul_shexs_yeni":
    case "ds_imtina_qeydiyyatciya":
    case "tanish_ol_yeni":
    case "tanish_ol":
    case "prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra":
        $orderBy = "case when dd.tip='daxil_olan_sened' AND dd.id=" . $forNotify . " then DATEADD(YEAR, 1, GETDATE()) else dd.created_at end";
        $whereElaqeliSened = " document.id = " . $forNotify . " OR ";
        break;
    case "kurator":
    case "ishtrakchi":
    case "kurator_imtina":
    case "razilashdiran":
    case "redaktor":
    case "razilashdiran_imtina":
    case "mesul_shexs_imtina_etdi":
    case "redakt_eden_imtina":
    case "redakt_eden":
    case "visa_veren":
    case "visa_veren_imtina":
    case "umumi_shobe":
    case "umumi_shobe_6":
    case "rey_muelifi":
    case "chap_eden":
    case "chap_eden_imtina":
    case "created_by_canceled":
    case "rey_muellifi_canceled":
    case "to_created_by":
    case "kim_gonderir":
    case "kim_gonderir_2":
    case "aidiyyati_orqan":
    case "tabeli_qurum":
    case "fiziki_shexs":
    case "aidiyyati_orqan_qeydiyyatci":
    case "tabeli_qurum_qeydiyyatci":
    case "fiziki_shexs_qeydiyyatci":
    case "chixan_sened_legv":
        $orderBy = "case when dd.tip='chixan_sened' AND dd.id=" . $forNotify . " then DATEADD(YEAR, 1, GETDATE()) else dd.created_at end";
        $whereChixanSened = " document.id = " . $forNotify . " OR ";
        break;
    default:
        $orderBy = "dd.created_at";
        break;
}
$tab = isset($_POST['tab']) && is_string($_POST['tab']) && strlen($_POST['tab'])
    ? $_POST['tab'] : 'butun_senedler';

$tabsForCountForNezaret = isset($_POST['tabsForCount']) && is_string($_POST['tabsForCount']) && strlen($_POST['tabsForCount'])
    ? @json_decode($_POST['tabsForCount']) : [];

$tabsForCount = isset($_POST['tabsForCount']) && is_string($_POST['tabsForCount']) && strlen($_POST['tabsForCount'])
    ? $_POST['tabsForCount'] : '';

$nezaret_sehifesi = isset($_POST['nezaret_sehifesi']) && is_string($_POST['nezaret_sehifesi'])
    ? $_POST['nezaret_sehifesi'] : '0';

$filterIcrachiForDosStr = getFilter('dos', $checkedFilters,$userId);
$filterIcrachiForXosStr = getFilter('xos', $checkedFilters,$userId);
$generalFilter = getFilter('general', $checkedFilters,$userId);
$generalFilterNezaret = getFilterNezaret($nezaretFilters);

$filterForPoaOperation = documentsForPoaOperations($userId);
$selectForPoaOperation = "

, derkenar.mesul_shexs
, (CASE WHEN derkenar.mesul_shexs <> ".$userId." AND derkenar.id IS NOT NULL THEN  CONCAT('\"(VK)-', (SELECT CONCAT( LEFT( Soyadi, 1), '. ', Adi ) FROM v_users WHERE USERID = derkenar.mesul_shexs ) ,'\"' ) ELSE '' END) as vekaletname
";


$forNotInPoaDocuments = "
                 SELECT alt_priv.string_id
    FROM TenantUserAuthentication
    
           LEFT JOIN tb_prodoc_role_privilegiyalar ON role_id = ProdocGroupId
           LEFT JOIN tb_prodoc_alt_privilegiyalar alt_priv ON alt_priv.id = alt_privilegiya_id
    WHERE TenantUserId = $userId
      AND privilegiya = 1
        ";

$tabPrivs = DB::fetchColumnArray($forNotInPoaDocuments);

$forNotInPoaDocuments = count($tabPrivs)>0? "OR dd.extra_id IN (".DB::arrayToSqlList($tabPrivs).") " : " AND 1=1 ";

$is_order_module = ($doc_type != '') ? " AND extra_id= " . DB::quote($doc_type) . " " : " AND (extra_id <> 'satin_alma' OR extra_id is null) ";

if ($nezaret_sehifesi) {

    if (is_array($tabsForCountForNezaret) && count($tabsForCountForNezaret) > 0) {
        $response = array("status" => "success", "count" => []);
        foreach ($tabsForCountForNezaret as $tab) {
            $response['count'][$tab] = getDocumentsNezaret($tab, true);
        }
        print json_encode($response);
        exit();
    } else {
        $senedler = getDocumentsNezaret($tab);
    }

} else {
    $isDashboard = "";

    if (!empty($tabsForCount)) {
        $response = array("status" => "success", "count" => []);

        $response['count'][$tabsForCount] = getDocuments($tabsForCount, true);

        print json_encode($response);
        exit();
    } else {
        $senedler = getDocuments($tab);
    }
}

ob_start();
require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/documents.php';
$html = ob_get_clean();

print json_encode(array("status" => "success", "html" => $html));

function getDocuments($tab, $count = false)
{
    global $userId;
    global $filterForPoaOperation;
    global $selectForPoaOperation;
    global $filterIcrachiForDosStr;
    global $filterIcrachiForXosStr;
    global $search_value;
    global $generalFilter;
    global $orderBy;
    global $rowCount;
    global $whereElaqeliSened;
    global $whereChixanSened;
    global $is_order_module;
    global $forNotInPoaDocuments;

    require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
    $priv = (new Privilegiya())->getByExtraId($tab);

    if ($priv === 0) {
        print json_encode(array("status" => "success", "html" => ""));
        exit();
        throw new Exception('You have no access to the tab');
    }

    $dashboardFilter = new Model\Dashboard\DashboardFilter($userId);
    $filters = $dashboardFilter->getFiltersByName($tab);

    $selectList = "";
    $limitScroll = "";
    if ($count) {
        $selectList = " 
        COUNT(dd.id) AS C 
    ";

        $orderBySQL = '';
        $limitScroll = '';
    } else {


        if (getProjectName() === TS) {
            $selectList = "
            dd.id,
            dd.status,
            dd.state,
            dd.muraciet_tipi,
            dd.sened_tip,
            dd.extra_id,
            (CASE WHEN dd.number = '-' THEN v_prodoc_document_number.document_number
             ELSE dd.number END)
             AS nomre,
            dd.tip,
            (CASE WHEN dd.sened_tip=2 THEN dd.son_icra_tarix ELSE '-' END ) as son_icra_tarix,
            dd.qeyd,
            dd.created_at,
            dd.sened_novu,
            dd.mektub_nezaretdedir,
            dd.rey_muellifi
            $selectForPoaOperation
        ";
        } else {
            $selectList = "
        dd.id,
        dd.status,
        dd.state,
        dd.muraciet_tipi,
        dd.sened_tip,
        dd.extra_id,
        (CASE WHEN dd.number = '-' THEN v_prodoc_document_number.document_number
             ELSE dd.number END)
             AS nomre,
       dd.tip,
        dd.nov,
        dd.qeyd,
        dd.created_at,
        dd.sened_novu,
        dd.mektub_nezaretdedir,
        dd.rey_muellifi,
        dd.tekrar_eyni
        $selectForPoaOperation
        
    ";
        }

        $orderBySQL = "order by (" . $orderBy . ") desc";
        $limitScroll = "OFFSET " . $rowCount . " ROWS FETCH NEXT 40 ROWS ONLY";
    }

    $sql = "
    Select 
        $selectList
    from (
       SELECT
         document.id,
         document.mektub_nezaretdedir as mektub_nezaretdedir,
         tip as muraciet_tipi,
          (CASE
        WHEN (tip = 3 AND internal_doc_type.extra_id = 'create_act') THEN (SELECT document_number_id
                                                                           FROM v_daxil_olan_senedler
                                                                           WHERE id = (SELECT TOP 1 task_command_id
                                                                                       FROM tb_prodoc_aktlar
                                                                                       WHERE document_id = document.id)
        )
        ELSE document.document_number_id END)                          AS nomre,
         'daxil_olan_sened' AS tip,
         (CASE tip
                WHEN 1 THEN 'dos'
                WHEN 2 THEN 'dos'
                ELSE 'ds'
            END)
             AS [sened_novu],
         (CASE WHEN tip = 1 THEN N'Hüquqi şəxs' WHEN tip = 3 THEN internal_doc_type.name ELSE N'Vətəndaş müraciəti' END) AS nov,
         document.created_at,
         (CASE  WHEN tip = 3 THEN internal_doc_type.parent_id ELSE tip END) as  senedin_novu,
         
         hardan_daxil_olub as haradan_daxil_olub,
         gonderen_teshkilat as gonderen_teshkilatDos,
         (CASE WHEN state= ".Document::STATE_IN_TRASH." THEN state ELSE 
            status END ) as status,
            state,
         internal_doc_type.extra_id,     
         mektubun_tipi as mektub_tipi,
         rey_muellifi  as rey_muellifi,
         (CASE  WHEN tb_f.tekrar_eyni = 2 THEN N'Eyni'
                WHEN tb_f.tekrar_eyni = 1 THEN N'Təkrar' 
                ELSE '' END) as  tekrar_eyni,
         rey_muellifi  as icrachi,
         '-'  as  teyinat,
         '-'  as hara_gonderilib,
         internal_document_type_id as alt_nov,
         sened_tip as sened_tip,
         qeyd,
         document.document_number as number,  
         is_deleted,   
         CONVERT(nvarchar(10), document.icra_edilme_tarixi, 105) as son_icra_tarix,    
         document.belong_to

       FROM v_daxil_olan_senedler_dashboard AS document
       LEFT JOIN tb_prodoc_inner_document_type AS internal_doc_type
        ON internal_doc_type.id = document.internal_document_type_id
        LEFT JOIN tb_daxil_olan_senedler_fiziki  as tb_f
        on document.id=daxil_olan_sened_id
        LEFT JOIN tb_prodoc_inner_document_type 
        on document.internal_document_type_id=tb_prodoc_inner_document_type.id

       WHERE $whereElaqeliSened ({$filters['document']}   $filterIcrachiForDosStr)

       UNION

       SELECT
           document.id,
           '' as mektub_nezaretdedir,
           '-' as muraciet_tipi,
           document_number_id AS nomre,
           'chixan_sened' AS tip,				 
           'xos' AS [sened_novu],
           N'Çıxan sənəd' AS nov,
           created_at,
           muraciet_tip_id as senedin_novu,
          '-' as haradan_daxil_olub,
          '-' as gonderen_teshkilatDos,
          (CASE WHEN status= ".OutgoingDocument::STATUS_LEGV_OLUNUB." THEN status ELSE 
            (CASE WHEN is_sended IS NULL OR is_sended=0 THEN 0 ELSE 1 END) END ) as status,
          NULL as state,
          '-' as extra_id,   
          '-' as mektub_tipi,
          '-' as rey_muellifi,
          '-' as tekrar_eyni,
          document.created_by as icrachi,
          teyinat ,
          gonderen_teshkilat as hara_gonderilib,
          '-' as alt_nov,
          '-' as sened_tip,
          qeyd as qeyd,
          '-' as number,    
          is_deleted,
          '-' as son_icra_tarix,
          NULL AS belong_to
          
       FROM v_chixan_senedler AS document
       WHERE $whereChixanSened ({$filters['outgoing_document']} AND (document.is_deleted IS NULL OR document.is_deleted = 0) $filterIcrachiForXosStr)

  ) dd
  LEFT JOIN v_prodoc_document_number
    ON nomre=v_prodoc_document_number.id
    $filterForPoaOperation
  WHERE 1=1 AND
  ( (dd.extra_id <> 'power_of_attorney' OR dd.extra_id IS NULL) $forNotInPoaDocuments )
  AND dd.is_deleted=0  $search_value  $is_order_module AND $generalFilter $orderBySQL  $limitScroll
";



    if ($count) {
        return (int)DB::fetchColumn($sql);
    } else {
        return DB::fetchAll($sql);
    }
}

function getDocumentsNezaret($tab, $count = false)
{
    global $userId;
    global $generalFilterNezaret;
    global $getNezaret;
    global $rowCount;
    global $search_value_nezaret;
    $myDocuments = "1=1";
    $tarix = getFillerTarix();

    $VaxtKecibQalib = isset($_POST['filt_vaxt']) && is_string($_POST['filt_vaxt']) ? $_POST['filt_vaxt'] : 'vaxti_kecib';
    $nezaretFiller = ($getNezaret == 1 ? "AND document.mektub_nezaretdedir='1' " : '');
    $fillerVaxtKecibQalib = ($VaxtKecibQalib == "vaxti_qalib" ? "DATEDIFF(MINUTE, GETDATE(), $tarix) > 0" : "DATEDIFF(MINUTE, GETDATE(), $tarix) < 0");
    $icra_muddeti = "ABS((DATEDIFF(MINUTE, $tarix, GETDATE()) / (60.0 * 24.0)))";
    $filters = getFilletler($icra_muddeti);

    require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
    $priv = new Privilegiya();
    $butun_senedler = $priv->getByExtraId('butun_senedler_nezaret');
    $priv_oz_senedleri = $priv->getByExtraId('aid_oldugu_senedler_nezaret');
    $dashboardFilter = new Model\Dashboard\DashboardFilter($userId);

    if($priv_oz_senedleri == 1 && $butun_senedler == 0){
        $myDocuments = $dashboardFilter->getFiltersByName('butun_senedler')['document'];
    }

    $filtersTab = array_key_exists($tab, $filters) ? $filters[$tab] : '1 <> 1';

    $selectList = "";
    $orderBySQL = '';
    $limitScroll = '';
    if ($count) {
        $selectList = " 
        COUNT(document.id) AS C 
    ";
        $limitScroll = '';
    } else {
        $selectList = " 
         document.status,
         document.id,
         document_number AS nomre,
         'daxil_olan_sened' AS tip,
         (CASE WHEN icra_edilme_tarixi IS NULL THEN son_icra_tarixi ELSE icra_edilme_tarixi END) AS tarix,
         (CASE WHEN tip = 1 THEN N'Hüquqi şəxs' WHEN tip = 3 THEN internal_doc_type.name ELSE N'Vətəndaş müraciəti' END) AS nov,
         created_at,
         (CASE tip
                WHEN 1 THEN 'dos'
                WHEN 2 THEN 'dos'
                ELSE 'ds'
            END)
             AS [sened_novu],
        {$icra_muddeti} AS icra_muddeti,
        document.rey_muellifi,
        document.mektub_nezaretdedir as mektub_nezaretdedir
    ";
        $orderBySQL = "ORDER BY created_at DESC";
        $limitScroll = "OFFSET " . $rowCount . " ROWS FETCH NEXT 40 ROWS ONLY";
    }

    $sql = "
   SELECT
     $selectList
   FROM v_daxil_olan_senedler_dashboard AS document
   LEFT JOIN tb_prodoc_inner_document_type AS internal_doc_type
   ON internal_doc_type.id = document.internal_document_type_id
   WHERE {$myDocuments} AND
   {$filtersTab} $search_value_nezaret AND document.status='1' AND (document.icra_edilme_tarixi IS NOT NULL OR document.son_icra_tarixi IS NOT NULL) 
   AND {$fillerVaxtKecibQalib} AND $generalFilterNezaret AND (document.is_deleted IS NULL OR document.is_deleted = 0) AND document.is_deleted=0 AND document.sened_tip = 2 $nezaretFiller
   $orderBySQL $limitScroll";

    if ($count) {
        return (int)DB::fetchColumn($sql);
    } else {

        return DB::fetchAll($sql);
    }
}

function getFillerTarix()
{
    global $nezaretFilters;
    $fillName = [];
    $tarix = '';

    if ($nezaretFilters != '') {
        foreach ($nezaretFilters as $key => $filter) {
            $filter = json_decode($filter, true);
            if ($filter["filterler_tipi"] == 'filterler') {
                if ($filter["key"] == 'teleb_olunan_tarix') {
                    $fillName[] = 'icra_edilme_tarixi';
                } else if ($filter["key"] == 'son_icra_tarixi') {
                    $fillName[] = 'son_icra_tarixi';
                }
            }
        }
    }

    if ((in_array('icra_edilme_tarixi', $fillName) && in_array('son_icra_tarixi', $fillName)) || empty($nezaretFilters)) {
        $tarix = '(CASE WHEN icra_edilme_tarixi IS NULL THEN son_icra_tarixi ELSE icra_edilme_tarixi END)';
    } else if (in_array('icra_edilme_tarixi', $fillName)) {
        $tarix = 'icra_edilme_tarixi';
    } else if (in_array('son_icra_tarixi', $fillName)) {
        $tarix = 'son_icra_tarixi';
    }

    return $tarix;
}

function getFilletler($icra_muddeti)
{
    $filters = array(
        'butun_senedler' => " 1 = 1 ",
        'beraber_bire' => " $icra_muddeti <= 3",
        'araliq_onbesden' => " ($icra_muddeti <= 10 AND $icra_muddeti > 3)",
        'coxdu_onbesden' => " $icra_muddeti > 10 ",
    );

    return $filters;
}
