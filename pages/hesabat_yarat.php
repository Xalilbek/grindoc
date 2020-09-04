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
    $koordinat_gotur = [];
    $veziyyet = [];

    $sql = "
       Select tb_prodoc_partlamamish_tek_sursat.*,
        tb_general_cities.ad as sheher_ad, 
        tb_general_regions.ad as rayon_ad,
		document_number 
        from tb_prodoc_partlamamish_tek_sursat 
        LEFT JOIN tb_general_cities on tb_general_cities.id=tb_prodoc_partlamamish_tek_sursat.sheher_id  
        left JOIN tb_general_regions on rayon_id=tb_general_regions.id 
		LEFT JOIN tb_prodoc_document_number on tb_prodoc_document_number.id=document_id
        where  tb_prodoc_partlamamish_tek_sursat.id = $gid
        ";

    $mInf = pdof()->query($sql)->fetch();

    $sql="
        Select * from tb_daxil_olan_senedler
        where id=".$mInf['document_id'];
    $docInfo= DB::fetch($sql);


    $sql="
    SELECT
        qurgunun_novu,
        modeli,
        miqdari,
        ( CASE WHEN surprizli = 1 THEN N'Hə' ELSE N'Yox' END ) AS surprizli,
        ( CASE WHEN tele_meftili = 1 THEN N'Hə' ELSE N'Yox' END ) AS tele_meftili 
    FROM
        [dbo].[tb_prodoc_partlamamish_tek_sursat_qurgular] 
    WHERE
        sened_id = $gid
            ";
    $qurgular = DB::fetchAll($sql);

    $devices=array();
    foreach ($qurgular as $key=>$qurgu) {
        $devices[] = [
            'table_counter' => $key + 1,
            'qurgunun_novu' => $qurgu['qurgunun_novu'],
            'modeli' => $qurgu['modeli'],
            'miqdari' => $qurgu['miqdari'],
            'surprizli' => $qurgu['surprizli'],
            'tele_meftili' => $qurgu['tele_meftili'],
        ];
    }
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($mInf['document_id']);
    $intDoc->setCustomQuery('v_daxil_olan_senedler_corrected', true);
    $created_by=$intDoc->getData()['created_by'];
    $created_by_info= DB::fetch("SELECT Concat(Adi,' ',Soyadi) as full_name, struktur_bolmesi, vezife from v_users where USERID=".$created_by);

    $relatedDoc = $intDoc->getRelatedDocuments()[0];
    $tapshiriq_number= $relatedDoc['document_number'];

    require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
    $RM = tapsiriqEmrinReyMuelifi($relatedDoc['id']);

    $mInf['document_number'] = $docInfo['document_number'];
    $mInf['tesdiq_olundu']   = $RM['tesdiq_olundu'];
    $mInf['tesdiq_tarixi']   = $RM['tesdiq_tarixi'];
    $mInf['hesabati_veren']  = $created_by_info['full_name'];
    $mInf['teshkilat']       = $created_by_info['struktur_bolmesi'];
    $mInf['vezife']          = $created_by_info['vezife'];
    $mInf['sheher_id']       = $mInf['sheher_ad'];
    $mInf['rayon_id']        = $mInf['rayon_ad'];

    if($mInf['dkps'] == 1)
    {
        $koordinat_gotur[] = 'DGPS';
    }
    if ($mInf['kps'] == 1)
    {
        $koordinat_gotur[] = 'GPS';
    }

    $mInf['koordinat_goturulub'] = !empty($koordinat_gotur) ? implode(", ", $koordinat_gotur) : '';

    if($mInf['veziyyet_zererleshdirilmish']==1)
    {
        $veziyyet[] = 'Zərərsizləşdirilmiş';
    }
    if($mInf['veziyyet_isharelenmish']==1)
    {
        $veziyyet[] = 'İşarənləmiş';
    }
    if($mInf['veziyyet_aparilmish']==1)
    {
        $veziyyet[] = 'Aparılmış';
    }

    $mInf['veziyyet'] = !empty($veziyyet) ? implode(", ", $veziyyet) : '';


    if(!$mInf)
    {
        exit('Erize yoxdu');
    }

    $elementler=array();

    $elementler = array_merge($elementler,$mInf);

    $petType = 'hesabat_yarat';

    $file = $modalParameters -> getFileExisting($petType,date('m/d/Y',strtotime($mInf['tesdiq_tarixi'])),$activeTenantId);
    $file_name = $file["file_name"];
    $file_orginal_name = $file["file_original_name"];
    if(isset($_POST['checkStatus'])&&!$file_name){

        exit(json_encode(['status'=>'error','errorMsg' => date('d-m-Y',strtotime($docInfo['created_at'])).' tarixinə olan export üçün fayl təyin olunmayıb']));
    }
    else if(isset($_POST['checkStatus'])&&$file_name){
        exit(json_encode(['status'=>'success']));

    }

    $all_data = $elementler;

    if(isset($_GET['export']) && $_GET['export']=="word")
    {
        $document = new TemplateProcessor(UPLOADS_DIR_PATH . '/prodoc/formal/'.$file_name);


        $document->cloneRow('table_row', count($devices));
        foreach ($devices as $key=>$task){
            foreach ($task as $key_element=>$element){
                $document->setValue(($key_element).'#'.($key+1),$element);
            }
            $document->setValue( 'table_row#'.($key+1),'');
        }

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