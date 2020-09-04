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
    $tip =isset($_GET['tip'])? (int)$_GET['tip']: (isset($_POST['tip']) ?(int)$_POST['tip'] :0);

    if(isset($tip)&& $tip==2){

        $mInf = pdof()->query("SELECT
                                            tb1.*,
                                            tb3.Soyadi,
                                            tb3.Adi,
                                            tb3.AtaAdi,
                                            tb1.created_at as created_at,
                                            tb1.gonderen_teshkilat_ad as organization_name,
                                            CONVERT(date, tb1.arayis_tarixi) AS certificate_date,
                                            tb3.vezife as position_name,
                                            document_number	
                                        FROM
                                            v_chixan_senedler tb1
                                            LEFT JOIN v_users tb3 ON USERID = arayis_user_id
                                        WHERE
                                            tb1.id='$gid'  ")->fetch();
    }
    else{
        $mInf = pdof()->query("SELECT
                                        tb1.*,
                                        tb3.Soyadi,
                                        tb3.Adi,
                                        tb3.AtaAdi,
                                        tb1.date as created_at,
                                        tb4.name as organization_name,
                                        CONVERT(date, tb1.[date]) AS certificate_date,
                                        tb3.vezife as position_name,
                                        tb5.document_number as  document_number
                                    FROM
                                        tb_prodoc_certificate tb1
                                        LEFT JOIN v_users tb3 ON USERID = employe
                                        LEFT JOIN tb_prodoc_certificate_organizations tb4 ON organization_id = tb4.id
                                        LEFT JOIN v_daxil_olan_senedler_corrected tb5 ON tb1.document_id = tb5.id 
                                    WHERE
                                        tb1.id='$gid'  ")->fetch();
    }



    if(!$mInf)
    {
        exit('Erize yoxdu');
    }
    $testiqedecekler = 	dil::soz("11testiqleyenler");

    $elementler=array();

    $elementler = array_merge($elementler,$mInf);

    $petType = 'arayish';

    $file = $modalParameters -> getFileExisting($petType,date('m/d/Y',strtotime($mInf['date'])),$activeTenantId);
    $file_name = $file["file_name"];
    $file_orginal_name = $file["file_original_name"];
    if(isset($_POST['checkStatus'])&&!$file_name){

        exit(json_encode(['status'=>'error','errorMsg' => date('d-m-Y',strtotime($mInf['created_at'])).' tarixinə olan export üçün fayl təyin olunmayıb']));


    }
    else if(isset($_POST['checkStatus'])&&$file_name){
        exit(json_encode(['status'=>'success']));

    }

    $all_data = $modalParameters -> makeFinalParametersArray($petType,$elementler);

    if(isset($_GET['export']) && $_GET['export']=="word")
    {
        $document = new TemplateProcessor(UPLOADS_DIR_PATH . '/prodoc/formal/'.$file_name);
        $row_num = 0;
        foreach($all_data AS $kee => $el)
        {
            $document->setValue($kee,$el);
        }


        $document->saveAs(UPLOADS_DIR_PATH . 'prodoc/formal/export_templates/'.$file_name);

        header("Location: " . getProjectBaseUrl("uploads/prodoc/formal/export_templates/".$file_name));
        exit;
    }

}