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
            gonderen_teshkilat_ad,
            ( SELECT $userAdSQLFormat FROM v_users WHERE USERID = etibarname_sexs ) AS etibarname_sexs_ad,
            ( SELECT imza FROM v_users WHERE USERID = created_by ) AS image,
            etibarname_kartin_nomresi,
            document_number,
            etibarname_avtomobilin_nomresi,
            etibarname_yanacagin_miqdari,
            etibarname_vesiqenin_kodu,
            etibarname_vesiqenin_nomresi,
            etibarname_vesiqeni_teqdim_eden_orqan,
            etibarname_etibarliq_muddet,
            ( SELECT $userAdSQLFormat FROM v_users WHERE USERID = etibarname_icraci_direktor ) AS etibarname_icraci_direktor_ad,
            ( SELECT $userAdSQLFormat FROM v_users WHERE USERID = etibarname_bas_muhasib ) AS etibarname_bas_muhasib_ad,
            created_at AS [date] 
        FROM
            v_chixan_senedler_eleva_melumat 
        WHERE
            id =$gid
            ";

    $mInf = pdof()->query($sql)->fetch();

    $date=$mInf['date'];


    $mInf['gonderen_teshkilat']                      = htmlspecialchars($mInf['gonderen_teshkilat_ad']);
    $mInf['etibarname_sexs_ad']                      = htmlspecialchars($mInf['etibarname_sexs_ad']);
    $mInf['etibarname_kartin_nomresi']               = htmlspecialchars($mInf['etibarname_kartin_nomresi']);
    $mInf['etibarname_avtomobilin_nomresi']          = htmlspecialchars($mInf['etibarname_avtomobilin_nomresi']);
    $mInf['etibarname_yanacagin_miqdari'  ]          = htmlspecialchars($mInf['etibarname_yanacagin_miqdari']);
    $mInf['document_number']                         = htmlspecialchars($mInf['document_number']);
    $mInf['date']                                    = htmlspecialchars(date("d.m.Y", strtotime($mInf['date'])));
    $mInf['etibarname_vesiqenin_kodu'     ]          = htmlspecialchars($mInf['etibarname_vesiqenin_kodu']);
    $mInf['etibarname_vesiqenin_nomresi'  ]          = htmlspecialchars($mInf['etibarname_vesiqenin_nomresi']);
    $mInf['etibarname_vesiqeni_teqdim_eden_orqan' ]  = htmlspecialchars($mInf['etibarname_vesiqeni_teqdim_eden_orqan']);
    $mInf['etibarname_etibarliq_muddet']             = htmlspecialchars($mInf['etibarname_etibarliq_muddet']);
    $mInf['etibarname_icraci_direktor']              = htmlspecialchars($mInf['etibarname_icraci_direktor_ad']);
    $mInf['etibarname_bas_muhasib']                  = htmlspecialchars($mInf['etibarname_bas_muhasib_ad']);
    $mInf['image']                                   = htmlspecialchars($mInf['image']);


    if(!$mInf)
    {
        exit('Erize yoxdu');
    }

    $elementler=array();

    $elementler = array_merge($elementler,$mInf);

    $petType = 'etibarname_chixan_sened';

    $file = $modalParameters -> getFileExisting($petType,date('m/d/Y',strtotime($date)),$activeTenantId);
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

        $document->saveAs(UPLOADS_DIR_PATH . 'prodoc/formal/export_templates/'.$file_orginal_name);

        if (getProjectName() === ANAMA) {
            $fileNameWithoutExtension = basename($file_name);
            Gears\Pdf::convert(
                UPLOADS_DIR_PATH . 'prodoc/formal/export_templates/'.$file_orginal_name,
                UPLOADS_DIR_PATH . "prodoc/formal/export_templates/{$fileNameWithoutExtension}.pdf"
            );

            header("Location: " . getProjectBaseUrl("uploads/prodoc/formal/export_templates/{$fileNameWithoutExtension}.pdf"));
        } else {
            header("Location: " . getProjectBaseUrl("uploads/prodoc/formal/export_templates/".$file_orginal_name));
        }

        exit;
    }

}