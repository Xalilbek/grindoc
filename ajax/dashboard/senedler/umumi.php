<?php
session_start();
include_once '../../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';

$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}
$UserId = (int)$_SESSION['erpuserid'];

if (isset($_POST['outgoingDocumentId']) && !isset($_POST['table'])) {
    require_once DIRNAME_INDEX . 'prodoc/includes/outgoing_document.php';
    $_POST['sened_id'] = xosToDos((int)$_POST['outgoingDocumentId'], true);
}

$data_tip_modules_umumi = array(
    'daxil_olan_sened' => 'daxil_olan_senedler',
    'butun_senedler'   => 'daxil_olan_sened',
    'chixan_sened'     => 'chixan_senedler',
);

$data_tip_modules = array(
    'daxili_sened_mezuniyyet' => array('v_mezuniyyetler', 'uploads/proid/vacations/'),
    'daxili_sened_ezamiyyet' => array('v_ezamiyyetler', 'uploads/proid/vacations/'),
    'daxili_sened_xestelik_vereqi' => array('v_xestelik_vereqleri', 'uploads/proid/vacations/'),
    'daxili_sened_labor_contract' => array('tb_proid_labor_contracts','uploads/proid/contracts/labor_contract/'),
    'daxili_sened_vacation_order' => array('tb_prodoc_vacation_orders','uploads/proid/vacations/'),
    'daxili_sened_business_trip' => array('tb_prodoc_business_trip', 'uploads/prodoc/business_trip/'),
    'daxili_sened_employee_petition' => array('tb_proid_employe_petition','uploads/proid/petitions/employe_petition/'),
    'daxili_sened_employee_terminate_petition' => array('tb_proid_employe_petition','uploads/proid/petitions/employe_petition/'),
    'daxili_sened_other_petition' => array('tb_proid_employe_petition','uploads/proid/petitions/employe_petition/'),
    'daxili_sened_vacation_compensation' => array('tb_emrler','uploads/emr_documents/emek_muqavilesine_xitam/'),
    'daxili_sened_termination_contract' => array('tb_emrler', 'uploads/emr_documents/emek_muqavilesine_xitam/' ),
    'daxili_sened_employment' => array('tb_emrler', 'uploads/emr_documents/emek_muqavilesine_xitam/'),
//    'daxili_sened_salary_advance' => array(),
    'daxili_sened_advance_report'  => array('tb_avans_hesabat','uploads/daxili_senedler/'),
    'daxili_sened_advance_request' => array('tb_avanslar','uploads/prodoc/common/')
);

if ( !(isset($_POST['sened_id']) && is_numeric($_POST['sened_id']) && (int)$_POST['sened_id'] > 0) ) {
    print json_encode(array("status" => "failed", "message" => "Bu adda əməliyyat yoxdur"));
    exit;
}



$sened_id = (int)$_POST['sened_id'];

$sql = "
        SELECT
          tb1.internal_document_type_id,
          tb1.tip,
          document_type.extra_id
        FROM
          v_daxil_olan_senedler_corrected AS tb1
            LEFT JOIN tb_prodoc_inner_document_type document_type ON document_type.id = tb1.internal_document_type_id
        WHERE tb1.id = '$sened_id'
	";

$senedler = DB::fetch($sql);
$document_extra_id = $senedler['extra_id'];

$incomingDocument = new Document($sened_id, [
    'data' => $senedler
]);

if ( isset($_POST['tip']) && is_string($_POST['tip']) && array_key_exists($_POST['tip'], $data_tip_modules_umumi) )
{
    $tip = (string)$_POST['tip'];
    $module_name = $data_tip_modules_umumi[$tip];

    $sened_fayl = $module_name . '_sened_fayl';
    $qosma_fayl = $module_name . '_qoshma_fayl';
    $esas_fayl = $module_name . '_esas_sened_fayl';
    $ishe_tik_fayl="muraciet_sened_fayl";


    $shekillerHtmlSened = "";
    $shekillerHtmlQoshma = "";
    $shekillerHtmlIsetik = "";
    $senedlerHtml = "";
    $senedlerHtml = "";

    $esasSenedlerHtml = "";
    $esasShekillerHtml = "";
    $iseTikHtml = "";

    $qoshmaHtml= "";

    if($tip=='daxil_olan_sened'){
        $sql ="   
               SELECT
                *
            FROM
                tb_files
            WHERE
                (
                    (
                        (module_name = '$sened_fayl' OR module_name = '$qosma_fayl' OR module_name ='$esas_fayl') AND module_entry_id = '$sened_id'
                    )
                    OR 
                    (
                        (module_name = '$ishe_tik_fayl') AND module_entry_id IN (
                            SELECT
                                id
                            FROM
                                v_prodoc_muraciet
                            WHERE
                                daxil_olan_sened_id = '$sened_id'
                        )
                    )
                )
            ORDER BY
                id ASC";
    }else{
        $sql="SELECT * FROM 
              tb_files WHERE (module_name='$sened_fayl' OR module_name='$qosma_fayl' OR module_name ='$esas_fayl') 
                         AND module_entry_id = '$sened_id'";
    }

    $senedler = DB::fetchAll($sql);


    if($tip=='daxil_olan_sened') $created_by = DB::fetchColumn("SELECT created_by FROM v_daxil_olan_senedler WHERE id = '$sened_id'");
    else $created_by = DB::fetchColumn("SELECT created_by FROM v_chixan_senedler WHERE id = '$sened_id'");


    $checkTrash = false;
    $doc = new Document($sened_id);

    if($tip=='daxil_olan_sened'){
        $checkTrash = !$doc->hasRelatedAction() && (int)$UserId == (int)$created_by;
    }
    else
    {
        $status = DB::fetchColumn("SELECT status FROM v_chixan_senedler WHERE id = '$sened_id'");
        $checkTrash = $status == IConfirmable::STATUS_IMTINA_OLUNUB && (int)$UserId == (int)$created_by;
    }

    $trash=($checkTrash)?
        ' <i class="fa fa-trash docTrash" data-toggle="tooltip" data-placement="right" title="Sənədi sil" style="color:red; cursor: pointer"></i>'
        :
        ''
    ;

    $checkRedact = DB::fetchColumn("SELECT [value] FROM tb_options WHERE option_name ='pdf_redact' ");


    $docInfo=array();
    if($senedler !== false)
    {
        foreach($senedler AS $senedInf)
        {
            $sened = $senedInf['file_actual_name'];
            $uzanti = explode(".", $sened);
            $uzanti = strtolower(end($uzanti));
            $docInfo[] = [
                'key'=>$sened,'id'=>$senedInf['id']
            ];
            if($uzanti=="jpg" || $uzanti=="jpeg" || $uzanti=="png" || $uzanti=="gif")
            {
                if($senedInf['module_name']==$sened_fayl){
                    $shekillerHtmlSened .= '<img data-file-id="'.$senedInf['id'].'" src="'.PRODOC_FILES_WEB_PATH.$sened.'" data-image="'.PRODOC_FILES_WEB_PATH.$sened.'" style="display:none;">';
                }
                elseif ($senedInf['module_name']==$esas_fayl)
                    $esasShekillerHtml .= '<img data-file-id="'.$senedInf['id'].'" src="'.PRODOC_FILES_WEB_PATH.$sened.'" data-image="'.PRODOC_FILES_WEB_PATH.$sened.'" style="display:none;" >';

                elseif ($senedInf['module_name']==$qosma_fayl)
                    $shekillerHtmlQoshma .= '<img data-file-id="'.$senedInf['id'].'" src="'.PRODOC_FILES_WEB_PATH.$sened.'" data-image="'.PRODOC_FILES_WEB_PATH.$sened.'" style="display:none;">';

                elseif ($senedInf['module_name']==$ishe_tik_fayl && $sened_fayl=='daxil_olan_senedler_sened_fayl')
                    $shekillerHtmlIsetik .= '<img data-file-id="'.$senedInf['id'].'" src="'.PRODOC_FILES_WEB_PATH.$sened.'" data-image="'.PRODOC_FILES_WEB_PATH.$sened.'" style="display:none;">';

            }
            else if($uzanti=="pdf")
            {
                if($senedInf['module_name']==$sened_fayl) {
                    if ($checkRedact)
                        $senedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i>
                        <a target="_blank" file-id="' . $senedInf['id'] . '" href="pages/prodoc/PDF_redakte.php?sened_id=' . $sened_id . '&tip=' . $tip . '&file_id=' . $senedInf['id'] . '"    >' . $senedInf['file_original_name'] . '</a> ' . $trash . '
                        <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="pages/prodoc/PDF_redakte.php?sened_id=' . $sened_id . '&tip=' . $tip . '&file_id=' . $senedInf['id'] . '"   />
                        </div>';
                    else
                        $senedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> 
                        <a target="_blank" file-id="' . $senedInf['id'] . '" href="'.PRODOC_FILES_WEB_PATH.$sened.'"   >' . $senedInf['file_original_name'] . '</a> ' . $trash . '
                        <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="'.PRODOC_FILES_WEB_PATH.$sened.'"    /></div>';
                }
                elseif ($senedInf['module_name']==$esas_fayl) {
                    if ($checkRedact)
                        $esasSenedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i>
                        <a target="_blank" file-id="' . $senedInf['id'] . '" href="pages/prodoc/PDF_redakte.php?sened_id=' . $sened_id . '&tip=' . $tip . '&file_id=' . $senedInf['id'] . '"    >' . $senedInf['file_original_name'] . '</a>  ' . $trash . '
                        <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="pages/prodoc/PDF_redakte.php?sened_id=' . $sened_id . '&tip=' . $tip . '&file_id=' . $senedInf['id'] . '"   />
                       </div>';
                    else
                        $esasSenedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> 
                        <a target="_blank" file-id="' . $senedInf['id'] . '" href="'.PRODOC_FILES_WEB_PATH.$sened.'"    >' . $senedInf['file_original_name'] . '</a> ' . $trash . '
                        <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="'.PRODOC_FILES_WEB_PATH.$sened.'"    /></div>';
                }
                elseif ($senedInf['module_name']==$qosma_fayl)
                    $qoshmaHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a target="_blank" file-id="'.$senedInf['id'].'" href="'.PRODOC_FILES_WEB_PATH.$sened.'" >'.$senedInf['file_original_name'].'</a>
                    <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="'.PRODOC_FILES_WEB_PATH.$sened.'"    />'.$trash.'</div>';
                elseif ($senedInf['module_name']==$ishe_tik_fayl && $sened_fayl=='daxil_olan_senedler_sened_fayl')
                    $iseTikHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a target="_blank" file-id="'.$senedInf['id'].'" href="'.PRODOC_FILES_WEB_PATH.$sened.'"    >'.$senedInf['file_original_name'].'</a>
                    <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="'.PRODOC_FILES_WEB_PATH.$sened.'"    />'.$trash.'</div>';

            }
            else if($uzanti=="rar"){

                if ($senedInf['module_name']==$esas_fayl)
                    $esasSenedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>'.$senedInf['file_original_name'].'</a></div>';
                else
                    $senedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>'.$senedInf['file_original_name'].'</a></div>';
            }
            elseif($uzanti=="docx")
            {
                if($senedInf['module_name']==$sened_fayl)
                    $senedlerHtml .= '<div>
                    <i class="fa fa-file-pdf-o"></i>'.$senedInf['file_original_name'].'  
                    <a file-id="' . $senedInf['id'] . '" href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>
                    <i class="fa fa-download" style="font-size: 20px" aria-hidden="true"></i></a> 
                    <a id="tabLinkDocx" target="_blank" href="prodoc/ajax/file/convert_docx_to_pdf.php?file_id='.$senedInf['id'].'" >
                     <i class="fa fa-external-link" style="cursor: pointer" aria-hidden="true"></i>
                     </a> '.$trash.'
                    <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="prodoc/ajax/file/convert_docx_to_pdf.php?file_id='.$senedInf['id'].'"    /></div>';
                elseif ($senedInf['module_name']==$esas_fayl)
                    $esasSenedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> '.$senedInf['file_original_name'].'
                     <a href="'.PRODOC_FILES_WEB_PATH.$sened.'" download><i class="fa fa-download" style="font-size: 20px" aria-hidden="true"></i></a> 
                      <a id="tabLinkDocx" target="_blank" href="prodoc/ajax/file/convert_docx_to_pdf.php?file_id='.$senedInf['id'].'" >
                      <i class="fa fa-external-link" style="cursor: pointer" aria-hidden="true"></i>
                      </a>'.$trash.'
                      <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="prodoc/ajax/file/convert_docx_to_pdf.php?file_id='.$senedInf['id'].'"    />
                    </div>';
                elseif ($senedInf['module_name']==$qosma_fayl)
                    $qoshmaHtml .= '<div><i class="fa fa-file-pdf-o"></i>'.$senedInf['file_original_name'].' 
                    <a file-id="' . $senedInf['id'] . '" href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>
                    <i class="fa fa-download" style="font-size: 20px" aria-hidden="true"></i>
                    </a> 
                    <a id="tabLinkDocx" target="_blank" href="prodoc/ajax/file/convert_docx_to_pdf.php?file_id='.$senedInf['id'].'" >
                     <i class="fa fa-external-link" style="cursor: pointer" aria-hidden="true"></i>
                      </a>'.$trash.'
                      <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="prodoc/ajax/file/convert_docx_to_pdf.php?file_id='.$senedInf['id'].'"    />
                      </div>';
                elseif ($senedInf['module_name']==$ishe_tik_fayl && $sened_fayl=='daxil_olan_senedler_sened_fayl')
                    $iseTikHtml .= '<div><i class="fa fa-file-pdf-o"></i> '.$senedInf['file_original_name'].'
                     <a href="'.PRODOC_FILES_WEB_PATH.$sened.'" download><i class="fa fa-download" style="font-size: 20px" aria-hidden="true"></i>
                     </a> 
                     <a id="tabLinkDocx" target="_blank" href="prodoc/ajax/file/convert_docx_to_pdf.php?file_id='.$senedInf['id'].'" > 
                     <i class="fa fa-external-link" style="cursor: pointer" aria-hidden="true"></i>
                     </a>
                     '.$trash.'                
                     <embed id="pdf_viewer" type="application/pdf" style="width: 100%; height: 500px;" src="prodoc/ajax/file/convert_docx_to_pdf.php?file_id='.$senedInf['id'].'"    />

                     </div>';

            }
            else
            {
//                $senedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>'.$senedInf['file_original_name'].'</a></div>';
                if($senedInf['module_name']==$sened_fayl)
//                    $esasSenedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>'.$senedInf['file_original_name'].'</a></div>';
                    $senedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a file-id="' . $senedInf['id'] . '" href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>'.$senedInf['file_original_name'].'</a>'.$trash.'</div>';
                elseif ($senedInf['module_name']==$esas_fayl)
                    $esasSenedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>'.$senedInf['file_original_name'].'</a>'.$trash.'</div>';
                elseif ($senedInf['module_name']==$qosma_fayl)
                    $qoshmaHtml .= '<div><i class="fa fa-file-pdf-o"></i><a file-id="' . $senedInf['id'] . '" href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>'.$senedInf['file_original_name'].'</a>'.$trash.'</div>';
                elseif ($senedInf['module_name']==$ishe_tik_fayl && $sened_fayl=='daxil_olan_senedler_sened_fayl')
                    $iseTikHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a href="'.PRODOC_FILES_WEB_PATH.$sened.'" download>'.$senedInf['file_original_name'].'</a>'.$trash.'</div>';

            }

        }
    }

//    $shekiller = mssql_query("SELECT * FROM tb_prodoc_skan_merkezi_fayllar WHERE sened_id='$sened_id' AND (SELECT sened_tip FROM tb_prodoc_skan_merkezi WHERE id=sid)='$senedTip'");
//    while($shekilInf = mssql_fetch_array($shekiller))
//    {
//        $shekillerHtml .= '<img src="uploads/daxil_olan_senedler/scanner/thmb/'.htmlspecialchars($shekilInf['thmb_fayl_ad']).'" data-image="uploads/daxil_olan_senedler/scanner/'.htmlspecialchars($shekilInf['fayl_ad']).'" style="display:none;">';
//    }

    ob_start();
    require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/senedler/umumi.php';
    $html = ob_get_clean();

    print json_encode(array("status" => "success", "html" => $html ));
}
elseif (isset($_POST['tip']) && is_string($_POST['tip']) && array_key_exists($_POST['tip'], $data_tip_modules)) {
    $table_name = $data_tip_modules[$_POST['tip']][0];
    $link = $data_tip_modules[$_POST['tip']][1];
    if ($table_name=='tb_emrler') $senedler = DB::fetchColumn("SELECT senedler FROM {$table_name} WHERE id='$sened_id'");
    elseif ($table_name=='tb_avanslar')  $senedler = DB::fetchColumn("SELECT senedler FROM {$table_name} WHERE id='$sened_id'");
    elseif ($table_name=='tb_avans_hesabat')  $senedler = DB::fetchColumn("SELECT senedler FROM {$table_name} WHERE id='$sened_id'");
    else $senedler = DB::fetchColumn("SELECT attachment FROM {$table_name} WHERE id='$sened_id'");
    $attachment = "&nbsp;";
    $shekillerHtml = "";
    $senedlerHtml = "";
    $iseTikHtml = "";
    foreach(explode(",", $senedler) AS $sened)
    {
        if(trim($sened)!="")
        {

            $uzanti = explode(".", $sened);
            $uzanti = strtolower(end($uzanti));
            if($uzanti=="jpg" || $uzanti=="jpeg" || $uzanti=="png" || $uzanti=="gif")
            {
                $shekillerHtml .= '<img src="'.$link.htmlspecialchars(basename($sened)).'" data-image="'.$link.htmlspecialchars(basename($sened)).'" style="display:none;">';
            }
            else
            {

                $senedlerHtml .= '<div><i class="fa fa-file-pdf-o"></i> <a href="'.$link.htmlspecialchars(basename($sened)).'" download>'.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a></div>';
            }

        }
    }

    ob_start();
    require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/senedler/umumi.php';
    $html = ob_get_clean();
//var_dump($html);
//exit();
    print json_encode(array("status" => "success", "html" => $html ));
}
else {
    print json_encode(array("status" => "failed", "html" => "Boşdur"));
}