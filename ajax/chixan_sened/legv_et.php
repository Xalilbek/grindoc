<?php
use History\History;
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];

$chixan_sened_id = getRequiredPositiveInt('id');
$sebeb           = get('sebeb');

try {
    $chixanSened = new OutgoingDocument($chixan_sened_id);
    $affected_docs = $chixanSened->legvEt($sebeb);
    $lastHistoryId = History::getLastInsertedId();

    if (isset($_FILES['sened'])) {
        $files = saveFiles('sened', PRODOC_FILES_SAVE_PATH, false);

        for ($j = 0, $lenj = count($files); $j < $lenj; ++$j) {

            SQL::insert('tb_files', [
                'module_name' => 'history_sened',
                'module_entry_id' => $lastHistoryId,
                'file_original_name' => $files[$j]['file_original_name'],
                'file_actual_name' => $files[$j]['file_actual_name'],
            ]);
        }
    }

    $returnedData = [];
    foreach ($affected_docs as $affected_doc) {
        $returnedData[] = [
            'id' => $affected_doc['doc']->data['id'],
            'number'   => $affected_doc['doc']->data['document_number'],
            'isClosed' => $affected_doc['isClosed'],
            'operationsCompleted' => $affected_doc['operationsCompleted'],
        ];
    }

    require_once  DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
    notifyAboutClosingDocuments($returnedData);

    $sql= "Select user_id from tb_prodoc_testiqleyecek_shexs where related_record_id=".$chixan_sened_id." AND status=1 ";
    $legv_notify=DB::fetchAll($sql);
    $shexsler= array();
    foreach ($legv_notify as $key=> $istirakci){

        $shexsler[]=(int)$legv_notify[$key][0];
    }

    $ishtirakchi_notify= pdof()->query("SELECT   CONCAT(Adi,' ', Soyadi) as user_name, document_number 
    from   v_chixan_senedler left JOIN tb_users on created_by=tb_users.USERID WHERE v_chixan_senedler.id=".$chixan_sened_id)->fetch();
    $isSubTask= pdof()->query("SELECT mesul_shexs, (SELECT rey_muellifi from v_daxil_olan_senedler WHERE id=daxil_olan_sened_id) as rey_muellifi from tb_derkenar 
    WHERE id=(SELECT parentTaskId from tb_derkenar WHERE id=(Select derkenar_id from v_prodoc_outgoing_document_relation WHERE outgoing_document_id=1777))")->fetch();
    foreach ($shexsler as $ishtirakchi){
        $user->sendNotifications( true, true,
            'chixan_sened_legv',
            $ishtirakchi_notify['user_name'], "",
            $chixan_sened_id,
            $ishtirakchi,
            "chixan_sened_legv",
            "",
            "",
            "",
            $ishtirakchi_notify['document_number'],
            "xaric_olan_sened",
            "legv"
        );
    }
    if ($isSubTask['mesul_shexs']!=''){
        $user->sendNotifications( true, true,
            'chixan_sened_legv',
            $ishtirakchi_notify['user_name'], "",
            $chixan_sened_id,
            $isSubTask['mesul_shexs'],
            "chixan_sened_legv",
            "",
            "",
            "",
            $ishtirakchi_notify['document_number'],
            "xaric_olan_sened",
            "legv"
        );
    }
    if ($isSubTask['rey_muellifi']!=''){
        $user->sendNotifications( true, true,
            'chixan_sened_legv',
            $ishtirakchi_notify['user_name'], "",
            $chixan_sened_id,
            $isSubTask['rey_muellifi'],
            "chixan_sened_legv",
            "",
            "",
            "",
            $ishtirakchi_notify['document_number'],
            "xaric_olan_sened",
            "legv"
        );
    }

    $user->success_msg('ok', ['affected_docs' => $returnedData]);
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

