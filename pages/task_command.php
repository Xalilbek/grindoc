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

$_GET['export'] = "word";

$modalParameters = new modalParameters ();

if((isset($_GET['id']) && $_GET['id']>0) || (isset($_POST['sid']) && $_POST['sid']>0)) {

    if (getProjectName() === ANAMA) {
        $userAdSQLFormat = " user_adi.user_ad_qisa ";
    } else {
        $userAdSQLFormat = " user_adi.user_ad ";
    }

    $userId = (int)$_SESSION['erpuserid'];

    $gid = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['sid'];

    $sql = "
       SELECT
           v_daxil_olan_senedler.id as sened_id,
            ( CASE v_daxil_olan_senedler.sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' ELSE N'Qeyd olunmayıb' END ) AS senedin_tipi,
            $userAdSQLFormat as rey_muellifi_adi,
            ttt.vezife as vezifesi,
            ttt.imza as image,
            tb3.status_changed_at AS imza_tarixi,
            tb2.*,
             v_daxil_olan_senedler.created_at AS date,
             tb2.document_date,
             v_daxil_olan_senedler.document_number,
              tb3.status
        FROM
            v_daxil_olan_senedler
            LEFT JOIN tb_prodoc_task_command as tb2 ON v_daxil_olan_senedler.id= tb2.document_id 
            LEFT JOIN tb_prodoc_testiqleyecek_shexs as tb3 ON tb3.related_record_id = tb2.document_id AND tb3.user_id = tb2.rey_muellifi AND tb3.tip = 'rey_muelifi'
            LEFT JOIN v_users AS ttt ON
             ttt.USERID = tb2.rey_muellifi
             LEFT JOIN v_user_adlar as user_adi ON 
             user_adi.USERID = tb2.rey_muellifi
        WHERE tb2.id = $gid
    ";

    $mInf = pdof()->query($sql)->fetch();

    $kime = [];
    $sql = "SELECT
					DISTINCT $userAdSQLFormat
				FROM
					tb_prodoc_task_command_hesabat_verenler as tb2
				LEFT JOIN v_user_adlar as user_adi ON user_adi.USERID = tb2.kime
				WHERE
					tb2.task_command_id = {$mInf['id']}";
    $kime = DB::fetchColumnArray(sprintf($sql));

    $sql = "SELECT
                $userAdSQLFormat as user_ad,
                derkenar_metn,
                ( CASE WHEN son_icra_tarixi IS NOT NULL THEN CONVERT ( VARCHAR, FORMAT ( son_icra_tarixi, 'dd-MM-yyyy' )) ELSE N'Müraciət olunduqda' END ) AS son_icra_tarixi 
            FROM
                tb_prodoc_task_command_hesabat_verenler AS tb2
                LEFT JOIN v_user_adlar as user_adi ON user_adi.USERID = tb2.kime 
            WHERE
                tb2.task_command_id = {$mInf['id']}";

    $tasks = DB::fetchAll(sprintf($sql));

    $task_components = array();
    foreach ($tasks as $key => $task) {
        $task_components[] = [
            'table_counter' => $key + 1,
            'kime' => $task['user_ad'],
            'tapshiriq' => $task['derkenar_metn'],
            'muddet' => $task['son_icra_tarixi'],
        ];
    }

    $melumat = [];
    $sql = "SELECT
					$userAdSQLFormat
				FROM
					tb_prodoc_testiqleyecek_shexs as tb2
				LEFT JOIN v_user_adlar as user_adi ON user_adi.USERID = tb2.user_id AND tb2.tip = 'tanish_ol'
				WHERE
					tb2.related_record_id = {$mInf['document_id']}";

    $melumat = DB::fetchColumnArray(sprintf($sql));

    $mInf['rey_muellifi'] = $mInf['status'] == 1 ? htmlspecialchars($mInf['rey_muellifi_adi']) : NULL;
    $mInf['senedin_tarixi'] = $mInf['document_date'];
    $mInf['kime'] = implode(",", $kime);
    $mInf['melumat'] = trim(implode(",", $melumat), ",");
    $mInf['image'] =  $mInf['status'] == 1 ? $mInf['image'] : NULL;
    $mInf['imza_tarixi'] = $mInf['status'] == 1 ? date('m/d/Y H:s', strtotime($mInf['imza_tarixi'])) : NULL;

    if (!$mInf) {
        exit('Erize yoxdu');
    }

    $elementler = array();

    $elementler = array_merge($elementler, $mInf);
    $petType = 'task_command';

    $file = $modalParameters->getFileExisting($petType, date('m/d/Y', strtotime($mInf['date'])), $activeTenantId);
    $file_name = $file["file_name"];
    $file_orginal_name = $file["file_original_name"];
    if (isset($_POST['checkStatus']) && !$file_name) {
        exit(json_encode(['status' => 'error', 'errorMsg' => date('d-m-Y', strtotime($mInf['date'])) . ' tarixinə olan export üçün fayl təyin olunmayıb']));
    } else if (isset($_POST['checkStatus']) && $file_name) {
        exit(json_encode(['status' => 'success']));
    }

    $all_data = $elementler;

    if (isset($_GET['export']) && $_GET['export'] == "word") {
        $document = new TemplateProcessor(UPLOADS_DIR_PATH . '/prodoc/formal/' . $file_name);
        $row_num = 0;

        $document->cloneRow('table_row', count($task_components));
        foreach ($task_components as $key => $task) {
            foreach ($task as $key_element => $element) {
                $document->setValue(($key_element) . '#' . ($key + 1), $element);
            }
            $document->setValue('table_row#' . ($key + 1), '');
        }

        foreach ($all_data AS $kee => $el) {
            if (!is_null($el) && $kee === "image") {
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

        $document->saveAs(UPLOADS_DIR_PATH . 'prodoc/formal/export_templates/' . $file_name);

        if (getProjectName() === ANAMA) {
            $fileNameWithoutExtension = basename($file_name);
            Gears\Pdf::convert(
                UPLOADS_DIR_PATH . 'prodoc/formal/export_templates/' . $file_name,
                UPLOADS_DIR_PATH . "prodoc/formal/export_templates/{$fileNameWithoutExtension}.pdf"
            );

            header("Location: " . getProjectBaseUrl("uploads/prodoc/formal/export_templates/{$fileNameWithoutExtension}.pdf"));
        } else {
            header("Location: " . getProjectBaseUrl("uploads/prodoc/formal/export_templates/" . $file_name));
        }
        exit;
    }
}