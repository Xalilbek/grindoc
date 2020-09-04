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
    if (getProjectName() === ANAMA) {
        $userAdSQLFormat = " CONCAT ( Adi, ' ', Soyadi ) ";
    } else {
        $userAdSQLFormat = " CONCAT ( Adi, ' ', Soyadi, ' ', AtaAdi ) ";
    }

    $userId = (int)$_SESSION['erpuserid'];

    $gid =isset($_GET['id'])? (int)$_GET['id']: (int)$_POST['sid'];

    $sql = "
        SELECT
            tb1.*,
            tb2.created_at,
		   (SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.selahiyyetli_user_id) as selahiyyetli_name,
           (SELECT TOP 1 vezife FROM v_users v WHERE v.USERID=tb1.selahiyyetli_user_id) as selahiyyetli_vezife,
           (SELECT TOP 1 struktur_bolmesi FROM v_users st WHERE st.USERID=tb1.selahiyyetli_user_id) as selahiyyetli_shobe,
           (SELECT TOP 1 user_ad FROM v_user_adlar s WHERE s.USERID=tb1.icraci_direktor) as icraci_direktor_name,
           (SELECT TOP 1 CONCAT(bi.code_of_state, '', bi.passport_num) FROM tb_users_biometric_ids bi WHERE bi.user_id=tb1.selahiyyetli_user_id) as vesiqesi_melumat
        FROM
            tb_prodoc_chixan_sened_tipi_etibarname AS tb1
            LEFT JOIN tb_chixan_senedler  AS tb2 ON tb2.id = $gid
        WHERE
            outgoing_documents_id =$gid
            ";

    $mInf = pdof()->query($sql)->fetch();

    $date = $mInf['created_at'];
    $mInf['etibarnamenin_meqsedi'] = htmlspecialchars($mInf['etibarnamenin_meqsedi']);
    $mInf['harada']               = htmlspecialchars($mInf['harada']);
    $mInf['selahiyyetli_name']    = htmlspecialchars($mInf['selahiyyetli_name']);
    $mInf['selahiyyetli_shobe']   = htmlspecialchars($mInf['selahiyyetli_shobe']);
    $mInf['selahiyyetli_vezife']  = htmlspecialchars($mInf['selahiyyetli_vezife']);
    $mInf['vesiqesi_melumat']     = htmlspecialchars($mInf['vesiqesi_melumat']);
    $mInf['icraci_direktor_name'] = htmlspecialchars($mInf['icraci_direktor_name']);
    $mInf['etibarliq_muddet']     = htmlspecialchars($mInf['etibarliq_muddet']);


    if(!$mInf)
    {
        exit('Erize yoxdu');
    }

    $elementler=array();

    $elementler = array_merge($elementler,$mInf);

    $petType = 'etibarname_esas';

    $file = $modalParameters -> getFileExisting($petType,date('m/d/Y',strtotime($date)),$activeTenantId, 'xo_sened_novu');
    $file_name = $file["file_name"];
    $file_orginal_name = $file["file_original_name"];

    if(isset($_POST['checkStatus'])&&!$file_name){

        exit(json_encode(['status'=>'error','errorMsg' => date('d-m-Y',strtotime($date)).' tarixinə olan export üçün fayl təyin olunmayıb']));
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
            if(!is_null($el) && $kee === "image")
            {
                $document->setImg(
                    $kee,
                    array(
                        'src' => UPLOADS_DIR_PATH . '/imzalar/'.$el,
                        'swh'=>'70',
                        'size'=>array(0=>70, 1=>70)
                    )
                );
            }
            else
            {
                $document->setValue($kee,$el);
            }
        }

        $document->saveAs(UPLOADS_DIR_PATH . 'prodoc/formal/export_templates/'.$file_name);

        if (getProjectName() === ANAMA) {
            $fileNameWithoutExtension = basename($file_name);
            Gears\Pdf::convert(
                UPLOADS_DIR_PATH . 'prodoc/formal/export_templates/'.$file_name,
                UPLOADS_DIR_PATH . "prodoc/formal/export_templates/{$fileNameWithoutExtension}.pdf"
            );

            header("Location: ". getProjectBaseUrl("uploads/prodoc/formal/export_templates/{$fileNameWithoutExtension}.pdf"));
        } else {
            header("Location: ". getProjectBaseUrl("uploads/prodoc/formal/export_templates/".$file_name));
        }

        exit;
    }

}