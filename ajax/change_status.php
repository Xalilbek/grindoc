<?php

use History\History;
use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;

session_start();
include_once '../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';

$user = new User();

$id = getInt('id');
$editing = $id > 0;

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

$userId = $_SESSION['erpuserid'];

$id  = getInt('id');

$prodocDaxilOlan = new Document($id);

if ($action === 'rey_muelifi_imtina')
{
    $yeni_rey_muelifi = getInt('yeni_rey_muelifi');
    $sebeb  = get('sebeb');

    $sonEmeliyyat = null;
    if ($yeni_rey_muelifi) {
        DB::update('tb_daxil_olan_senedler', array(
            'rey_muellifi' => $yeni_rey_muelifi
        ), $id);

        // $prodocDaxilOlan->setState(Document::STATE_CANCELED, ['changedByType' => 'rey_muellifi', 'note' => $sebeb]);

        $history = new History(new Document($id));

        $history->create([
            'operation' =>  'state_changed_to_' . Document::STATE_CANCELED . '_by_' . 'rey_muellifi',
            'note' => $sebeb
        ]);
    } else {
        $prodocDaxilOlan->setState(Document::STATE_CANCELED, ['changedByType' => 'rey_muellifi', 'note' => $sebeb]);
        $lastHistoryId = History::getLastInsertedId();
        attachFileToHistory($lastHistoryId);
    }
    $sql = pdof()->query("Select created_by,  CONCAT(Adi, Soyadi) as user_name, document_number from v_daxil_olan_senedler left JOIN tb_users on rey_muellifi=tb_users.USERID where id=".$id)->fetch();
    $user->sendNotifications( true, true,
        'yoxlayici_imtina_etdi',
        $sql[1], "",
        $id,
        $sql[0],
        "yoxlayici_imtina_etdi",
        "",
        "",
        "",
        $sql[2],
        "daxil_olan_sened",
        "imtina"
    );

}
else if ($action === 'rey_muelifi_qebul')
{
    $prodocDaxilOlan->setState(Document::STATE_AUTHOR_ACCEPTED);
}

if ($action === 'yoxlayici_imtina')
{
    $yoxlama = getInt('yoxlama');
    $sebeb  = get('sebeb');

    $sonEmeliyyat = null;
    if ($yoxlama) {
        DB::update('tb_daxil_olan_senedler', array(
            'yoxlayan_shexs' => $yoxlama
        ), $id);

        $prodocDaxilOlan->setState(Document::STATE_CANCELED, ['changedByType' => 'yoxlayan_shexs', 'note' => $sebeb]);
    } else {
        $prodocDaxilOlan->setState(Document::STATE_CANCELED, ['changedByType' => 'yoxlayan_shexs', 'note' => $sebeb]);
        $lastHistoryId = History::getLastInsertedId();
        attachFileToHistory($lastHistoryId);
    }

    $sql = pdof()->query("Select created_by,  CONCAT(Adi, Soyadi) as user_name, document_number from v_daxil_olan_senedler left JOIN tb_users on yoxlayan_shexs=tb_users.USERID where id=".$id)->fetch();
    $user->sendNotifications( true, true,
        'yoxlayici_imtina_etdi',
        $sql[1], "",
        $id,
        $sql[0],
        "yoxlayici_imtina_etdi",
        "",
        "",
        "",
        $sql[2],
        "daxil_olan_sened",
        "imtina"
    );

}
else if ($action === 'yoxlayici_qebul')
{

    require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
    $prodocDaxilOlan->setCustomQuery('v_incoming_document_all', true);

    $documentNumberGeneral = new DocumentNumberGeneral($prodocDaxilOlan);
    $note  = get('note');

    if ((int)$prodocDaxilOlan->getData()['document_number_id']) {
        // nomresi artiq var
        $prodocDaxilOlan->setState((getProjectName() === TS) ? Document::STATE_AUTHOR_ACCEPTED : Document::STATE_INSPECTED,['note' => $note]);

        $sessionUserInfo = $user->getUserInfo();
        $username = $user->tmzle($sessionUserInfo['user_name']);

        $document_number = pdof()->query("Select top 1 document_number, rey_muellifi,  CONCAT(Adi, Soyadi) as user_name,  USERID, yoxlayan_shexs from tb_daxil_olan_senedler left JOIN tb_users on created_by=USERID where id=".$id)->fetch();
        $user->sendNotifications( true, true,
            'prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra',
            $document_number[2], "",
            $id,
            $document_number[1],
            "prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra",
            "",
            "",
            "",
            $document_number[0],
            "daxil_olan_sened",
            "derkenar"
        );
    } else if ($documentNumberGeneral->isNumberEditable() || $documentNumberGeneral->isNumberEditableWithSelect()) {
        // nomresini ozu daxil etmelidir
        $note  = get('note');
        $prodocDaxilOlan->setState(Document::STATE_NUMBER_REQUIRED,['note' => $note]);

        $sessionUserInfo = $user->getUserInfo();
        $username = $user->tmzle($sessionUserInfo['user_name']);

        $document_number = pdof()->query("Select top 1 document_number, rey_muellifi,  CONCAT(Adi, Soyadi) as user_name,  USERID, yoxlayan_shexs from tb_daxil_olan_senedler left JOIN tb_users on created_by=USERID where id=".$id)->fetch();
        $user->sendNotifications( true, true,
            'prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra_nomre',
            $document_number[2], "",
            $id,
            $userId,
            "prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra_nomre",
            "",
            "",
            "",
            $document_number[0],
            "daxil_olan_sened",
            "derkenar"
        );
    } else {
        $note  = get('note');
        // nomresi yoxdur ve elnen hecne yazilmir!
        $prodocDaxilOlan->setState( (getProjectName() === TS) ? Document::STATE_AUTHOR_ACCEPTED : Document::STATE_INSPECTED, ['note' => $note]);
        $documentNumberGeneral->assignNumber();

        $sessionUserInfo = $user->getUserInfo();
        $username = $user->tmzle($sessionUserInfo['user_name']);

        $document_number = pdof()->query("Select top 1 document_number, rey_muellifi,  CONCAT(Adi, Soyadi) as user_name,  USERID, yoxlayan_shexs from tb_daxil_olan_senedler left JOIN tb_users on created_by=USERID where id=".$id)->fetch();
        $user->sendNotifications( true, true,
            'prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra',
            $document_number[2], "",
            $id,
            $document_number[1],
            "prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra",
            "",
            "",
            "",
            $document_number[0],
            "daxil_olan_sened",
            "derkenar"
        );
    }
}
else if ($action === "yoxlayan_save_number")
{
    if ((int)$prodocDaxilOlan->getData()['document_number_id'] === 0) {
        $f = [
            [
                "IsRequired" => true,
                "InputType" => "text",
                "ColumnName" => "document_number",
                "Title" => "Sənədin №-si"
            ],
            [
                "InputType" => "checkbox",
                "ColumnName" => "editable_with_select"
            ],
        ];

        try {
            $note  = get('note');

            pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            pdo()->beginTransaction();

            $daxilOlanSenedForm = new Form($f);
            $daxilOlanSenedForm->check();
            $dataToBeInsertedDaxilOlanSened = $daxilOlanSenedForm->collectDataToBeInserted();

            // nomresi yoxdur ve elnen hecne yazilmir!
            require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
            $prodocDaxilOlan->setCustomQuery('v_incoming_document_all', true);

            $documentNumberGeneral = new DocumentNumberGeneral($prodocDaxilOlan, [
                'manualDocumentNumber' => $dataToBeInsertedDaxilOlanSened['document_number'],
                'editable_with_select' => $dataToBeInsertedDaxilOlanSened['editable_with_select']
            ]);

            $prodocDaxilOlan->setState(Document::STATE_INSPECTED,['note' => $note]);
            $documentNumberGeneral->assignNumber();

            $forNotify = $prodocDaxilOlan->getId();
            $sql = "UPDATE tb_notifications SET status='1' WHERE bolme='prodoc_sened_qeydiyyatdan_kecib_yoxlamadan_sonra_nomre' AND kid='$forNotify' AND user_id='$userId'";
            DB::query($sql);

            pdo()->commit();
            $user->success_msg();
        } catch (Exception $e) {
            print json_encode([
                'status' => 'error',
                'errors' => [$e->getMessage()]
            ]);
            pdo()->rollBack();
        }
    }
}

function attachFileToHistory($id)
{
    if (isset($_FILES['sened'])) {
        $files = saveFiles('sened', PRODOC_FILES_SAVE_PATH, false);

        for ($j = 0, $lenj = count($files); $j < $lenj; ++$j) {

            SQL::insert('tb_files', [
                'module_name' => 'history_sened',
                'module_entry_id' => $id,
                'file_original_name' => $files[$j]['file_original_name'],
                'file_actual_name' => $files[$j]['file_actual_name'],
            ]);
        }
    }

}