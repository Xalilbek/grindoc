<?php
use History\History;
session_start();
include_once '../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$sened_id = getRequiredPositiveInt('id');
$sebeb    = get('sebeb');


try {
    $Sened = new Document($sened_id);
    $Sened->legvEt($sebeb);
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

    $user->success_msg();
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

