<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
require_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$taskId = getRequiredPositiveInt('id');

$task = new Task($taskId);
$task->changeStatus(Task::STATUS_QEBUL_OLUNUB);

$user->success_msg();