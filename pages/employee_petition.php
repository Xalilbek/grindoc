<?php
session_start();
include_once '../../class/class.functions.php';
$user = new User();

if(!$user->get_session())
{
    header("Location: login.php");
    exit;
}
use PhpOffice\PhpWord\TemplateProcessor;

$_GET['export'] = "word";

$modalParameters = new modalParameters ();

if(isset($_GET['id']) && $_GET['id']>0)
{

    $userId = (int)$_SESSION['erpuserid'];

    $gid = (int)$_GET['id'];

    // $new_position = pdof()->query("SELECT ( SELECT struktur_bolmesi FROM tb_Struktur WHERE sebebi = struktur_id ) AS yeni_shobe, ( SELECT vezife FROM tb_vezifeler WHERE sebebi2 = id ) AS yeni_vezife ,(SELECT vezife  FROM tb_vezifeler tb3 WHERE tb2.vezife_id=tb3.id) AS cari_vezife, (SELECT struktur_bolmesi  FROM tb_Struktur tb4 WHERE tb2.struktur_id=tb4.struktur_id) AS cari_shobe FROM tb_proid_employe_petition tb1 LEFT JOIN tb_users tb2 ON tb1.who_registered=tb2.USERID  WHERE id='$gid'")->fetch();

    $mInf = pdof()->query("SELECT tb1.*,tb_emrInfo.*,tb3.Soyadi,tb3.Adi,tb3.AtaAdi,tb4.all_data,tb1.quvveye_minme_tarixi
						  FROM tb_proid_employe_petition tb1
  						  LEFT JOIN tb_users AS tb3 ON tb3.USERID = tb1.employe
  						  LEFT JOIN tb_emrler AS tb4 ON tb4.id = tb1.EsasEmrId
  						  OUTER APPLY(SELECT TOP 1 yeni_vezife,yeni_bolme FROM tb_emrler WHERE status=1 AND xitam_verilib=1 AND user_id=tb1.employe AND TenantId=tb1.TenantId) tb_emrInfo
 						  WHERE tb1.id='$gid'")->fetch();

    if(!$mInf)
    {
        exit('Erize yoxdu');
    }
    $testiqedecekler = 	dil::soz("11testiqleyenler");
    $type  = (int)$mInf['type'];

    $elementler = trim($mInf['all_data'])!=""?@json_decode($mInf['all_data'],true):[];

    $elementler = array_merge($elementler,$mInf);

    $petType = 'employe_petition'.(int)$type;

    if((int)$type==3)
    {
        $mInf['yeni_vezife'] = (int)$mInf['sebebi2'];
        $mInf['yeni_bolme'] = (int)$mInf['sebebi'];
    }

    $file = $modalParameters -> getFileExisting($petType,date('m/d/Y',strtotime($mInf['date'])),$activeTenantId);
    $file_name = $file["file_name"];
    $file_orginal_name = $file["file_original_name"];
    if(!$file_name)
    {
        exit(date('d-m-Y',strtotime($mInf['petition_date'])).' tarixinə olan export üçün fayl təyin olunmayıb');
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
    exit;

    print $user->template_yukle('msk/prodoc/employe_petition' . $type, $elementler);

    if(isset($_GET['export']) && $_GET['export']=="pdf")
    {
        $template = ob_get_contents();
        ob_end_clean();
        require_once "mpdf/mpdf.php";
        $mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10);
        $mpdf->list_indent_first_level = 0;
        $mpdf->WriteHTML($template, 2);
        $mpdf->Output($title.'-'.$gid.'.pdf', 'D');
    }

}