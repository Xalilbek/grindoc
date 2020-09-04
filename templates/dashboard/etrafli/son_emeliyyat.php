<?php
include_once '../../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'prodoc/ajax/dashboard/tarixce/queryAndTranslate.php';
include_once DIRNAME_INDEX . 'prodoc/ajax/dashboard/tarixce/history_operation.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';

$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$fromParent = false;

function son_emeliyyat($task_id, $sened_id) {

    global $relatedKeyAndOperationTextMap, $operationTextMap;

    $documentId = $sened_id;
    $relatedTasks = $task_id;

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
            tb1.daxil_olan_sened_id = $documentId AND tb1.derkenar_id IS NULL
    ";
    $outgoingDocuments = DB::fetchAllIndexed($sql, 'id');


    $sql = "SELECT id
        FROM tb_prodoc_muraciet
        WHERE
        daxil_olan_sened_id = $documentId AND derkenar_id IS NULL AND tip = 'ishe_tik'";
    $appeals = DB::fetchColumnArray($sql);

    $conds = [
        "(history.related_key = 'document' AND history.related_record_id = '$documentId')"
    ];

    if (count($relatedTasks)) {
        $conds[] = sprintf(
            "(history.related_key = 'task' AND history.related_record_id IN (%s))",
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

    $sql = "
        SELECT TOP 1
            history.id,
            history.created_at,
            history.operation,
            v_users.user_name,
            history.related_key,
            history.related_record_id,
            history.note,
            CONCAT(history.related_key, '_', history.operation) AS key_and_operation,
            task.id AS taskID,
            type.name as internal_document_type
        FROM
            tb_prodoc_history history
        LEFT JOIN v_users
         ON history.operator_id = v_users.USERID
        LEFT JOIN tb_derkenar task
         ON history.related_key = 'task' AND task.id = history.related_record_id
        LEFT JOIN tb_prodoc_muraciet M
         ON history.related_key = 'appeal' AND history.related_record_id = M.id 
        LEFT JOIN tb_daxil_olan_senedler D
         ON D.id = M.related_document_id
		LEFT JOIN tb_prodoc_inner_document_type type
		 ON type.id = D.internal_document_type_id
        WHERE (%s) AND history.operation <> 'create_approve_group_tanish_ol'
        AND (
            history.operation IN (%s) OR 
            CONCAT(history.related_key, '_', history.operation) IN (%s)
        )
        ORDER BY history.created_at DESC
    ";

    $historyRecord = DB::fetch(sprintf(
        $sql,
        implode(' OR ', $conds),
        DB::arrayToSqlList(array_keys($operationTextMap)),
        DB::arrayToSqlList(array_keys($relatedKeyAndOperationTextMap))
    ));

    $historyRecordLogs = [];
    if (!empty($historyRecord)) {
        $historyRecordLogs =  DB::fetchAll("
            SELECT
                history_log.prodoc_history_id,
                history_log.column_name,
                history_log.column_value
            FROM tb_prodoc_history_log  history_log
            WHERE history_log.prodoc_history_id = ".$historyRecord['id']);
    }


    $operationText = "";
    if ((int)$historyRecord['taskID']) {
        $task = new Task($historyRecord['taskID']);
        if ($task->isSubTask()) {
            $relatedKeyAndOperationTextMap['task_registration'] = 'Alt dərkənar yazıldı';
            $relatedKeyAndOperationTextMap['task_edit']               = 'Alt dərkənara düzəliş edildi';
            $relatedKeyAndOperationTextMap['task_status_change_to_2'] = 'Alt dərkənar icraya götürüldü';
            $relatedKeyAndOperationTextMap['task_status_change_to_3'] = 'Alt dərkənardan imtina edildi';
        }
    }

    if ($historyRecord['key_and_operation'] === "appeal_registration" && !empty($historyRecord['internal_document_type'])) {
        $operationText = $historyRecord['internal_document_type'] . " qeydiyyatdan keçirilib.";
    }
    elseif(isset($relatedKeyAndOperationTextMap[$historyRecord['key_and_operation']]))
    {
        $operationText = $relatedKeyAndOperationTextMap[$historyRecord['key_and_operation']];
    }
    else if (isset($operationTextMap[$historyRecord['operation']]))
    {
        $operationText = $operationTextMap[$historyRecord['operation']];
    }

    if ('outgoing_document_registration' === $historyRecord['key_and_operation'] || 'outgoing_document_edit' === $historyRecord['key_and_operation']) {

        $teyinat_ad    = findColumnValue('teyinat_ad', $historyRecord, $historyRecordLogs);
        $operationText = sprintf($operationText, $teyinat_ad);
    }

    print  $operationText;
}