<?php


session_start();
require_once '../../../class/phpword/PHPWord.php';
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
$user = new User();
if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}
foreach ($_FILES as $columnName => $file) {

    $fileIds = [];

    $files = saveFiles($columnName, PRODOC_FILES_SAVE_PATH, true, false);

    for ($j = 0, $lenj = count($files); $j < $lenj; ++$j) {

        $fileIds [] = SQL::insertAndReturnId('tb_files', [
            'module_name' => $columnName,
            'module_entry_id' => 0,
            'file_original_name' => $files[$j]['file_original_name'],
            'file_actual_name' => $files[$j]['file_actual_name'],
            'created_by' => (int)$_SESSION['erpuserid'],
        ]);
    }
    $inserted_files = DB::fetchAll('SELECT id, file_actual_name FROM tb_files WHERE id IN (' . implode(",", $fileIds) . ')');
    print json_encode([
        'status' => 'success',
        'files' => $inserted_files
    ]);

}

