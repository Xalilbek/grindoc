<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';



$user = new User();

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
        "ColumnName" => "loginPageImg"
    ]
];

try{
//    check file is set to be deleted
    if(isset($_POST['sil'])){
        $path = realpath(UPLOADS_DIR_PATH."logos/".getProjectName().".png"); //get image path
        if (!is_writable(UPLOADS_DIR_PATH."logos/")){ // check folder has permission to write
            throw new Exception("Save path is not writable!");
        }
        unlink($path); // delete file
        exit(json_encode(['status'=>'success']));
    }else{

        saveFiles('loginPageImg',UPLOADS_DIR_PATH.'logos',true,getProjectName().".png",['allowedFileExtensions' => ['png']]);

        print json_encode([
            'status' => 'success',
            'id' => 1
        ]);
        exit();
    }

}
catch (Exception $e){
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var: ' . $e->getMessage()]
    ]);
}
