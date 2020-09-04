<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
require_once DIRNAME_INDEX . 'prodoc/functions/settings.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/Teqdimat.php';

define('DOCUMENT_KEY', 'teqdimat');

$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$id = getInt('id',0);

$fields = [
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "kim",
        "IsRequiredErrorMessage" => "Kim vacib sahədi!"
    ],
    [
        "IsRequired" => false,
        "InputType" => "arrayOfIds",
        "ColumnName" => "sened_fayl",
        "IsRequiredErrorMessage" => "Pdf faylı seçilməyib!",
        "customValidation" => 'checkPdfExistense'
    ],
    [
        "InputType" => "id",
        "ColumnName" => "kime"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "qisa_mezmun",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "melumat_metni",
    ],
];
$fieldOfDos=[
    [
        "InputType" => "text.datetime",
        "ColumnName" => "senedin_tarixi"

    ]

    ];

$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

$formOfDos = new Form($fieldOfDos);
$formOfDos->check();
$dataToBeInsertedOfDos = $formOfDos->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    $documentId = NULL;

    if ($id) {
        $taskCommand = new \Model\InternalDocument\Teqdimat($id);
        $taskCommand->edit($dataToBeInserted);
        $documentId = (int)$taskCommand->getData()['document_id'];

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $intDoc = new InternalDocument($taskCommand->getData()['document_id']);
        $user->editInternalDocument(
            $intDoc,
            DOCUMENT_KEY,
            false
        );
    } else {
        $taskCommand = \Model\InternalDocument\Teqdimat::create($dataToBeInserted, $user);
        $document = $user->createInternalDocumentNumber($taskCommand->getId(), 'teqdimat', NULL, true);
        $documentId = $document->getId();
        $id=$document->getId();
        createRelation($document->getId());
    }

    DB::update('tb_daxil_olan_senedler',[
        'senedin_tarixi'=>$dataToBeInsertedOfDos['senedin_tarixi']
    ],$documentId);

    daxiliSenedinTestiqlemesiniElaveEt($taskCommand->getId(), DOCUMENT_KEY, $documentId, '', 'daxili_sened_novu');

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