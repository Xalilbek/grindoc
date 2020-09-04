<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
require_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
$user = new User();
$id=$_POST['id'];

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}
$sessionUserInfo = $user->getUserInfo();
$username = $user->tmzle($sessionUserInfo['USERID']);

$daxil_olan_sened_id = getRequiredPositiveInt('id');
$sebeb = get('sebeb');

$derkenar_id = DB::fetchColumn(
    sprintf(
        "SELECT id
         FROM tb_derkenar
         WHERE daxil_olan_sened_id = %s
         AND mesul_shexs = %s",
        $daxil_olan_sened_id,
        $user->getSessionUserId()
    )
);

if (FALSE === $derkenar_id) {
    $user->error_msg('Access error!');
}

$derkenar = new Task($derkenar_id);
$derkenar->changeStatus(Task::STATUS_IMTINA_OLUNUB, $sebeb);
$checkParentTask = pdof()->query("Select parentTaskId  FROM tb_derkenar where id=".$derkenar_id." AND daxil_olan_sened_id=".$id)->fetch();

if ($checkParentTask[0]!='' && $checkParentTask[0]!=null && $checkParentTask[0]!='0' ){
    $document_number = pdof()->query("Select top 1 document_number, (Select TOP 1 mesul_shexs  FROM tb_derkenar 
                    where parentTaskId=0 AND daxil_olan_sened_id=".$daxil_olan_sened_id.") as mesul, 
                    (SELECT  CONCAT(Adi,' ' , Soyadi) as user_name FROM tb_users WHERE USERID=".$username."),  
                    USERID, yoxlayan_shexs from tb_daxil_olan_senedler left JOIN tb_users on created_by=USERID where id=".$id)->fetch();

    $user->sendNotifications( true, true,
        'mesul_icrachi_imtina_etdi',
        $document_number[2], "",
        $id,
        $document_number[1],
        "mesul_icrachi_imtina_etdi",
        "",
        "",
        "",
        $document_number[0],
        "daxil_olan_sened",
        "imtina"
    );
}
else{
    $document_number = pdof()->query("Select top 1 document_number, rey_muellifi, (SELECT  CONCAT(Adi,' ' , Soyadi) as user_name FROM tb_users WHERE USERID=".$username."),  USERID, yoxlayan_shexs from tb_daxil_olan_senedler left JOIN tb_users on created_by=USERID where id=".$id)->fetch();

    $user->sendNotifications( true, true,
        'mesul_shexs_imtina_etdi',
        $document_number[2], "",
        $id,
        $document_number[1],
        "mesul_shexs_imtina_etdi",
        "",
        "",
        "",
        $document_number[0],
        "daxil_olan_sened",
        "imtina"
    );
}

$user->success_msg();