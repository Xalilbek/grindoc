<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$chixan_sened_id = getRequiredPositiveInt('id');

try {
    $chixanSened = new OutgoingDocument($chixan_sened_id);
    $affected_docs = $chixanSened->answerIsNotRequired(get('note'));

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

    $user->success_msg('ok', ['affected_docs' => $returnedData]);
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

