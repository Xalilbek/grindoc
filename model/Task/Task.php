<?php
require_once DIRNAME_INDEX . 'prodoc/model/Appeal/IAppealAssociated.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/model/IConfirmable.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
require_once DIRNAME_INDEX . 'prodoc/model/Exception/NotAllowedOperationException.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
require_once DIRNAME_INDEX . 'prodoc/service/Log/ILog.php';
require_once DIRNAME_INDEX . 'prodoc/service/Log/Log.php';
require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . '/utils/utils.php';
require_once DIRNAME_INDEX . 'class/class.bildirish.php';


use History\IHistory;
use History\History;
use Log\ILog;
use Log\Log;
use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;
use Model\DocumentNumber\DocumentNumberGeneral\IDocument;
use Service\Confirmation\Confirmation;
use Util\ArrayUtils;
use PowerOfAttorney\IPowerOfAttorneyDocument;
use PowerOfAttorney\PowerOfAttorney;

class Task extends BaseEntity
    implements
    IAppealAssociated,
    IBaseEntity,
    IHistory,
    ILog,
    IPowerOfAttorneyDocument,
    IConfirmable
{
    const STATUS_QEBUL_OLUNMAYIB = 1;
    const STATUS_QEBUL_OLUNUB = 2;

    protected $history;
    protected $log;
    protected $powerOfAttorney;

    public function __construct($id = null, $params = null)
    {
        parent::__construct($id, $params);

        if (is_null($params) || (is_array($params) && !array_key_exists('customQuery', $params))) {
            $this->setCustomQuery('v_derkenar', true);
        }

        $this->powerOfAttorney = new PowerOfAttorney($this, $_SESSION['erpuserid'], new User());
        $this->history = new History($this);
        $this->log = new Log($this);
    }

    public function getStatusColumnName()
    {
        return 'approving_status';
    }

    public function onFullApprove()
    {
        $document = new Document($this->getData()['daxil_olan_sened_id']);
        if ($document->isInformative()) {
            $document->setStatus($document->getCorrectDocumentStatusForInformative());
        }

    }
    public function getTableName()
    {
        return 'tb_derkenar';
    }

    public function getColumnName()
    {
        return 'derkenar_id';
    }

    public function findAllSubTasks(Task $mainTask = null, $subTasks = []): array
    {
        if (is_null($mainTask)) {
            $mainTask = $this;
        }

        $subTask = DB::fetchOneColumnBy('tb_derkenar', 'id', [
            'parentTaskId' => $mainTask->getId()
        ]);

        if (false === $subTask) {
            return $subTasks;
        } else {
            $subTask = new self($subTask);
            $subTasks[] = $subTask;
        }

        return $this->findAllSubTasks($subTask, $subTasks);
    }

    public function delete()
    {
        $operator_id = isset($_SESSION['erpuserid']) ? $_SESSION['erpuserid'] : NULL;

        $document_number = DB::fetchColumn('SELECT document_number FROM v_daxil_olan_senedler WHERE id = ' . $this->getData()['daxil_olan_sened_id']);

        $log_text = $document_number . ' nömrəli sənədin ' . $this->id . ' dərkənarı ' . date('d M Y,  H:i') . ' tarixində silindi. ';

        $this->taskDeleteBackUp();

        DB::query("DELETE FROM tb_derkenar_elave_shexsler  WHERE derkenar_id = " . $this->id);
        DB::query("DELETE FROM tb_derkenar  WHERE id = " . $this->id);

        $this->log->create([
            'operation' => Log::OPERATION_DELETE,
            'operator_id' => $operator_id,
            'log_text' => $log_text
        ]);


        foreach ($this->getRelatedOutgoingDocuments() as $document) {

            $outGoingDocument = new OutgoingDocument($document['outgoing_document_id']);
            $outGoingDocument->deleteByAdmin();
        }

        foreach ($this->getRelatedInternalDocuments() as $document) {
            $incomingDocument = new Document($document['related_document_id']);
            $incomingDocument->deleteByAdmin();
        }
        $notifications = new bildirish_class();

        $notifications->deleteForTask($this->id);

        DB::query(" DELETE FROM tb_prodoc_testiqleyecek_shexs 
                            where related_record_id = ".$this->id." AND related_class='Task' ");


        return true;

    }

    public function taskDeleteBackUp(){
        $taskData = removeNumericKeys($this->getData());
        unset($taskData['id']);
        unset($taskData['mesul_shexs_ad']);
        unset($taskData['derkenar_metn_ad']);
        unset($taskData['rey_muellifi']);

        return DB::insertAndReturnId('tb_derkenar_deleted', $taskData);
    }

    function getRelatedOutgoingDocuments()
    {
        return  DB::fetchAll("SELECT outgoing_document_id FROM tb_prodoc_appeal_outgoing_document WHERE appeal_id
                                    IN( SELECT id FROM tb_prodoc_muraciet WHERE derkenar_id = " . $this->id . " )");
    }
    function getRelatedInternalDocuments()
    {
        return  DB::fetchAll("SELECT related_document_id FROM tb_prodoc_muraciet WHERE tip = 'ishe_tik'
                                    AND derkenar_id = " . $this->id );
    }
    // is not used
    public function canCreateAppeal()
    {
        return true;
    }

    public static function create(array $data, User $user = null)
    {
        $incomingDocument = new Document($data['daxil_olan_sened_id']);

        if(!$incomingDocument->derkenarYazaBiler()){
            throw new Exception(
                'Bu əməliyyatı yerinə yetirmək hüququnuz yoxdur.'
            );
        }

        $isSubTask = (int)$data['parentTaskId'] > 0;

        if (!array_key_exists('specifiesResult', $data)) {
            $data['specifiesResult'] = NULL;
        }

        if ($isSubTask) {
            $parentTask = new self($data['parentTaskId']);
            self::isTaskAlreadyCreated($parentTask->getId(),$data['poa_user_id']);
            if ($parentTask->isSubTask()) {
                throw new Exception(
                    'Məsul icraçı alt dərkənar yarada bilməz!'
                );
            }

            if ((int)$parentTask->info['mesul_shexs'] === (int)$data['mesul_shexs']) {
                throw new Exception(
                    'Yaratdığınız alt dərkənarın məsul şəxsi cari dərkənarın məsul şəxsi ilə eyni dir!'
                );
            }

            $data['daxil_olan_sened_id'] = $parentTask->info['daxil_olan_sened_id'];
        }
//task
        if ($incomingDocument->isInformative()) {
            if ($isSubTask) {
                throw new Exception('Sənədin tipi məlumat üçün olduğda, alt dərkənar yarada bilmərsiz');
            }

            $taskData = [
                'mesul_shexs' => $data['mesul_shexs'],
                'nezaretde_saxlanilsin' => $data['nezaretde_saxlanilsin'],
                'son_icra_tarixi' => $data['nezaretde_saxlanilsin'] ? $data['son_icra_tarixi'] : null,
                'derkenar_metn_id' => $data['derkenar_metn_id'],
                'daxil_olan_sened_id' => $data['daxil_olan_sened_id'],
                'status' => self::STATUS_QEBUL_OLUNUB,
                'daxili_nezaret' => $data['daxili_nezaret'],
                'group' =>$data['group'],
                'diger_derkenar_metn' => $data['derkenar_metn'],
            ];
        } else {
            $taskData = [
                'mesul_shexs' => $data['mesul_shexs'],
                'nezaretde_saxlanilsin' => $data['nezaretde_saxlanilsin'],
                'son_icra_tarixi' => $data['nezaretde_saxlanilsin'] ? $data['son_icra_tarixi'] : null,
                'derkenar_metn_id' => $data['derkenar_metn_id'],
                'daxil_olan_sened_id' => $data['daxil_olan_sened_id'],
                'parentTaskId' => $data['parentTaskId'],
                'specifiesResult' => $data['specifiesResult'],
                'daxili_nezaret' => $data['daxili_nezaret'],
                'group' =>  $data['group'],
                'status' => (getProjectName() === TS) ? self::STATUS_QEBUL_OLUNUB : self::STATUS_QEBUL_OLUNMAYIB,
                'diger_derkenar_metn' => $data['derkenar_metn']
            ];
        }

        $taskData['created_by'] = $_SESSION['erpuserid'];

        $self = self::createBaseForm($incomingDocument,$data,$taskData, $user);
        $curators = [];
        if (!$isSubTask) {
            if (array_key_exists('kuratorlar', $data)) {
                for ($i = 0, $len = count($data['kuratorlar']); $i < $len; ++$i) {
                    DB::insert('tb_derkenar_elave_shexsler', [
                        'user_id' => $data['kuratorlar'][$i],
                        'derkenar_id' => $self->getId(),
                        'tip' => 'kurator'
                    ]);

                    $curators[] = $data['kuratorlar'][$i];
                }
            }
        }

        $attendants = [];
        for ($i = 0, $len = count($data['ishtrakchi_shexsler']); $i < $len; ++$i) {
            if($data['ishtrakchi_shexsler'][$i]!="") {
                $explode = explodeGroupOrPerson($data['ishtrakchi_shexsler'][$i],0);
                DB::insert('tb_derkenar_elave_shexsler', [
                    'user_id' => $explode['shexs'],
                    'derkenar_id' => $self->getId(),
                    'tip' => 'ishtrakchi',
                    'group_id' => $explode['group']
                ]);

                $attendants[] = $data['ishtrakchi_shexsler'][$i];

            }
        }

        $melunatlancaq = [];

        if (isset($data['melumat'])){
            for ($i = 0, $len = count($data['melumat']); $i < $len; ++$i) {
                if($data['melumat'][$i]!="") {
                    $explode = explodeGroupOrPerson($data['melumat'][$i],0);
                    DB::insert('tb_derkenar_elave_shexsler', [
                        'user_id' => $explode['shexs'],
                        'derkenar_id' => $self->getId(),
                        'tip' => 'melumat',
                        'group_id' => $explode['group']
                    ]);
                }
            }
        }

        $confirmingUsers = [];
        if ($incomingDocument->isInformative()) {
            $taskRelatedUsers = array_unique(executorsOfTask($self->getId(),'melumat'));

            foreach ($taskRelatedUsers as $taskRelatedUser) {
                $confirmingUsers[] = [
                    'user_id' => $taskRelatedUser,
                    'type' => TestiqleyecekShexs::TIP_TANISH_OL,
                    'order' => 1
                ];
            }
        } else {
            $taskRelatedUsers = array_unique(executorsOfTask($self->getId(),'ishtrakchi'));
            for ($i = 0, $len = count($taskRelatedUsers); $i < $len; ++$i) {
                $confirmingUsers[] = [
                    'user_id' => $taskRelatedUsers[$i],
                    'type' => TestiqleyecekShexs::TIP_TANISH_OL
                ];
            }
        }

        $self->updateTaskSonIcraTarixi($data);

        $confirmation = new Service\Confirmation\Confirmation($self);
        $confirmation->addNewConfirmingUsers($confirmingUsers, false);

        updateOperationStatusOfRelatedDocuments($incomingDocument->getId());
        correctStatusAndResultOfRelateDocuments($incomingDocument->getId());

        DB::query("DELETE 
                            FROM
                                tb_prodoc_testiqleyecek_shexs 
                            WHERE
                                related_record_id = ".$data['daxil_olan_sened_id']." 
                                AND related_class = 'Document' 
                                AND user_id IN (
                            SELECT
                                mesul_shexs 
                            FROM
                                v_derkenar 
                            WHERE
                                daxil_olan_sened_id = ".$data['daxil_olan_sened_id'].")"); //.(isset($data['mesul_shexs'])?$data['mesul_shexs']:0));

        return $self;
    }

    public function edit($data)
    {
        if (!$this->canEdit()) {
            throw new NotAllowedOperationException();
        }

        $isSubTask = (int)$this->info['parentTaskId'] > 0;

        if ($isSubTask) {
            $parentTask = new self($data['parentTaskId']);

            if ((int)$parentTask->info['mesul_shexs'] === (int)$data['mesul_shexs']) {
                throw new Exception(
                    'Yaratdığınız alt dərkənarın məsul şəxsi cari dərkənarın məsul şəxsi ilə eyni dir!'
                );
            }

            $data['daxil_olan_sened_id'] = $parentTask->info['daxil_olan_sened_id'];
        }

        $key = 'derkenar_metn_id';
        $val = $data['derkenar_metn_id'];
        if ($data['derkenar_metn_id'] <= 0){
            $key = 'diger_derkenar_metn';
            $val = $data['derkenar_metn'];
        }

        DB::update('tb_derkenar', [
            'mesul_shexs' => $data['mesul_shexs'],
            'nezaretde_saxlanilsin' => $data['nezaretde_saxlanilsin'],
            'son_icra_tarixi' => $data['nezaretde_saxlanilsin'] ? $data['son_icra_tarixi'] : null,
            $key => $val,
            'status' => self::STATUS_QEBUL_OLUNMAYIB
        ], $this->getId());
        $this->reload();


        $sql = sprintf("DELETE FROM tb_derkenar_elave_shexsler WHERE derkenar_id = %s", $this->getId());
        DB::query($sql);

//        DB::query("DELETE FROM tb_prodoc_testiqleyecek_shexs
//           where related_record_id=".$data['daxil_olan_sened_id']." AND
//           related_class='Document' " );


        if (!$isSubTask) {
            for ($i = 0, $len = count($data['kuratorlar']); $i < $len; ++$i) {
                DB::insert('tb_derkenar_elave_shexsler', [
                    'user_id' => $data['kuratorlar'][$i],
                    'derkenar_id' => $this->getId(),
                    'tip' => 'kurator'
                ]);
            }
        }

        for ($i = 0, $len = count($data['ishtrakchi_shexsler']); $i < $len; ++$i) {
            if($data['ishtrakchi_shexsler'][$i]!="") {
                $explode = explodeGroupOrPerson($data['ishtrakchi_shexsler'][$i],0);
                DB::insert('tb_derkenar_elave_shexsler', [
                    'user_id' => $explode['shexs'],
                    'derkenar_id' => $this->getId(),
                    'tip' => 'ishtrakchi',
                    'group_id' => $explode['group']
                ]);
            }
        }
        $confirmingUsers = [];

        $ishtirakchilar = executorsOfTask($this->getId(),'ishtrakchi');

        for ($i = 0, $len = count($ishtirakchilar); $i < $len; ++$i) {
            $confirmingUsers[] = [
                'user_id' => $ishtirakchilar[$i],
                'type' => TestiqleyecekShexs::TIP_TANISH_OL
            ];
        }

        $this->updateTaskSonIcraTarixi($data);

        $incomingDocument = new Document($data['daxil_olan_sened_id']);
        $confirmation = new Service\Confirmation\Confirmation($incomingDocument);
        $confirmation->addNewConfirmingUsers($confirmingUsers, false);
        DB::query("DELETE FROM tb_prodoc_testiqleyecek_shexs where related_record_id=".$data['daxil_olan_sened_id']." AND related_class='Document' AND  user_id=".(isset($data['mesul_shexs'])?$data['mesul_shexs']:0));
        $this->history->create([
            'operation' => 'edit'
        ]);
    }

    public function createBaseForm($incomingDocument,$data,$taskData, User $user = null){
        $executors = [];
        if (array_key_exists('poa_user_id', $data) && (int)$data['poa_user_id']) {
            $executors[] = $data['poa_user_id'];
        } else {
            $executors = $incomingDocument->derkenarYazaBilenShexshler();
        }

        if (!is_null($user)) {
            $data['created_by'] = $user->getSessionUserId();
            $data['TenantId']   = $user->getActiveTenantId();
        }
        if (null !== static::getIsDeletedColumnName()) {
            $data[static::getIsDeletedColumnName()] = 0;
        }

        $id = DB::insertAndReturnId((new static())->getTableName(), $taskData);

        $self = new static($id);
        $self->setInfo($data);

        $poa = new PowerOfAttorney(
            $incomingDocument,
            $_SESSION['erpuserid'],
            new User()
        );

        if ($self instanceof IHistory) {
            $history = new History($self);
            $history->create([
                'operation' => 'registration',
                'poa'=>$poa->getPowerOfAttorneysByExecutors($executors),
                'note'=> $data['derkenar_metn']
            ]);
        }

        return $self;
    }

    public function getLoggedData(string $operation)
    {
        if ('registration' === $operation || 'edit' === $operation) {
            $this->setCustomQuery('v_derkenar', true);

            if (!array_key_exists('mesul_shexs_ad', $this->record)) {
                $this->reload();
            }

            return ArrayUtils::pick($this->record, [
                'mesul_shexs_ad'
            ]);
        }

        return null;
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    public static function isTaskAlreadyCreated($parentTaskId,$executor=0)
    {
        $subTask = DB::fetchOneBy('tb_derkenar', [
            'parentTaskId' => $parentTaskId
        ]);

        if (false !== $subTask) {
            throw new Exception(
                'Alt dərkənar daha öncə yazılıb!'
            );
        }
    }

    public function changeStatus($yeniStatus, $note = '')
    {
        $yeniStatus = (int)$yeniStatus;

        if (!in_array($yeniStatus, $this->statuslarinMassivi(), true)) {
            throw new Exception('Bu status movcud deyil!');
        }

        require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
        $this->powerOfAttorney = new PowerOfAttorney(
            new Document($this->data['daxil_olan_sened_id']),
            $_SESSION['erpuserid'],
            new User()
        );

        if (!$this->powerOfAttorney->canExecute((int)$this->info['mesul_shexs'])) {
            throw new Exception("Access error!");
        }

        // TODO: elave yoxlamalar

        DB::update('tb_derkenar', [
            'status' => $yeniStatus
        ], $this->id);

        $this->history->create([
            'operation' => 'status_change_to_' . $yeniStatus,
            'note'      => $note
        ]);
    }

    public function statuslarinMassivi()
    {
        return [
            self::STATUS_QEBUL_OLUNMAYIB,
            self::STATUS_QEBUL_OLUNUB,
            self::STATUS_IMTINA_OLUNUB
        ];
    }

    public function canEdit()
    {
        if ((int)$this->info['parentTaskId'] > 0) {
            $sql = "SELECT mesul_shexs FROM tb_derkenar WHERE id = {$this->info['parentTaskId']}";
            $editorUserId = (int)DB::fetchColumn($sql);
        } else {
            $editorUserId = (int)$this->info['rey_muellifi'];
        }

        return
            (int)$_SESSION['erpuserid'] === $editorUserId &&
            in_array($this->info['status'], [Task::STATUS_QEBUL_OLUNMAYIB, Task::STATUS_IMTINA_OLUNUB]);
    }

    public function isSubTask(): bool
    {
        return (int)$this->info['parentTaskId'] > 0;
    }

    public function getMainTask(Task $task): Task
    {
        if (!$task->isSubTask()) {
            return $task;
        }

        return $this->getMainTask(new self($task->info['parentTaskId']));
    }
    public function  getSubTasks(){
        $subTasks = DB::fetchColumnArray("SELECT id FROM v_derkenar WHERE parentTaskId = ".$this->id);
        return $subTasks;
    }

    /**
     * Aciq derkenar - qebul olunmayan derkenardi
     *
     * @param $daxilOlanSenedId
     * @return array
     */
    public static function getAciqDerkenarinMesulShexsleriniByDaxilOlanSenedId($daxilOlanSenedId)
    {
        return DB::fetchAllIndexed(
            sprintf(
                'SELECT id, mesul_shexs FROM tb_derkenar WHERE daxil_olan_sened_id = %s AND status = %s',
                (int)$daxilOlanSenedId,
                self::STATUS_QEBUL_OLUNMAYIB
            ),
            'mesul_shexs'
        );
    }

    public static function getRelatedTaskIds(ITaskAssociated $taskAssociated, $includeSubTasks = false)
    {
        $sql = sprintf("
            SELECT id
            FROM tb_derkenar
            WHERE
            %s = %s AND %s
        ",
            $taskAssociated->getColumnName(),
            $taskAssociated->getId(),
            $includeSubTasks ? '1 = 1' : ' ( parentTaskId IS NULL OR parentTaskId = 0 ) '
        );

        return DB::fetchColumnArray($sql);
    }

    public static function getAllTasks(ITaskAssociated $taskAssociated)
    {
        $sql = sprintf("
            SELECT *
            FROM v_derkenar
            WHERE
            %s = %s
        ", $taskAssociated->getColumnName(), $taskAssociated->getId());

        return DB::fetchAll($sql);
    }

    public static function getRelatedAcceptedTasksIndexedByExecutors(ITaskAssociated $taskAssociated)
    {
        $sql = sprintf("
            SELECT *
            FROM tb_derkenar
            WHERE
            %s = %s AND
            status = %s
        ", $taskAssociated->getColumnName(), $taskAssociated->getId(), self::STATUS_QEBUL_OLUNUB);
//        var_dump($sql);exit();

        return DB::fetchAllIndexed($sql, 'mesul_shexs');
    }

    public static function getRelatedMainAcceptedTasksIndexedByExecutors(ITaskAssociated $taskAssociated)
    {
        $sql = sprintf("
            SELECT *
            FROM tb_derkenar
            WHERE
            %s = %s AND
            status = %s AND
            (parentTaskId IS NULL OR parentTaskId = 0)
        ", $taskAssociated->getColumnName(), $taskAssociated->getId(), self::STATUS_QEBUL_OLUNUB);

        return DB::fetchAllIndexed($sql, 'mesul_shexs');
    }

    public function getHistoryKey(): string
    {
        return 'task';
    }

    public function getLogKey():string {
        return 'task';
    }

    private function updateTaskSonIcraTarixi($data)
    {
        $sonTarix = $data['son_icra_tarixi'];

        DB::update('tb_derkenar', [
            'son_icra_tarixi' => $sonTarix,
        ], $data['daxil_olan_sened_id'], 'daxil_olan_sened_id');

        DB::update('tb_daxil_olan_senedler', [
            'icra_edilme_tarixi' => $sonTarix,
        ], $data['daxil_olan_sened_id']);
    }

    public function hasRelatedAction()
    {
        $id = $this->getId();

        $sql = sprintf("SELECT * FROM (
                            SELECT TOP 1 id FROM tb_derkenar WHERE parentTaskId = $id
                            UNION 
                            SELECT TOP 1 id FROM tb_prodoc_muraciet WHERE derkenar_id = $id
                      ) as a");

        return DB::query($sql)->fetchColumn() !== FALSE;
    }

    public function canCancel()
    {
        if (getProjectName() === TS) {
            return false;
        }

        $poa = new PowerOfAttorney\PowerOfAttorney(
            new Document($this->data['daxil_olan_sened_id']),
            $_SESSION['erpuserid'],
            new User()
        );

        return (
                ((int)$this->data['status'] === self::STATUS_QEBUL_OLUNMAYIB || (int)$this->data['status'] === self::STATUS_QEBUL_OLUNUB)
                && $poa->canExecute($this->data['mesul_shexs'])
            ) &&
            !$this->hasRelatedAction();
    }

    private $lastmayBeFamiliarApprovingUser;

    /**
     * @return mixed
     */
    public function getLastmayBeFamiliarApprovingUser()
    {
        return $this->lastmayBeFamiliarApprovingUser;
    }

    public function mayBeFamiliar()
    {
        $approvers = $this->mayBeFamiliarExecutors();
        $userID = $this->powerOfAttorney->canExecute(array_keys($approvers), true);

        if (false === $userID) {
            return false;
        }

        $this->lastmayBeFamiliarApprovingUser = $approvers[$userID];

        return true;
    }

    public function mayBeFamiliarExecutors()
    {

        $users = [];
        $confirmation = new Service\Confirmation\Confirmation($this);

        foreach ($confirmation->getApprovingUsers(TestiqleyecekShexs::STATUS_TESTIQLEMEYIB) as $approvingUser){
            $users[$approvingUser['user_id']] = $approvingUser;
        }

        return  $users;
    }
    public function getDate(): DateTime
    {
        return new DateTime($this->getData()['elave_olunma_tarixi']);
    }
    public function getStatus()
    {
        $incomingDocument = new Document($this->getData()['daxil_olan_sened_id']);
        return (int)$incomingDocument->getData()['status'];
    }

}