<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$teyinat = getInt('teyinat');

$chixanSenedFields = [
    [
        "IsRequired" => true,
        "Title" => "Kim göndərir",
        "InputType" => "select",
        "ColumnName" => "kim_gonderir"
    ],
    [
        "IsRequired" => true,
        "Title" => "Sənədin növü",
        "InputType" => "select",
        "ColumnName" => "senedin_novu"
    ],
    [
        "IsRequired" => true,
        "Title" => "Vərəqlərin sayı",
        "InputType" => "text",
        "ColumnName" => "vereq_sayi"
    ],
    [
        "InputType" => "text.datetime",
        "ColumnName" => "gonderme_tarixi"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "mektubun_qisa_mezmunu"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "forma"
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "teyinat",
        "Title" => "Təyinat"
    ],
    [
        "IsRequired" => true,
        "InputType" => "select",
        "ColumnName" => "gonderen_teshkilat",
        "Title" => "Göndərən təşkilat"
    ],
    [
        "IsRequired" => $teyinat === 4,
        "InputType" => "select",
        "ColumnName" => "gonderen_shexs",
        "Title" => "Göndərən şəxs"
    ],
    [
        "InputType" => "file",
        "ColumnName" => "sened_fayl",
    ],
    [
        "InputType" => "file",
        "ColumnName" => "qoshma_fayl",
    ],
];

$chixanSenedForm = new Form($chixanSenedFields);
$chixanSenedForm->check();
$dataToBeInsertedChixanSened = $chixanSenedForm->collectDataToBeInserted();

$id = getInt('id');

$chixanSened = NULL;
if ($id) {
    $chixanSened = new OutgoingDocument($id);
}

$isEdit = $id > 0;
try {
	pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	pdo()->beginTransaction();

	if ($isEdit) {
		SQL::update(OutgoingDocument::TABLE_NAME, $dataToBeInsertedChixanSened, $id);
        $chixanSened->setStatus(OutgoingDocument::STATUS_TESTIQLEMEDE);
        \Service\Confirmation\Confirmation::butunShexslerinStatuslariniYenile(OutgoingDocument::class, $id);

        $od = new OutgoingDocument($id);
        $history = new \History\History($od);
        $history->create(['operation' => 'edit', 'note' => $dataToBeInsertedChixanSened['qeyd']]);

	} else {
//		$dataToBeInsertedChixanSened['created_by'] = $userId;
//		$dataToBeInsertedChixanSened['created_at'] = date('Y-m-d H:i:s');
//		$dataToBeInsertedChixanSened['is_deleted'] = 0;
//		$dataToBeInsertedChixanSened['TenantId'] = $user->getActiveTenantId();
//
//        SQL::insert('tb_chixan_senedler', $dataToBeInsertedChixanSened);
//        $id = SQL::getLastId('tb_chixan_senedler');
//        SQL::insert('tb_chixan_senedler_log', [
//            'sened_id'  => $id,
//            'user_id'   => $userId,
//            'ne'        => 'sened_qeydiyyatdan_kecib'
//        ]);
	}

    $chixanSenedForm->saveFiles($id, 'chixan_senedler', PRODOC_FILES_SAVE_PATH);

    pdo()->commit();
	$user->success_msg();

} catch (Exception $e) {
	pdo()->rollBack();
	$user->error_msg($e->getMessage());
}