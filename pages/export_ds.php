<?php
session_start();
include_once '../../class/class.functions.php';
$user = new User();

if(!$user->get_session())
{
    header("Location: login.php");
    exit;
}
$activeTenantId = $user->getActiveTenantId();
require_once DIRNAME_INDEX.'/vendor/autoload.php';

use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Gears\Pdf;
$_GET['export'] = "word";

if (getProjectName() === ANAMA) {
    $userAdSQLFormat = " CONCAT ( Adi, ' ', Soyadi ) ";
} else {
    $userAdSQLFormat = " CONCAT ( Adi, ' ', Soyadi, ' ', AtaAdi ) ";
}

$modalParameters = new modalParameters ();

if(
    ((isset($_GET['id']) && $_GET['id']>0) || (isset($_POST['sid']) && $_POST['sid']>0))&&
    ((isset($_GET['document_id']) && $_GET['document_id']>0) || (isset($_POST['document_id']) && $_POST['document_id']>0))
) {

    $gid = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['sid'];
    $document_id = isset($_GET['document_id']) ? (int)$_GET['document_id'] : (int)$_POST['document_id'];

    $incomingDocument = new Document($document_id);
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $tip = InternalDocument::getExtraIdById((int)$incomingDocument->getData()['internal_document_type_id']);


    $sql = "
		SELECT
			tb1.*,
			(
				SELECT
					$userAdSQLFormat
				FROM
					tb_users
				WHERE
					USERID = tb1.created_by
			) AS created_by_name,
			(
				SELECT
					imza
				FROM
					v_users
				WHERE
					USERID = tb1.created_by
			) AS image,
			(
				SELECT
					$userAdSQLFormat
				FROM
					tb_users
				WHERE
					USERID = tb2.belong_to
			) AS belong_to,
			(SELECT  
			      struktur_bolmesi 
			      from v_users 
			      where USERID=tb1.created_by) 
			      as teshkilat,
			(SELECT  
			      vezife 
			      from v_users 
			      where USERID=tb1.created_by) 
			      as vezife,
			tb1.status,
			tb1.created_at as [date],
			convert(varchar,tb1.created_at,105) as	created_at,
			convert(varchar,tb1.senedin_tarixi,105) as	senedin_tarix
		FROM
			v_daxil_olan_senedler_corrected AS tb1
		LEFT JOIN tb_daxil_olan_senedler AS tb2
		 ON tb1.id=tb2.id	
		WHERE tb1.id = '$document_id'
	";

    $docInfo = DB::fetch($sql);

    require_once 'propertiesForType.php';
    if (isset(dataForType($document_id)[$tip])) {
        $sql = dataForType($document_id)[$tip]['query'];

        $senedler = DB::fetch($sql);
    } else $senedler = array();

    $senedler = array_merge($senedler, $docInfo);

    if (!$senedler) {
        exit('Erize yoxdu');
    }

    $elementler = array();
    $elementler = array_merge($elementler, $senedler);
    $ixrac_tip = $tip;

    if ($tip === 'satin_alma') {
        $sifaris_tipi = DB::fetchColumn("SELECT sifaris_tipi FROM tb_prodoc_satinalma_sifaris WHERE document_id = '$document_id'");
        $ixrac_tip = $tip . '_' . $sifaris_tipi;
    }
    if ($tip === 'create_act') {
        $sifaris_tipi = DB::fetchColumn("SELECT act_type FROM tb_prodoc_aktlar WHERE document_id = '$document_id'");
        $ixrac_tip = $sifaris_tipi;
    }

    $file = $modalParameters->getFileExisting($ixrac_tip, date('m/d/Y', strtotime($senedler['date'])), $activeTenantId);

    $file_name = $file["file_name"];
    $file_orginal_name = $file["file_original_name"];

    if (isset($_POST['checkStatus']) && !$file_name) {
        exit(json_encode(['status' => 'error', 'errorMsg' => date('d-m-Y', strtotime($senedler['date'])) . ' tarixinə olan export üçün fayl təyin olunmayıb']));
    } else if (isset($_POST['checkStatus']) && $file_name) {
        exit(json_encode(['status' => 'success']));
    }

    $all_data = $elementler;

    $document = new TemplateProcessor(UPLOADS_DIR_PATH . '/prodoc/formal/' . $file_name);

    if ($tip == "hesabat_yarat") {

        $intDoc = new InternalDocument($all_data['document_id']);
        $intDoc->setCustomQuery('v_daxil_olan_senedler_corrected', true);
        $relatedDoc = $intDoc->getRelatedDocuments()[0];
        $tapshiriq_number = $relatedDoc['document_number'];

        require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
        $RM = tapsiriqEmrinReyMuelifi($relatedDoc['id']);

        $sql = "SELECT
            TOP 1
            v_user_adlar.imza
        FROM
            tb_prodoc_icrachi_shexsler AS tb2
            LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.user_id
        WHERE
            tb2.sened_tip = 3 AND tb2.icrachi_tip = 'rey_muellifi' ";

        $tasks = DB::fetchColumn(sprintf($sql));

        $all_data['tesdiq_olundu'] = $RM['tesdiq_olundu'];
        $all_data['tesdiq_tarixi'] = $RM['tesdiq_tarixi'];
        $all_data['iscinin_imzasi'] = $tasks;


        $otherComponents = otherComponents($gid, $tip, $document_id);
        $all_data = array_merge($all_data, $otherComponents);
        $table_data = hasTable($gid, $tip, $document_id);

        makeTableRow($table_data);
    } elseif ($tip == "satin_alma") {
        $otherComponents = otherComponents($gid, $tip, $document_id);
        $all_data = array_merge($all_data, $otherComponents);
        $table_data = hasTable($gid, $tip, $document_id);

        makeTableRow($table_data);
    }
    elseif ($tip=="create_act")
    {

        $sql="
        SELECT  CONCAT(Adi, ' ', Soyadi) as user_name
        FROM tb_prodoc_akt_imzalayan_shexsler imzalayan_shexsler
               LEFT JOIN v_users ON v_users.USERID = imzalayan_shexsler.user_id
        WHERE document_id = ".$all_data['document_id'];

        $imzalayan_shexsler = DB::fetchAll($sql);
        $imzalayanlar="";


        for ($i=0; $i<count($imzalayan_shexsler);$i++){
            if ($i==0){
                $imzalayanlar.=$imzalayan_shexsler[$i]['user_name'];
            }
            else{
                $imzalayanlar.=', '.$imzalayan_shexsler[$i]['user_name'];
            }


        }

        $otherComponents = otherComponents($gid,$tip,$document_id);
        $all_data= array_merge($all_data,$otherComponents);

        $table_data=hasTable($gid,$tip,$document_id);
        $ordersOfElements=hasOrders($gid,$tip,$document_id);
        makeTableRow($table_data);
        makeTableRow($ordersOfElements,'order_row');

    } elseif ($tip == "elave_razilashdirma") {
        $all_data['emeqhaqqina_elave'] = $user->mebleq_sozle($all_data['emeqhaqqina_elave']) . " " . $all_data['emeqhaqqina_elave_valyuta_ad'];

        $sql = "SELECT
                TOP 1
                v_user_adlar.user_ad,
                v_user_adlar.imza
            FROM
                tb_prodoc_icrachi_shexsler AS tb2
                LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.user_id
            WHERE
                tb2.sened_tip = 3 AND tb2.icrachi_tip = 'rey_muellifi' ";

        $task = DB::fetch(sprintf($sql));

        $all_data['ise_goturen_adi'] = $task['user_ad'];
        $all_data['iscinin_imzasi'] = $task['imza'];
    }

    $row_num = 0;
    foreach ($all_data AS $kee => $el) {
        if (!is_null($el) && ($kee === "image" || $kee === 'emekdash_image' || $kee === "iscinin_imzasi")) {
            $document->setImg(
                $kee,
                array(
                    'src' => UPLOADS_DIR_PATH . '/imzalar/' . $el,
                    'swh' => '70',
                    'size' => array(0 => 70, 1 => 70)
                )
            );
        } else {
            $document->setValue($kee, $el);
        }
    }

    $document->saveAs(UPLOADS_DIR_PATH. 'prodoc/formal/export_templates/'.$file_name);
    if (getProjectName() === ANAMA) {
        $fileNameWithoutExtension = basename($file_name);
        Gears\Pdf::convert(
            UPLOADS_DIR_PATH. 'prodoc/formal/export_templates/'.$file_name,
            UPLOADS_DIR_PATH . "prodoc/formal/export_templates/{$fileNameWithoutExtension}.pdf"
        );

        header("Location: ". getProjectBaseUrl("uploads/prodoc/formal/export_templates/{$fileNameWithoutExtension}.pdf"));
    } else {
        $pgp = new Pdf(UPLOADS_DIR_PATH. 'prodoc/formal/'.$file_name);
        $pgp->stream();
    }

    exit;
}

function makeTableRow($table_count, $row='table_row')
{
    global $document;

    if (count($table_count)>0){
        $document->cloneRow($row, count($table_count));

        foreach ($table_count as $key=>$task){
            foreach ($task as $key_element=>$element){
                $document->setValue(($key_element).'#'.($key+1),$element);
            }
            $document->setValue( $row.'#'.($key+1),'');
        }
    }
}

