<?php

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/UmumiForma.php';

define('DOCUMENT_KEY', 'elave_razilashdirma');

$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$id = getInt('id');

$fields = [
    [
        "IsRequired" => true,
        "InputType" => "text.date",
        "ColumnName" => "senedin_tarixi",
        "IsRequiredErrorMessage" => "Sənədin tarixini seçmədiniz"
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "sened_tip",
        "IsRequiredErrorMessage" => "Sənədin tipini seçmədiniz"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "qisa_mezmun"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd",
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "emekdash",
        "IsRequiredErrorMessage" => "Əməkdaşı seçmədiniz"
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "emeqhaqqina_elave_valyuta",
        "IsRequiredErrorMessage" => "Valyutanı seçmədiniz"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "muqavile",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "elave_razilashdirnama_nomresi",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "emeqhaqqina_elave",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "emeqhaqqina_elave_valyuta",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "ezamiyyet_muddeti",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "sv_pin_kodu",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "sv_nomresi",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "sv_teqdim_eden_orqan",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "unvan",
    ],
];

$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    if ($id) {
        $taskCommand = new \Model\InternalDocument\ElaveRazilashdirma($id);
        $taskCommand->edit($dataToBeInserted);

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($taskCommand->getData()['document_id']);

        $user->editInternalDocument(
            $intDoc,
            DOCUMENT_KEY,
            false
        );

        $documentId = $taskCommand->getData()['document_id'];
    } else {
        $taskCommand = \Model\InternalDocument\ElaveRazilashdirma::create($dataToBeInserted, $user);
        $document = $user->createInternalDocumentNumber($taskCommand->getId(), DOCUMENT_KEY, false, true);
        createRelation($document->getId());

        $documentId = $document->getId();
        $id = $document->getId();
    }

    daxiliSenedinTestiqlemesiniElaveEt($taskCommand->getId(), DOCUMENT_KEY, $documentId);

    pdo()->commit();
    $responseArr=array('id'=>$id);
    $user->success_msg("Ok!",$responseArr);
} catch (BaseException $e) {
    print json_encode([
        'status' => 'error',
        'errors' => [nl2br($e->getMessage())]
    ]);
} catch (Exception $e) {
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var']
    ]);
} finally {
    pdo()->rollBack();
}