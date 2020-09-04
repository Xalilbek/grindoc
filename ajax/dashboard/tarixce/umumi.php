<?php
session_start();
include_once '../../../../class/class.functions.php';
include_once 'queryAndTranslate.php';
include_once 'history_operation.php';
$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$fromParent = false;

if (isset($_POST['outgoingDocumentId']) && !isset($_POST['table'])) {
    require_once DIRNAME_INDEX . 'prodoc/includes/outgoing_document.php';
    $_POST['sened_id'] = xosToDos((int)$_POST['outgoingDocumentId'], true);
}

$id = getInt('sened_id');
$tip = isset($_POST['tip']) && is_string($_POST['tip']) ? $_POST['tip'] : 'daxil_olan_sened';




if (
    (
        isset($_POST['sened_id']) &&
        is_numeric($_POST['sened_id']) &&
        (int)$_POST['sened_id'] > 0 &&
        ($tip === 'daxil_olan_sened' || $tip === 'butun_senedler')
    )
    ||
    (
        isset($_POST['relatedKey'])
        && $_POST['relatedKey'] === 'task'
        && isset($_POST['relatedId']) &&
        (int)$_POST['relatedId'] > 0
    )
)
{
    $relatedKey = '';
    if (isset($_POST['sened_id'])) {
        $relatedKey = 'daxil_olan_sened';

        $documentId = (int)$_POST['sened_id'];

        $document = new Document($documentId);
        $relatedTasks = Task::getRelatedTaskIds($document);

        $filter = " tb1.daxil_olan_sened_id = $documentId AND (tb1.derkenar_id IS NULL OR tb1.derkenar_id = 0) ";

        $conds = [
            "(history.related_key = 'document' AND history.related_record_id = '$documentId')"
        ];

    } else {
        $fromParent = true;

        $tip = $relatedKey = 'task';
        $relatedId = $taskId = (int)$_POST['relatedId'];

        $task = new Task($taskId);

        $relatedKeyAndOperationTextMap['task_registration'] = 'Alt dərkənar yazıldı';

        if ($task->isSubTask()) {
            $relatedKeyAndOperationTextMap['task_edit']               = 'Alt dərkənara düzəliş edildi';
            $relatedKeyAndOperationTextMap['task_status_change_to_2'] = 'Alt dərkənar icraya götürüldü';
            $relatedKeyAndOperationTextMap['task_status_change_to_3'] = 'Alt dərkənardan imtina edildi';
        }

        $sql = "
            SELECT id
            FROM tb_derkenar
            WHERE
            parentTaskId = $taskId
        ";
        $relatedTasks = DB::fetchColumnArray($sql);

        $filter = " tb1.derkenar_id = $taskId ";

        $conds = [
            "(history.related_key = 'task' AND history.related_record_id = '$taskId' AND history.operation <> 'registration')"
        ];
    }

    require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
    require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
    require_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';

    $sql = "
        SELECT
            tb2.document_number,
            tb2.id,
            tb2.teyinat_ad,
            gonderen_teshkilat_ad
        FROM
            v_prodoc_outgoing_document_relation AS tb1
        LEFT JOIN v_chixan_senedler AS tb2 ON tb1.outgoing_document_id = tb2.id
        WHERE
            $filter
                    ORDER BY tb2.created_at ASC

    ";

    $outgoingDocuments = DB::fetchAllIndexed($sql, 'id');

    $sql = "SELECT tb1.id
            FROM tb_prodoc_muraciet AS tb1
            WHERE
            $filter AND tb1.tip = 'ishe_tik'";
    $appeals = DB::fetchColumnArray($sql);


    if (count($relatedTasks)) {
        $conds[] = sprintf(
            "(history.related_key = 'task' AND history.operation = 'registration' AND history.related_record_id IN (%s))",
            implode(',', $relatedTasks)
        );
    }

    if (count($appeals)) {
        $conds[] = sprintf(
            "(history.related_key = 'appeal' AND history.related_record_id IN (%s))",
            implode(',', $appeals)
        );
    }

    if (count($outgoingDocuments) && array_keys($outgoingDocuments)[0] != '') {

        $conds[] = sprintf(
            "(history.related_key = 'outgoing_document' AND history.operation IN ('registration', 'edit') AND history.related_record_id IN (%s))",
            implode(',', array_keys($outgoingDocuments))
        );
    }

    $sql = "
        SELECT
            history.id,
            history.created_at,
            history.operation,
            v_users.user_name,
            history.related_key,
            history.related_record_id,
            history.note,
			type.name as internal_document_type,
			document.document_number
        FROM
            tb_prodoc_history history
        LEFT JOIN v_users
         ON history.operator_id = v_users.USERID
        LEFT JOIN tb_prodoc_muraciet M
         ON history.related_key = 'appeal' AND history.related_record_id = M.id 
        LEFT JOIN tb_daxil_olan_senedler D
         ON D.id = M.related_document_id
		LEFT JOIN tb_prodoc_inner_document_type type
		 ON type.id = D.internal_document_type_id
		  LEFT JOIN tb_prodoc_power_of_attorney poa 
          ON poa.id = history.poa_id
        LEFT JOIN v_daxil_olan_senedler document
          ON poa.document_id = document.id
        WHERE %s
                ORDER BY history.created_at ASC

    ";

    $historyRecords = DB::fetchAllIndexed(sprintf($sql, implode(' OR ', $conds)), 'id');

    $sql = "SELECT sened_tip
            FROM tb_daxil_olan_senedler
            WHERE id = '$id'";
    $senedTip = (int)DB::fetchColumn($sql);


    $historyRecordFiles = [];
    if (count($historyRecords) > 0) {
        $sql = sprintf("
        SELECT file_original_name, file_actual_name, module_entry_id
        FROM tb_files
        WHERE module_name = 'history_sened' AND module_entry_id IN (%s)
    ", DB::arrayToSqlList(array_keys($historyRecords)));

        $historyRecordFiles = DB::fetchAllGroupped($sql, 'module_entry_id');
    }

    $historyRecordLogs = [];
    if (!empty($historyRecords)) {
        $sql = sprintf("
            SELECT
                history_log.prodoc_history_id,
                history_log.column_name,
                history_log.column_value
            FROM tb_prodoc_history_log  history_log
            WHERE history_log.prodoc_history_id IN (%s)
        ", implode(',', array_keys($historyRecords)));

        $historyRecordLogs = DB::fetchAll($sql);
    }

    ob_start();
    require DIRNAME_INDEX . 'prodoc/templates/dashboard/tarixce/tarixce.php';
    $temp = ob_get_clean();

    print json_encode(array("status" => "success", "html" => $temp ));
}
else if (
    (
        isset($_POST['sened_id']) && is_numeric($_POST['sened_id']) && (int)$_POST['sened_id'] > 0 &&
        $tip === 'chixan_sened'
    )
    ||
    (
        isset($_POST['from_parent'])
        && isset($_POST['relatedKey'])
        && $_POST['relatedKey'] === 'outgoing_document'
        && isset($_POST['relatedId'])
    )
)
{
    require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
    require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
    require_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';

    $relatedKey = get('relatedKey');
    $relatedId  = get('relatedId');

    if (isset($_POST['from_parent'])) {
        $fromParent = true;
        $documentId = $relatedId;
    } else {
        $documentId = (int)$_POST['sened_id'];
    }

    $document = new OutgoingDocument($documentId);

    $conds = [
        "(history.related_key = 'outgoing_document' AND history.related_record_id = '$documentId')"
    ];

    if ($fromParent) {
        $conds[] = "(history.operation <> 'registration' AND history.operation <> 'edit')";
    }

    $relatedKeyAndOperationTextMap['outgoing_document_registration'] = $relatedKeyAndOperationTextMap['document_registration'];

    $sql = "
        SELECT
            history.id,
            history.created_at,
            history.operation,
            v_users.user_name,
            history.related_key,
            history.related_record_id,
            history.note,
            document.document_number
        FROM
            tb_prodoc_history history
        LEFT JOIN v_users
         ON history.operator_id = v_users.USERID
          LEFT JOIN tb_prodoc_power_of_attorney poa 
          ON poa.id = history.poa_id
        LEFT JOIN v_daxil_olan_senedler document
          ON poa.document_id = document.id
        WHERE %s
    ";
    $historyRecords = DB::fetchAllIndexed(sprintf($sql, implode(' AND ', $conds)), 'id');

    $historyRecordFiles = [];
    if (count($historyRecords) > 0) {
        $sql = sprintf("
        SELECT file_original_name, file_actual_name, module_entry_id
        FROM tb_files
        WHERE module_name = 'history_sened' AND module_entry_id IN (%s)
    ", DB::arrayToSqlList(array_keys($historyRecords)));

        $historyRecordFiles = DB::fetchAllGroupped($sql, 'module_entry_id');
    }

    $historyRecordLogs = [];
    if (!empty($historyRecords)) {
        $sql = sprintf("
            SELECT
                history_log.prodoc_history_id,
                history_log.column_name,
                history_log.column_value
            FROM tb_prodoc_history_log  history_log
            WHERE history_log.prodoc_history_id IN (%s)
        ", implode(',', array_keys($historyRecords)));

        $historyRecordLogs = DB::fetchAll($sql);
    }

    ob_start();
    require DIRNAME_INDEX . 'prodoc/templates/dashboard/tarixce/tarixce.php';
    $temp = ob_get_clean();

    print json_encode(array("status" => "success", "html" => $temp ));
}
else if (
    isset($_POST['from_parent'])
    && isset($_POST['relatedKey'])
    && $_POST['relatedKey'] === 'task'
    && isset($_POST['relatedId']) &&
    (int)$_POST['relatedId'] > 0
)
{
    require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
    require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
    require_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';

    $fromParent = true;

    $relatedKey = get('relatedKey');
    $relatedId  = get('relatedId');

    $taskId = (int)$_POST['relatedId'];
    $tip = $_POST['relatedKey'];

    $task = new Task($taskId);

    $relatedKeyAndOperationTextMap['task_registration'] = 'Alt dərkənar yazıldı';

    if ($task->isSubTask()) {
        $relatedKeyAndOperationTextMap['task_edit']               = 'Alt dərkənara düzəliş edildi';
        $relatedKeyAndOperationTextMap['task_status_change_to_2'] = 'Alt dərkənar icraya götürüldü';
        $relatedKeyAndOperationTextMap['task_status_change_to_3'] = 'Alt dərkənardan imtina edildi';
    }

    $sql = "
            SELECT id
            FROM tb_derkenar
            WHERE
            parentTaskId = $taskId
    ";
    $relatedTasks = DB::fetchColumnArray($sql);

    $sql = "
        SELECT
            tb2.document_number,
            tb2.id,
            tb2.teyinat_ad,
            gonderen_teshkilat_ad
        FROM
            v_prodoc_outgoing_document_relation AS tb1
        LEFT JOIN v_chixan_senedler AS tb2 ON tb1.outgoing_document_id = tb2.id
        WHERE
            tb1.derkenar_id = $taskId
    ";
    $outgoingDocuments = DB::fetchAllIndexed($sql, 'id');

    $sql = "SELECT id
            FROM tb_prodoc_muraciet
            WHERE
            derkenar_id = $taskId AND tip = 'ishe_tik'";
    $appeals = DB::fetchColumnArray($sql);

    $sql="  SELECT
                internal_document_id 
            FROM
                tb_internal_document_relation 
            WHERE
                related_document_id = $documentId 
                AND related_document_type = 'incoming'";

    $task_command_documents = DB::fetchColumnArray($sql);

    $conds = [
        "(history.related_key = 'task' AND history.related_record_id = '$taskId' AND history.operation <> 'registration')"
    ];

    if (count($relatedTasks)) {
        $conds[] = sprintf(
            "(history.related_key = 'task' AND history.operation = 'registration' AND history.related_record_id IN (%s))",
            implode(',', $relatedTasks)
        );
    }

    if (count($appeals)) {
        $conds[] = sprintf(
            "(history.related_key = 'appeal' AND history.related_record_id IN (%s))",
            implode(',', $appeals)
        );
    }

    if (count($outgoingDocuments)) {
        $conds[] = sprintf(
            "(history.related_key = 'outgoing_document' AND history.operation IN ('registration', 'edit') AND history.related_record_id IN (%s))",
            implode(',', array_keys($outgoingDocuments))
        );
    }

    if (count($task_command_documents)) {
        $conds[] = sprintf(
            "(history.related_key = 'document' AND history.operation IN ('registration') AND history.related_record_id IN (%s))",
            implode(',', $task_command_documents)
        );
    }

    $sql = "
        SELECT
            history.id,
            history.created_at,
            history.operation,
            v_users.user_name,
            history.related_key,
            history.related_record_id,
            history.note,
            document.document_number
        FROM
            tb_prodoc_history history
        LEFT JOIN v_users
         ON history.operator_id = v_users.USERID
        LEFT JOIN tb_prodoc_power_of_attorney poa 
          ON poa.id = history.poa_id
        LEFT JOIN v_daxil_olan_senedler document
          ON poa.document_id = document.id
        WHERE %s
    ";
    $historyRecords = DB::fetchAllIndexed(sprintf($sql, implode(' OR ', $conds)), 'id');

    $historyRecordLogs = [];
    if (!empty($historyRecords)) {
        $sql = sprintf("
            SELECT
                history_log.prodoc_history_id,
                history_log.column_name,
                history_log.column_value
            FROM tb_prodoc_history_log  history_log
            WHERE history_log.prodoc_history_id IN (%s)
        ", implode(',', array_keys($historyRecords)));

        $historyRecordLogs = DB::fetchAll($sql);
    }

    ob_start();
    require DIRNAME_INDEX . 'prodoc/templates/dashboard/tarixce/tarixce.php';
    $temp = ob_get_clean();

    print json_encode(array("status" => "success", "html" => $temp));
}
else {
    $sened_id = (int)$_POST['sened_id'];

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $tn = InternalDocument::getTableNameByType($tip);

    $internalDocumentId = DB::fetchOneColumnBy($tn, 'id', [
        'document_id' => $sened_id
    ]);


    $sql = sorguTip($tip, $internalDocumentId);

    if ('' === $sql) {
        $user->error_msg();
    }

    $tarixceler = DB::fetchAll($sql);

    $html = '<div><div class="mt-element-list"><div class="mt-list-container list-news"><ul>';
    foreach ($tarixceler as $tarixce) {

        if (!array_sum($tarixce)) continue;

        $tarixce_ne = tarixceNe($tip, $tarixce['ne']);

        ob_start();
        require DIRNAME_INDEX . 'prodoc/templates/dashboard/tarixce/umumi.php';
        $temp = ob_get_clean();

        $html .= $temp;
    }
    $html .= '</ul></div></div></div>';

    print json_encode(array("status" => "success", "html" => $html ));
}