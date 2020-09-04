<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';

$user = new User();
$senedId = get('sened_id');

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}
$userId = (int)$_SESSION['erpuserid'];

$fields= [
    [
        "InputType" => "id",
        "ColumnName" => "id"
    ],
    [
        "Title" => "Sənəd",
        "InputType" => "file",
        "ColumnName" => "sened_fayl"
    ]
];


try{
    $form = new Form($fields);

    $form->check();

    $dataToBeInserted = $form->collectDataToBeInserted();

    if(isset($_FILES['upload_file']) && isset($_FILES['upload_file']['name']))
    {
        $form = saveFiles('upload_file', PRODOC_FILES_SAVE_PATH.'/comment/',true);

        for ($j = 0, $lenj = count($form); $j < $lenj; ++$j) {
            SQL::insert('tb_files', [
                'module_name' => 'comments_fayl',
                'module_entry_id' => 0,
                'file_original_name' => $form[$j]['file_original_name'],
                'file_actual_name' => $form[$j]['file_actual_name'],
                'created_by' => $userId,
            ]);
        }

    }

    print json_encode([
        'status' => 'success',
        'id' => $dataToBeInserted['id']
    ]);
    exit();
}
catch (Exception $e){
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var' . $e->getMessage()]
    ]);
}


