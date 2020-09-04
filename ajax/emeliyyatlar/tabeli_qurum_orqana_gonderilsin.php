<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$daxil_olan_sened_id = getRequiredPositiveInt('daxil_olan_sened_id');

$chixanSenedFields = [
    [
        "IsRequired" => true,
        "Title" => "Kim göndərir",
        "InputType" => "select",
        "ColumnName" => "kim_gonderir"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd"
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "teyinat",
        "Title" => "Təyinat"
    ],
    [
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "gonderen_teshkilat",
        "Title" => "Göndərən təşkilat"
    ]
];

$chixanSenedForm = new Form($chixanSenedFields);
$chixanSenedForm->check();
$dataToBeInsertedChixanSened = $chixanSenedForm->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    $dataToBeInsertedChixanSened['created_by'] = $userId;
    $dataToBeInsertedChixanSened['created_at'] = date('Y-m-d H:i:s');
    $dataToBeInsertedChixanSened['is_deleted'] = 0;
    $dataToBeInsertedChixanSened['TenantId'] = $user->getActiveTenantId();

    $gonderen_teshkilatlar = $dataToBeInsertedChixanSened['gonderen_teshkilat'];
    $tableDatasCount = count($gonderen_teshkilatlar);

    for ($i = 0; $i < $tableDatasCount; $i++)
    {
        $dataToBeInsertedChixanSened['gonderen_teshkilat'] = $gonderen_teshkilatlar[$i];
        $dataToBeInsertedChixanSened['gonderen_shexs'] = 0;

        DB::insert('tb_chixan_senedler', $dataToBeInsertedChixanSened);
        $_module_entry_id = DB::getLastId('tb_chixan_senedler');
        $_module_entry_ids[] = $_module_entry_id;

        DB::insert('tb_chixan_senedler_log', [
            'sened_id'  => $_module_entry_id,
            'user_id'   => $userId,
            'ne'        => 'sened_qeydiyyatdan_kecib'
        ]);
    }

//
//    foreach ($_module_entry_ids as $id) {
//        $chixanSenedForm->saveFiles($id, 'chixan_senedler', PRODOC_FILES_SAVE_PATH);
//    }

    $priv = new Privilegiya();
    $netice_bir_icrachi_olduqda_priv = $priv->getByExtraId('netice_bir_icrachi_olduqda');
    $netice_selectinin_goster = $netice_bir_icrachi_olduqda_priv === 1;

    if ($netice_selectinin_goster) {

        $netice = getInt('netice');
        DB::update('tb_daxil_olan_senedler', [
            'netice' => $netice
        ], $daxil_olan_sened_id);

    }

    pdo()->commit();
    $user->success_msg();

} catch (Exception $e) {
    pdo()->rollBack();
    $user->error_msg($e->getMessage());
}

