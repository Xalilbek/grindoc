<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 12.04.2018
 * Time: 18:49
 */
namespace Service\Confirmation;

use PowerOfAttorney\PowerOfAttorney;

require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/model/IConfirmable.php';

class Confirmation
{
    private $className;
    private $entityId;
    private $entity;

    const NO_ORDER = NULL;

    public function __construct(\IConfirmable $entity)
    {
        $this->entity = $entity;
        if (method_exists($entity, 'getClassNameForConfirmation')) {
            $this->className = $entity->getClassNameForConfirmation();
        } else {
            $this->className = get_class($entity);
        }

        $this->entityId  = $entity->getId();
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function createConfirmingUsers(array $users, $triggerEvents = true): void
    {
        foreach ($users as $user) {
            if (array_key_exists('order', $user)) {
                if ($user['order'] === 'next_order') {
                    $order = $this->getCurrentOrder() + 1;
                }
                else {
                    $order = $user['order'];
                }
            } else {
                $order = $this->entity->getApproveOrder($user);
            }

            if (array_key_exists('created_by', $user)) {
                $createdBy = $user['created_by'];
            } else {
                $createdBy = isset($_SESSION['erpuserid']) ? $_SESSION['erpuserid'] : NULL;
            }


            \DB::insert('tb_prodoc_testiqleyecek_shexs', [
                'order'             => $order,
                'user_id'           => $user['user_id'],
                'tip'               => $user['type'],
                'related_record_id' => $this->entityId,
                'related_class'     => $this->className,
                'status' => \TestiqleyecekShexs::STATUS_TESTIQLEMEYIB,
                'created_by' => $createdBy
            ]);


        }

        if ($triggerEvents) {
            if (count($users)) {
                $this->entity->onStatusChange($this->entity::STATUS_TESTIQLEMEDE, $this);
            } else {
                $this->entity->onStatusChange($this->entity::STATUS_TESTIQLENIB, $this);
                $this->entity->onFullApprove();
            }
        }
    }

    public function addNewConfirmingUsers(array $users, $compareStatus = true): void
    {
        $this->setOrder($users);

        $this->createConfirmingUsers(
            $this->removeDuplicateConfirmations(
                $users,
                $this->getApprovingUsers(),
                $compareStatus
            )
        );
    }

    public function setOrder(array &$confirmations): void
    {
        $confirmations = array_map(function ($confirmation) {
            if (!array_key_exists('order', $confirmation)) {
                $confirmation['order'] = $this->entity->getApproveOrder($confirmation);
            }

            return $confirmation;
        }, $confirmations);
    }

    private function removeDuplicateConfirmations(
        array $confirmations,
        array $compareWith,
        $compareStatus = true
    ): array
    {
        $newConfirmations = [];

        $lenJ = count($compareWith);
        for ($i = 0, $len = count($confirmations); $i < $len; ++$i) {
            if (!array_key_exists('status', $confirmations[$i])) {
                $confirmations[$i]['status'] = 0;
            }

            for ($j = 0; $j < $lenJ; ++$j) {
                if (
                    (int)$confirmations[$i]['user_id'] === (int)$compareWith[$j]['user_id'] &&
                    (int)$confirmations[$i]['order']   === (int)$compareWith[$j]['order'] &&
                    $confirmations[$i]['type']         === $compareWith[$j]['type'] &&
                    ($compareStatus ? ((int)$confirmations[$i]['status'] === (int)$compareWith[$j]['status']) : true)
                )
                    continue 2;
            }

            $newConfirmations[] = $confirmations[$i];
        }

        return $newConfirmations;
    }

    private $operationsCache = [];

    public function getApprovingUsers($status = NULL)
    {
        if (is_null($status)) {
            $objectHash = spl_object_hash($this->entity);
            if (array_key_exists($objectHash, $this->operationsCache)) {
                return $this->operationsCache[$objectHash];
            }
        }

        $sql = sprintf("
            SELECT t1.user_id, t1.tip AS type, t1.id, t1.status, t2.user_ad_qisa, t1.[order], t1.is_deleted
            FROM tb_prodoc_testiqleyecek_shexs AS t1
            LEFT JOIN v_user_adlar AS t2 ON t1.user_id = t2.USERID
            WHERE
            t1.related_class = %s AND
            t1.related_record_id = %s
            %s
        ",
            \DB::quote($this->className),
            $this->entityId,
            is_null($status) ? '' : " AND status = $status "
        );

        if (is_null($status)) {
            return $operationsCache[$objectHash] = \DB::fetchAll($sql);
        } else {
            return \DB::fetchAll($sql);
        }
    }

    public function getApprovingUsersOfGroup($status = NULL, $tip = NULL)
    {
        $sql = sprintf("
            SELECT t1.user_id, t1.tip AS type, t1.id, t1.status, t2.user_ad_qisa, t1.[order],
            tb_prodoc_group_user.group_id, tb_prodoc_group.name as group_name
            FROM tb_prodoc_testiqleyecek_shexs AS t1
            LEFT JOIN v_user_adlar AS t2 ON t1.user_id = t2.USERID
            LEFT JOIN tb_prodoc_group_user on tb_prodoc_group_user.user_id=t2.USERID
            LEFT JOIN tb_prodoc_group on tb_prodoc_group.id = tb_prodoc_group_user.group_id
            WHERE
            t1.related_class = %s AND
            t1.related_record_id = %s
            AND t1.user_id is not null
          
            %s 
            %s 
              
            ORDER BY tb_prodoc_group_user.group_id desc 
        ",
            \DB::quote($this->className),
            $this->entityId,
            is_null($status) ? '' : " AND status = $status ",
            is_null($tip) ? '' : " AND t1.tip = '$tip' "
        );
        return \DB::fetchAll($sql);
    }

    public function removeAllConfirmations()
    {
        $sql = sprintf("
            DELETE
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE 
            related_class = %s AND
            related_record_id = %s
        ",
            \DB::quote($this->className),
            $this->entityId
        );

        return \DB::exec($sql);
    }

    public static $generalTypeConfirmations = [];
    public static function setConfirmationForGeneralType($type, callable $callback)
    {
        // callback must return list of users
        self::$generalTypeConfirmations[$type] = $callback;
    }

    public function getCurrentApprovingTypes()
    {
        $currentOrder = $this->getCurrentOrder();

        if (NULL === $currentOrder) {
            return [];
        }

        $sql = sprintf("
            SELECT tip
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE
            related_class = %s AND 
            related_record_id = %s AND 
            status = 0 AND 
            [order] = %s AND
            (is_deleted IS NULL OR is_deleted = 0)
        ",
            \DB::quote($this->className),
            $this->entityId,
            $currentOrder
        );

        return array_unique(\DB::fetchColumnArray($sql));
    }

    public function getCurrentApprovingUsers($returnFirst = false)
    {
        $currentOrder = $this->getCurrentOrder();

        if (NULL === $currentOrder) {
            return [];
        }

        $sql = sprintf("
            SELECT %s user_id, tip, id, related_record_id, [order]
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE
            related_class = %s AND 
            related_record_id = %s AND 
            status = 0 AND 
            [order] = %s AND
            (is_deleted IS NULL OR is_deleted = 0)
        ",
            $returnFirst ? ' TOP 1 ' : '',
            \DB::quote($this->className),
            $this->entityId, $currentOrder
        );

        if ($returnFirst) {
            return \DB::fetch($sql);
        } else {
            $currentConfirmingUsers = [];

            foreach (\DB::fetchAll($sql) as $currentConfirmation) {
                foreach ($this->getUsersForConfirmation($currentConfirmation) as $cUserId) {
                    $currentConfirmingUsers[$cUserId] = [
                        'user_id' => $cUserId,
                        'tip' => $currentConfirmation['tip'],
                        'id'  => $currentConfirmation['id'],
                        'related_record_id' => $currentConfirmation['related_record_id'],
                        'order' => $currentConfirmation['order'],
                    ];
                };
            }

            return $currentConfirmingUsers;
        }
    }

    public function isCancel()
    {
        $sql = sprintf("
            SELECT  status
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE
            related_class = %s AND 
            related_record_id = %s AND 
            status = 2 AND 
            (is_deleted IS NULL OR is_deleted = 0)
        ",
            \DB::quote($this->className),
            $this->entityId
        );
        $isCanceled = \DB::fetch($sql);

        if ($isCanceled == false) {
            return false;
        } else {
            return true;
        }
    }

    public function hasOperationExecuted(string $operationName): bool {
        foreach ($this->getAllOperations() as $operation) {
            if ($operation['type'] === $operationName && (int)$operation['status'] === 1) {
                return true;
            }
        }

        return false;
    }

    public function getAllowedOperations(PowerOfAttorney $poa) {
        $currentOrder = (int)$this->getCurrentOrder();

        $allowedOperations = [];
        foreach ($this->getAllOperations() as $operation) {
            $order = (int)$operation['order'];
            $allowedOrder = $order === 0 || $order === $currentOrder;
            if (false === $allowedOrder) {
                continue;
            }

            if ((int)$operation['is_deleted'] === 1) {
                continue;
            }

            if (!$poa->canExecute($operation['user_id'])) {
                continue;
            }

            if (method_exists($this->entity, 'canExecuteOperation')) {
                if (false === $this->entity->canExecuteOperation($operation, $this)) {
                    continue;
                }
            }

            $allowedOperations[] = $operation;
        }

        return $allowedOperations;
    }

    public function getAllowedOperationsOfAllUsers() {
        $currentOrder = (int)$this->getCurrentOrder();

        $allowedOperations = [];
        foreach ($this->getAllOperations() as $operation) {
            $order = (int)$operation['order'];
            $allowedOrder = $order === 0 || $order === $currentOrder;
            if (false === $allowedOrder) {
                continue;
            }

            if ((int)$operation['is_deleted'] === 1) {
                continue;
            }

            if (method_exists($this->entity, 'canExecuteOperation')) {
                if (false === $this->entity->canExecuteOperation($operation, $this)) {
                    continue;
                }
            }

            $allowedOperations[] = $operation;
        }

        return $allowedOperations;
    }

    public function getAllOperations() {
        return $this->getApprovingUsers();
    }

    public function getUsersForConfirmation($confirmation)
    {
        $users = [];
        $type  = $confirmation['tip'];

        if (array_key_exists($type, self::$generalTypeConfirmations)) {
            $users = self::$generalTypeConfirmations[$type]($this);
        } else {
            $users = [(int)$confirmation['user_id']];
        }

        return $users;
    }


    public function getCurrentOrder()
    {
        $sql = sprintf("
            SELECT MIN([order])
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE
            related_class = %s AND 
            related_record_id = %s AND
            status = 0 AND
            (is_deleted IS NULL OR is_deleted = 0)
        ", \DB::quote($this->className), $this->entityId);

        return \DB::fetchColumn($sql);
    }

    public function getMaxOrder()
    {
        $sql = sprintf("
            SELECT MAX([order])
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE
            related_class = %s AND 
            related_record_id = %s
        ", \DB::quote($this->className), $this->entityId);

        return \DB::fetchColumn($sql);
    }

    public static function butunShexslerinStatuslariniYenile($class, $recordId)
    {
        $class = pdo()->quote($class);
        $recordId = (int)$recordId;

        $sql = sprintf("
            UPDATE tb_prodoc_testiqleyecek_shexs
            SET status = %s, status_changed_at = GETDATE()
            WHERE
            related_class = {$class} AND 
            related_record_id = {$recordId}
        ", \TestiqleyecekShexs::STATUS_TESTIQLEMEYIB);

        \DB::query($sql);
    }
}