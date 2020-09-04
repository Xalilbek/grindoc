<?php
session_start();
include_once '../../../../class/class.functions.php';
$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$tip = get('tip');
$id  = get('sened_id');


if ('daxil_olan_sened' === $tip) {
    $outgoingDocumentId = getInt('outgoingDocumentId', 0);

    if ($outgoingDocumentId) {

        $sql = "
            select daxil_olan_sened_id
            from v_prodoc_outgoing_document_relation
            where outgoing_document_id = $outgoingDocumentId
        ";

        $id = DB::fetchColumn($sql);

        if (FALSE === $id) {
            $user->error_msg('no related incoming document');
        }
    } else {
        $incomingDocument = new Document($id);
        if (Document::TIP_DAXILI === (int)$incomingDocument->getData()['tip']) {
            require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
            $tip = InternalDocument::getExtraIdById((int)$incomingDocument->getData()['internal_document_type_id']);

            $internalDoc = new InternalDocument($id);
            $id = $internalDoc->getRelatedDataId();
        }
    }
} else if ('butun_senedler' === $tip) {

    $id = getInt('sened_id', 0);

    $incomingDocument = new Document($id);

    if (Document::TIP_DAXILI === (int)$incomingDocument->getData()['tip']) {
        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $tip = InternalDocument::getExtraIdById((int)$incomingDocument->getData()['internal_document_type_id']);

        $internalDoc = new InternalDocument($id);
        $id = $internalDoc->getRelatedDataId();
    }

}

$data_tip_tables_umumi = array(
    'daxil_olan_sened'          => [null, 'daxil_olan_sened'],
    'butun_senedler'          => [null, 'daxil_olan_sened'],
    'chixan_sened'              => ['v_chixan_senedler_eleva_melumat', 'chixan_sened']
);

$data_tip_tables = array(
    'arayish'                       => '/daxili_senedler/arayishlar',
    'power_of_attorney'             => '/daxili_senedler/power_of_attorney',
    'task_command'                  => '/daxili_senedler/task_command',
    'umumi_forma'                   => '/daxili_senedler/umumi_forma',
    'satin_alma'                    => '/daxili_senedler/satin_alma',
    'elave_razilashdirma'           => '/daxili_senedler/elave_razilashdirma',
    'teqdimat'                      => '/daxili_senedler/teqdimat',
    'icra_sexsin_deyisdirilmesi'    => '/daxili_senedler/icra_sexsin_deyisdirilmesi',
    'icra_muddeti_deyisdirilmesi'   => '/daxili_senedler/icra_muddeti_deyisdirilmesi',
    'mezuniyet_erizesi'             => '/daxili_senedler/mezuniyyet',
    'mezuniyet_emri'                => '/daxili_senedler/vacation_order',
    'ezamiyyet_erizesi'             => '/daxili_senedler/ezamiyyet',
    'ezamiyet_emri'                 => '/daxili_senedler/business_trip',
    'hesabat_yarat'                 => '/daxili_senedler/hesabat_yarat',
    'create_act'                 => '/daxili_senedler/create_act',
    'ish_taphsirigi'                => '/daxili_senedler/ish_taphsirigi',
    'ishe_xitam_erizesi'            => '/daxili_senedler/petitions_info',
    'icaze'                         => '/daxili_senedler/icazeler',
    'ishe_qebul_erizesi'            => '/daxili_senedler/ishe_qebul_erizesi',
    'xestelik_vereqi'               => '/daxili_senedler/xestelik_vereqi',
    'protask_taprsiriq_child'       => '/daxili_senedler/protask_taprsiriq_child',

    'gorushler'        => '/daxili_senedler/gorushler',
    'emek_muqavilesi'   => '/daxili_senedler/labor_contract',
    'employment'    =>  '/daxili_senedler/employment',
    'vacation_compensation'    =>  '/daxili_senedler/vacation_compensation',
    'termination_contract'    =>  '/daxili_senedler/termination_contract',
    'termination_petition'    =>  '/daxili_senedler/termination_petition',
    'employee_petition'    =>  '/daxili_senedler/employee_petition',
    'other_petition' => '/daxili_senedler/petitions_info',
    'advance_request' => '/daxili_senedler/advance_request',
    'advance_report' => '/daxili_senedler/advance_report',
    'salary_advance' => '/daxili_senedler/salary_advance',
);

$sened_id = $id;

if ( array_key_exists($tip, $data_tip_tables_umumi) )
{

    $table_name = $data_tip_tables_umumi[$tip][0];
    $file_name = $data_tip_tables_umumi[$tip][1];
    $html = '';

    if (!is_null($table_name)) {
        $senedler = DB::fetch("SELECT * FROM {$table_name} WHERE id = '$id'");
    } else {
        $senedler = true;
    }


    if ($senedler !== false) {
        ob_start();
        require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/etrafli/'.$file_name.'.php';
        $html = ob_get_clean();
    }

    print json_encode(array("status" => "success", "html" => $html, 'type' => 'incoming' ));
} elseif ( array_key_exists($tip, $data_tip_tables) ) {
    $file_name = $data_tip_tables[$tip];

    $parametrler['sid'] = $id;

    require_once DIRNAME_INDEX . 'prodoc/ajax/dashboard/etrafli'.$file_name.'.php';
}
else {
    print json_encode(array("status" => "failed", "message" => "Bu adda əməliyyat yoxdur"));
}