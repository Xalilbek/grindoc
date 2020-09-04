<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 25.04.2018
 * Time: 10:19
 */

require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/IConfirmable.php';
require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';

use History\IHistory;
use History\History;
use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;
use Model\DocumentNumber\DocumentNumberGeneral\IDocument;
use PowerOfAttorney\PowerOfAttorney;
use Service\Confirmation\Confirmation;

abstract class BaseEntity
{
    protected $id;
    private $info;
    private $customQuery;
    private $params;
    private $infotemp = [];

    public function __construct($id = NULL, $params = NULL)
    {
        $this->id = $id;

        if (is_string($params)) {
            $this->customQuery = $params;
        } elseif (is_array($params)) {
            $this->params = $params;
        }

        if (is_array($params)) {
            if (array_key_exists('info', $params)) {
                $this->info = $params['info'];
            } elseif (array_key_exists('data', $params)) {
                $this->info = $params['data'];
            }

            if (array_key_exists('customQuery', $params)) {
                $this->customQuery = $params['customQuery'];
            }
        }
    }

    public function getId():int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setCustomQuery(string $customQuery, $isView = false):void
    {
        if ($isView) {
            // TODO: extract view from subquery
            $customQuery = "SELECT * FROM {$customQuery}";
        }

        // changed?
        if ($this->customQuery !== $customQuery) {
            $this->customQuery = $customQuery;
            $this->reload();
        }
    }

    public function reload()
    {
        $this->info = null;
    }

    public function __get($name)
    {
        if ('info' === $name || 'record' === $name || 'data' === $name) {
            return $this->getInfo();
        }

        return NULL;
    }

    /**
     * Alias to getInfo
     * @return mixed
     */
    public function getData()
    {
        return $this->getInfo();
    }

    public function getInfo()
    {
        if (is_null($this->info)) {

            $this->info = DB::fetchById(
                is_null($this->customQuery) ?
                    $this->getTableName() :
                    "({$this->customQuery})",
                $this->id
            );

        }

        foreach ($this->infotemp as $c =>$tmp) {
            $this->info[$c] = $tmp;
        }
        $this->infotemp = [];
        return $this->info;
    }

    public function setInfo($info)
    {
        $this->info = $info;
    }

    public function setRecordColumnValue($column, $value): void
    {
        $this->infotemp[$column] = $value;
    }

    public static function getIsDeletedColumnName()
    {
        return 'is_deleted';
    }

    public function getLoggedData(string $operation)
    {
        return null;
    }

    public static function create(array $data, User $user = null)
    {
        if (!is_null($user)) {
            $data['created_by'] = $user->getSessionUserId();
            $data['TenantId']   = $user->getActiveTenantId();
        }

        if (null !== static::getIsDeletedColumnName()) {
            $data[static::getIsDeletedColumnName()] = 0;
        }


        $id = DB::insertAndReturnId((new static())->getTableName(), $data);

        $self = new static($id);
        $self->setInfo($data);

        if ($self instanceof IHistory) {
            $history = new History($self);
            $history->create([
                'operation' => 'registration'
            ]);
        }

        return $self;
    }

    public static function createWithAdditionalParams(array $data, User $user = null, $params = [])
    {


        if (!is_null($user)) {
            $data['created_by'] = $user->getSessionUserId();
            $data['TenantId']   = $user->getActiveTenantId();
        }

        if (null !== static::getIsDeletedColumnName()) {
            $data[static::getIsDeletedColumnName()] = 0;
        }

        $id = DB::insertAndReturnId((new static())->getTableName(), $data);

        $self = new static($id);
        $self->setInfo($data);

        if ($self instanceof IHistory) {
            $history = new History($self);
            $history->create([
                'operation' => 'registration',
                'note' => array_key_exists('note', $params) ? $params['note'] : NULL
            ]);
        }

        return $self;
    }

    public function onStatusChange($newStatus, Confirmation $confirmation)
    {
        if ($this instanceof IBaseEntity && null !== $this->getStatusColumnName()) {
            DB::update($this->getTableName(), [
                $this->getStatusColumnName() => $newStatus
            ], $this->getId());
        }

        if (IConfirmable::STATUS_TESTIQLEMEDE === (int)$newStatus && $this instanceof IHistory) {
            $history = new History($this);

            foreach ($confirmation->getCurrentApprovingTypes() as $currentApprovingType) {
                $history->create([
                    'operation' => 'create_approve_group_' . $currentApprovingType,
                    'operator_id' => (int)$_SESSION['erpuserid']
                ]);
            }
        }

        if ($this instanceof IDocument && IConfirmable::STATUS_TESTIQLENIB === $newStatus) {
            $documentNumberGeneral = new DocumentNumberGeneral($this);
            $documentNumberGeneral->onDocumentApprove();
        }

        require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
        notifyOnStatusChange($newStatus, $confirmation);
    }

    public function onApprove(array $approvingUser, $confirmationService)
    {
        if ($this instanceof IHistory) {
            $history = new History($this);
            $historyID = $history->create([
                'operation' => 'approve_' . $approvingUser['tip'],
                'note' => $approvingUser['note'],
                'poa'=>$approvingUser['power_of_attorney']
            ]);

            $form = [
                [
                    "Title" => "Sənəd",
                    "InputType" => "file",
                    "ColumnName" => "sened"
                ],
            ];

            $formObject = new Form($form);
            $formObject->check();
            $formObject->saveFiles($historyID, 'history', PRODOC_FILES_SAVE_PATH);

            $previousOrder = $approvingUser['order'];

            $confirmation  = new Confirmation($this);
            $currentOrder  = $confirmation->getCurrentOrder();

            if ($currentOrder !== NULL && $previousOrder !== $currentOrder) {
                foreach ($confirmation->getCurrentApprovingTypes() as $currentApprovingType) {
                    $history->create([
                        'operation' => 'create_approve_group_' . $currentApprovingType,
                        'operator_id' => (int)$_SESSION['erpuserid']
                    ]);
                }
            }
        }

        require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
        notifyOnApprove($approvingUser, $confirmationService);
    }

    public function onCancel(array $cancelingUser, $confirmationService)
    {
        if ($this instanceof IHistory) {
            $history = new History($this);
            $history->create([
                'operation' => 'cancel_' . $cancelingUser['tip'],
                'note' => $cancelingUser['note'],
                'poa' => $cancelingUser['powerOfAttorney_id']
            ]);
        }

        require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
        notifyOnCancel($cancelingUser, $confirmationService);
    }

    public function onFullApprove()
    {

    }

    public function getApproveOrder(array $user)
    {
        return 1;
    }

    public function getStatusColumnName()
    {
        return 'status';
    }
    public function getEditors(){
        $editors = [];
        $data = $this->getData();

        if (array_key_exists('created_by', $data)) {

                $editors[] = (int)$data['created_by'];
        }


        return $editors;
    }
    public function canEdit()
    {
        $data = $this->getData();

        if (array_key_exists('created_by', $data)) {
            return (int)$data['created_by'] === (int)$_SESSION['erpuserid'];
        }

        return false;
    }
}