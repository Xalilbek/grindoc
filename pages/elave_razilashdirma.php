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

if((isset($_GET['id']) && $_GET['id']>0) || (isset($_POST['sid']) && $_POST['sid']>0))
{

    $userId = (int)$_SESSION['erpuserid'];

    $gid =isset($_GET['id'])? (int)$_GET['id']: (int)$_POST['sid'];

    $sql = "
       SELECT
           v_daxil_olan_senedler.id as sened_id,
            ( CASE tb2.sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' ELSE N'Qeyd olunmayıb' END ) AS senedin_tipi,
            ( SELECT name FROM tb_prodoc_nazalogiya WHERE id = tb2.qisa_mezmun ) AS qisa_mezmun,
            rey_muellifi_ad,
            ttt.user_name,
            ttt.vezife,
            v.ad AS emeqhaqqina_elave_valyuta_ad,
            tb2.*,
             v_daxil_olan_senedler.created_at AS date,
             v.ad as valyuta_ad,
             v_daxil_olan_senedler.document_number
        FROM
            v_daxil_olan_senedler
            LEFT JOIN tb_prodoc_elave_razilashdirma as tb2 ON v_daxil_olan_senedler.id= tb2.document_id 
            LEFT JOIN v_users AS ttt ON
             ttt.USERID = tb2.emekdash
             LEFT JOIN tb_valyuta As v
             ON v.id = tb2.emeqhaqqina_elave_valyuta
        WHERE tb2.id = $gid
    ";

    $mInf = pdof()->query($sql)->fetch();

    $mInf['emeqhaqqina_elave_ve_valyuta'] = $user->mebleq_sozle($mInf['emeqhaqqina_elave']) . " " . $mInf['valyuta_ad'];

    if(!$mInf)
    {
        exit('Erize yoxdu');
    }

    $elementler=array();

    $elementler = array_merge($elementler,$mInf);

    $petType = 'elave_razilashdirma';

    $file = $modalParameters -> getFileExisting($petType,date('m/d/Y',strtotime($mInf['date'])),$activeTenantId);
    $file_name = $file["file_name"];
    $file_orginal_name = $file["file_original_name"];

    if(isset($_POST['checkStatus'])&&!$file_name){

        exit(json_encode(['status'=>'error','errorMsg' => date('d-m-Y',strtotime($mInf['date'])).' tarixinə olan export üçün fayl təyin olunmayıb']));


    }
    else if(isset($_POST['checkStatus'])&&$file_name){
        exit(json_encode(['status'=>'success']));

    }

    $all_data = $elementler;

    if(isset($_GET['export']) && $_GET['export']=="word")
    {
        $document = new TemplateProcessor(UPLOADS_DIR_PATH . '/prodoc/formal/'.$file_name);
        $row_num = 0;
        foreach($all_data AS $kee => $el)
        {
            $document->setValue($kee,$el);
        }

        $document->saveAs(UPLOADS_DIR_PATH . 'prodoc/formal/export_templates/'.$file_orginal_name);

        header("Location: " . getProjectBaseUrl("uploads/prodoc/formal/export_templates/".$file_name));
        exit;
    }

}