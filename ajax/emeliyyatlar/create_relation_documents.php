<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
require_once DIRNAME_INDEX . 'prodoc/component/Form.php';
$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$fields = [
    [
        "Title" => "Sənəd",
        "InputType" => "file",
        "ColumnName" => "sened_fayl"
    ],
    [
        "IsRequired" => false,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => "confirming_users"
    ],
    [
        "Title" => "Sənədlər",
        "InputType" => "related_document",
        "ColumnName" => "related_document"
    ],
    [
        "Title" => "Sənəd",
        "InputType" => "outgoingDocumentId",
        "ColumnName" => "outgoingDocumentId"
    ],
];
$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();


$userId = $_SESSION['erpuserid'];

$qeyd = get('qeyd');


try {
    DB::beginTransaction();


    $outgoingDocumentQuote = DB::quote($dataToBeInserted['outgoingDocumentId']);
    $related_incoming_documents = DB::fetchColumnArray(" SELECT daxil_olan_sened_id FROM v_prodoc_outgoing_document_relation WHERE outgoing_document_id =  ".$outgoingDocumentQuote);




    $outgoingDocuments = array(new OutgoingDocument($dataToBeInserted['outgoingDocumentId']));

    foreach ($dataToBeInserted['related_document'] as $document) {

        $haveRelated = false;
        foreach($related_incoming_documents as $incoming_document ){

            if($incoming_document == $document){
                $haveRelated=true;
            }
        }

        if(!$haveRelated){

            $appeal = Appeal::create([
                'daxil_olan_sened_id' => $document,
                'derkenar_id' => NULL,
                'netice_id' => NULL,
                'outgoingDocuments' => $outgoingDocuments,
                'tip' => Appeal::TIP_SENED_HAZIRLA,
                'dos_status' => NULL,
                'document_elaqelendirme' => 1
            ]);
        }
    }








    DB::commit();
} catch (Exception $e) {
    DB::rollBack();

    $user->error_msg($e->getMessage());
}

$user->success_msg();