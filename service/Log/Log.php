<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 14.06.2018
 * Time: 16:40
 */

namespace Log;

require_once 'ILog.php';

use DB;
use Exception;


class Log
{
    private $entity;

    CONST OPERATION_DELETE = 'delete';
    CONST OPERATION_RESET = 'reset';
    CONST OPERATION_STATUS_CHANGE = 'status_change';

    public function __construct( ILog $entity)
    {
        $this->entity = $entity;
    }

    public function create(array $data)
    {



        // TODO: Check existense in log_operation array



        $id = DB::insertAndReturnId('tb_prodoc_admin_operation_logs', [
            'document_type' => $this->entity->getLogKey(),
            'related_record_id' => $this->entity->getId(),
            'operation' => $data['operation'],
            'operator_id' => NULL,
            'log_text' => $data['log_text']
        ]);



        return $id;
    }
}