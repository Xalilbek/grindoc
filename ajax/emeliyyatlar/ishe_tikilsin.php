<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
require_once DIRNAME_INDEX . 'prodoc/component/Form.php';
$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}
$poa_user_id = getInt('poa_user_id', 0);

$fields= [
    [
        "Title" => "Sənəd",
        "InputType" => "file",
        "ColumnName" => "sened_fayl"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "poa_user_id"
    ],
    [
        "IsRequired" => false,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => "confirming_users"
    ],
];
$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();


$userId = $_SESSION['erpuserid'];

$qeyd   = get('qeyd');

$daxil_olan_sened_id = getInt('daxil_olan_sened_id', 0);
$taskId              = getInt('taskId', 0);

try {
    DB::beginTransaction();

    $doc = new Document($daxil_olan_sened_id);
    $netice_selectinin_goster = $doc->neticeniDaxilEdeBiler();

    if ($taskId) {
        $task = new Task($taskId);

        if (Appeal::hasOpenedOutgoingDocuments($task)) {
            $user->error_msg("İlk öncə xaric olan sənədi ləğv edib sonra işə tikmək lazımdır.");
        }

        $netice = 0;
        if ($netice_selectinin_goster) {
            $netice = getRequiredPositiveInt('netice');
        }

        $appeal = Appeal::create([
            'derkenar_id' => $taskId,
            'tip' => Appeal::TIP_ISHE_TIKILSIN,
            'note' => $qeyd,
            'netice_id' => $netice,
            'confirming_users' => $dataToBeInserted['confirming_users'],
            'poa_user_id' => $poa_user_id
        ], $user);

        if ($task->isSubTask()) {
            $notifyForMesulShexs = pdof()->query("Select daxil_olan_sened_id, (SELECT document_number 
        from v_daxil_olan_senedler WHERE v_daxil_olan_senedler.id=tt.daxil_olan_sened_id  ) 
        as document_number, (SELECT Concat( Adi, ' ', Soyadi) as ad FROM tb_users WHERE USERID = mesul_shexs) as user_name, 
        (SELECT created_by from v_daxil_olan_senedler WHERE v_daxil_olan_senedler.id=tt.daxil_olan_sened_id  ) as qeydiyyatci,
        (Select mesul_shexs 
        FROM tb_derkenar where tt.parentTaskId=tb_derkenar.id ) as mesul_shexs, 
        (SELECT rey_muellifi from v_daxil_olan_senedler where id=tt.daxil_olan_sened_id) as rey_muellifi,
        (SELECT Concat( Adi, ' ', Soyadi) from tb_users WHERE USERID=(Select mesul_shexs FROM tb_derkenar where tt.parentTaskId=tb_derkenar.id ) ) as mesul_shexs_ad
         from tb_derkenar tt  where parentTaskId > 0 and tt.id=".$taskId)->fetch();
            $user->sendNotifications( true, true,
                "ishe_tik",
                $notifyForMesulShexs['user_name'], "",
                $notifyForMesulShexs['daxil_olan_sened_id'],
                $notifyForMesulShexs['rey_muellifi'],
                "ishe_tik",
                "",
                "",
                "",
                $notifyForMesulShexs['document_number'],
                "daxil_olan_sened",
                "ishe_tik"
            );
            $user->sendNotifications( true, true,
                "ishe_tik_qeydiyyatci",
                $notifyForMesulShexs['user_name'], $notifyForMesulShexs['mesul_shexs_ad'],
                $notifyForMesulShexs['daxil_olan_sened_id'],
                $notifyForMesulShexs['qeydiyyatci'],
                "ishe_tik_qeydiyyatci",
                "",
                "",
                "",
                $notifyForMesulShexs['document_number'],
                "daxil_olan_sened",
                "ishe_tik"
            );
        }

    } else {
        $incomingDocument = new Document($daxil_olan_sened_id);

        if (Appeal::hasOpenedOutgoingDocuments($incomingDocument)) {
            $user->error_msg("İlk öncə xaric olan sənədi ləğv edib sonra işə tikmək lazımdır.");
        }

        $netice = 0;
        if ($netice_selectinin_goster) {

            $netice = getRequiredPositiveInt('netice');
            DB::update('tb_daxil_olan_senedler', [
                'netice' => $netice
            ], $daxil_olan_sened_id);

        }

        $appeal = Appeal::create([
            'daxil_olan_sened_id' => $daxil_olan_sened_id,
            'netice_id' => $netice,
            'tip' => Appeal::TIP_ISHE_TIKILSIN,
            'note' => $qeyd,
            'confirming_users' => $dataToBeInserted['confirming_users'],
            'poa_user_id' => $poa_user_id
        ]);

    }

    $form->saveFiles($appeal->getId(), 'muraciet', PRODOC_FILES_SAVE_PATH);


    DB::commit();
} catch (Exception $e) {
    DB::rollBack();

    $user->error_msg($e->getMessage());
}

$user->success_msg();