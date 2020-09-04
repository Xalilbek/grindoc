<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 14.06.2018
 * Time: 16:40
 */

namespace History;

require_once 'IHistory.php';

use DB;
use Exception;

class History
{
    private static $lastInsertedId;
    private $entity;

    public function __construct(IHistory $entity)
    {
        $this->entity = $entity;
    }

    public function create(array $data)
    {
        if (!array_key_exists('note', $data)) {
            $data['note'] = NULL;
        }

        if (!array_key_exists('operation', $data)) {
            throw new Exception('operation is required');
        }

        // TODO: Check existense in history_operation array

        $operator_id = (int)$_SESSION['erpuserid'];
        if (array_key_exists('operator_id', $data)) {
            $operator_id = $data['operator_id'];
        }

        $poaId = null;
        if (array_key_exists('poa', $data) && count($data['poa']) > 0) {
            $poaId = $data['poa'][0]['id'];
        }

        $id = DB::insertAndReturnId('tb_prodoc_history', [
            'related_key'       => $this->entity->getHistoryKey(),
            'related_record_id' => $this->entity->getId(),
            'operation'         => $data['operation'],
            'operator_id'       => $operator_id,
            'note'              => $data['note'],
            'poa_id' => $poaId
        ]);

        $loggedData = $this->entity->getLoggedData($data['operation']);

        if (is_array($loggedData) && count($loggedData)) {
            foreach ($loggedData as $columnName => $columnValue) {
                DB::insertAndReturnId('tb_prodoc_history_log', [
                    'prodoc_history_id' => $id,
                    'column_name'       => $columnName,
                    'column_value'      => $columnValue
                ]);
            }
        }

        self::$lastInsertedId = $id;
        return $id;
    }

    public static function getLastInsertedId()
    {
        return self::$lastInsertedId;
    }
}