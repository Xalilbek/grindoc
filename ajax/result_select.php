<?php
session_start();
include_once '../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

try {
    $userId = $_SESSION['erpuserid'];
    $documentList = json_decode(get('docIds'));

    if (!is_array($documentList)) {
        throw new Exception();
    }

    if (count($documentList) === 0) {
        $user->success_msg('0');
    }

    foreach ($documentList as $documentId) {
        $d = new Document($documentId);
        if ($d->neticeniDaxilEdeBiler()) {
            $user->success_msg('1');
        }
    }

    $user->success_msg('0');
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}
