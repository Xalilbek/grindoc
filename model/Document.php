<?php
require_once DIRNAME_INDEX . 'prodoc/model/Task/ITaskAssociated.php';
require_once DIRNAME_INDEX . 'prodoc/model/Appeal/IAppealAssociated.php';
require_once DIRNAME_INDEX . 'prodoc/model/IConfirmable.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/Exception/NotAllowedOperationException.php';
require_once DIRNAME_INDEX . 'prodoc/model/Exception/RepeatOrSameDocumentException.php';
require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/IIncomingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
require_once DIRNAME_INDEX . 'prodoc/service/Log/ILog.php';
require_once DIRNAME_INDEX . 'prodoc/service/Log/Log.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
require_once DIRNAME_INDEX . 'class/class.bildirish.php';
require_once DIRNAME_INDEX . 'prodoc/includes/user_info.php';


use Model\DocumentNumber\DocumentNumberGeneral\IIncomingDocument;
use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;
use History\IHistory;
use History\History;
use Log\ILog;
use Log\Log;
use PowerOfAttorney\IPowerOfAttorneyDocument;
use PowerOfAttorney\PowerOfAttorney;

class Document extends BaseEntity
    implements
    ITaskAssociated,
    IAppealAssociated,
    IConfirmable,
    IBaseEntity,
    IIncomingDocument,
    IHistory,
    IPowerOfAttorneyDocument,
    ILog
{
    const TIP_HUQUQI = 1;
    const TIP_FIZIKI = 2;
    const TIP_DAXILI = 3;

    const STATUS_ACIQ = 1;
    const STATUS_BAGLI = 2;

    const STATE_NONE = 0;
    const STATE_IN_INSPECTION = 1;
    const STATE_NUMBER_REQUIRED = 6;
    const STATE_INSPECTED = 2;
    const STATE_AUTHOR_ACCEPTED = 3;
    const STATE_CANCELED = 4;
    const STATE_IN_TRASH = 5;

    const SENED_TIP_MELUMAT_UCHUN = 1;
    const SENED_TIP_ICRA_UCHUN = 2;

    protected $history;
    protected $log;
    protected $powerOfAttorney;
    private $cache = [];

    public function __construct($id = null, $params = null)
    {
        parent::__construct($id, $params);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->history = new History($this);
        $this->log = new Log($this);

        $this->powerOfAttorney = new PowerOfAttorney($this, $_SESSION['erpuserid'], new User());
        $this->currentUserId = $_SESSION['erpuserid'];
    }

    public function setPowerOfAttorney(PowerOfAttorney $powerOfAttorney)
    {
        // TODO: should be removed
    }

    public function getStatus()
    {
        return (int)$this->data['status'];
    }

    public function getDate(): DateTime
    {
        return new DateTime($this->data['created_at']);
    }

    public function getDocumentNumberColumnName()
    {
        return 'document_number';
    }

    public function getDocumentNumberId()
    {
        return (int)$this->record['document_number_id'];
    }

    public function getDirection()
    {
        return 'incoming';
    }

    public function getDocumentType()
    {
        return (int)$this->record['mektubun_tipi'];
    }

    public function getSenderPerson()
    {
        if (self::TIP_FIZIKI === (int)$this->record['tip']) {
            return (int)$this->record['muraciet_eden'];
        } else {
            return null;
        }
    }

    public function getTypeTitle()
    {
        $typeTitle ='';
        $type = $this->info['tip'];
        if ($type == 1){
            $typeTitle = 'Hüquqi';
        }else if($type == 2){
            $typeTitle = 'Vətəndaş müraciəti';
        }else if($type == 3){
            $typeTitle = 'Daxili sənəd';
        }
        return $typeTitle;
    }

    public function getSenderPersonFirstSurnameLetter($usedFor = null)
    {
        if ($usedFor === 'serial_number' && getProjectName() !== TS) {
            // no need for serial number for each person's surname
            return null;
        }

        if (self::TIP_FIZIKI === (int)$this->record['tip']) {
            if (array_key_exists('senderPersonFirstSurnameLetter', $this->cache)) {
                return $this->cache['senderPersonFirstSurnameLetter'];
            }

            $sql = sprintf("select Soyadi from tb_Customers where id = %s",
                (int)$this->record['muraciet_eden']
            );

            $soyadi = DB::fetchColumn($sql);

            if (false !== $soyadi) {
                $this->cache['senderPersonFirstSurnameLetter'] = mb_substr($soyadi, 0, 1);
            } else {
                $this->cache['senderPersonFirstSurnameLetter'] = null;
            }

            return $this->cache['senderPersonFirstSurnameLetter'];
        } else {
            return null;
        }
    }

    public function getOption()
    {
        static $optionList = null;

        if (is_null($optionList)) {
            $optionList = DB::fetchAllIndexed('SELECT id, name, extra_id FROM tb_prodoc_document_number_pattern_option_list',
                'extra_id');
        }

        if (self::TIP_FIZIKI === (int)$this->record['tip']) {
            return $optionList['vetendash']['id'];
        } else {
            return $optionList['adiyati_qurum']['id'];
        }
    }

    public function getOptionValue()
    {
        if (self::TIP_FIZIKI === (int)$this->record['tip']) {
            return (int)$this->record['muraciet_eden_tip_id'];
        } else {
            return (int)$this->record['gonderen_teshkilat'];
        }
    }

    public function getParticipants($grouppedBy = false, $params = []){

        $filter = '1=1';
        $unionQuery = '';

        if (array_key_exists('derkenarId', $params) && $params['derkenarId']) {
            $task = new Task($params['derkenarId']);
            $relatedTasks = $params['derkenarId'];
            $filter = '';

            if ($task->info['parentTaskId'] > 0){
                $relatedTasks =  $task->info['parentTaskId'];
            }

            if ($relatedTasks > 0){
                $filter = " derkenar.id =".$relatedTasks;
            }
        } else {
            $relatedTasks = Task::getAllTasks($this);
            $filter = "derkenar.daxil_olan_sened_id = ".$this->getId();

            $unionQuery = "SELECT
                                mesul_shexs AS USERID,
                                ( CASE WHEN parentTaskId > 0 THEN 'sub_task_executor' ELSE 'mesul_shexs' END ) AS tip,
                                NULL AS status 
                            FROM
                                v_derkenar
                            WHERE
                                daxil_olan_sened_id = " . $this->getId() . "  UNION";
        }

        $icra_shexsin_deyishdirilmesi="";
        if($this->haveDocumentOperation()=='icra_sexsin_deyisdirilmesi'){
            $icra_shexsin_deyishdirilmesi = "
                 UNION
                    SELECT
                         yeni_icra_eden_sexs as USERID,
                         'yeni_icra_eden_sexs' as tip,
                         null as status
                    FROM
                        tb_icra_sexsin_deyisdirilmesi 
                    WHERE document_id = ".$this->getId()."  
                        UNION
                    SELECT
                         hemIcraciShexs as USERID,
                         'yeni_hemIcrachi' as tip,
                         null as status
                    FROM
                    tb_icra_sexsin_deyisdirilmesi_hem_icraci_sexsler hemIcrachi
                    LEFT JOIN  tb_icra_sexsin_deyisdirilmesi icrachi ON icrachi.id = hemIcrachi.parent_id
                    WHERE icrachi.document_id = ".$this->getId()."  
            ";
        }

        if(is_array($relatedTasks) && false === (count($relatedTasks)>0)) {

            $sql = "
            SELECT
                dd.USERID,
                tb2.user_ad_qisa,
                dd.tip,
                dd.status
                
            FROM
                (
                SELECT DISTINCT
                    ( CASE WHEN tb1.user_id > 0 THEN tb1.user_id ELSE tb4.user_id END ) AS USERID,
                    
                        tb1.tip,
                         null as status 
                    FROM
                        tb_daxil_olan_senedler_elave_shexsler AS tb1
                        LEFT JOIN tb_prodoc_group tb3 ON tb3.id = tb1.group_id
                        LEFT JOIN tb_prodoc_group_user tb4 ON tb4.group_id = tb1.group_id 
                    WHERE
                        daxil_olan_sened_id = ".$this->getId()."  
                        UNION	
                    SELECT
                        tb2.user_id AS USERID,
                        tb2.emeliyyat_tip AS tip,
                        tb2.status  
                    FROM
                        tb_prodoc_formlar_tesdiqleme AS tb2 
                    WHERE
                        daxil_olan_sened_id = ".$this->getId()."   
                   ".$icra_shexsin_deyishdirilmesi." AND emeliyyat_tip != 'kurator' AND emeliyyat_tip != 'ishtrakchi'
                    ) dd 			
                    LEFT JOIN v_user_adlar tb2 ON tb2.USERID = dd.USERID
            
                WHERE dd.USERID IS NOT NULL AND dd.USERID > 0 
                   ";
        }else {
            $sql = "
            SELECT
                dd.USERID ,
                tb2.user_ad_qisa,
                dd.tip,
                dd.status 
            FROM
                (
                SELECT DISTINCT
                    ( CASE WHEN tb1.user_id > 0 THEN tb1.user_id ELSE tb4.user_id END ) AS USERID,
                    tb1.tip,
                    tesdiqleme.status 
                FROM
                    tb_derkenar_elave_shexsler AS tb1
                    LEFT JOIN tb_prodoc_group tb3 ON tb3.id = tb1.group_id
                    LEFT JOIN tb_prodoc_group_user tb4 ON tb4.group_id = tb1.group_id
                    LEFT JOIN tb_derkenar derkenar ON derkenar.id = tb1.derkenar_id
                    LEFT JOIN tb_prodoc_testiqleyecek_shexs tesdiqleme ON tesdiqleme.user_id = ( CASE WHEN tb1.user_id > 0 THEN tb1.user_id ELSE tb4.user_id END ) 
                        AND tesdiqleme.tip = 'tanish_ol' 
                        AND tesdiqleme.related_class = 'Task' 
                        AND tesdiqleme.related_record_id = tb1.derkenar_id 
                    WHERE
                        $filter
                        UNION
                    SELECT
                        user_id AS USERID,
                         tip,
                        NULL AS status 
                    FROM
                        tb_daxil_olan_senedler_elave_shexsler 
                    WHERE
                        daxil_olan_sened_id = ".$this->getId() ." AND tip = 'mesul_shexs'
                    UNION
                    $unionQuery
                    SELECT
                        tb2.user_id AS USERID,
                        tb2.emeliyyat_tip AS tip,
                        tb2.status 
                    FROM
                        tb_prodoc_formlar_tesdiqleme AS tb2 
                    WHERE
                        daxil_olan_sened_id = " . $this->getId() . " 
                        " . $icra_shexsin_deyishdirilmesi . " AND emeliyyat_tip != 'kurator' AND emeliyyat_tip != 'ishtrakchi'
                    ) dd
                    LEFT JOIN v_user_adlar tb2 ON tb2.USERID = dd.USERID 
                WHERE
                dd.USERID IS NOT NULL 
                AND dd.USERID > 0";

        }

        if($grouppedBy){
            return DB::fetchAllGroupped($sql,$grouppedBy);
        }else{
            return DB::fetchAll($sql);
        }
    }

    public function getColumnName()
    {
        return 'daxil_olan_sened_id';
    }

    public function getTableName()
    {
        return 'tb_daxil_olan_senedler';
    }

    public function getStatusColumnName()
    {
        return null;
    }

    public function getListOfStates(): array
    {
        return [
            self::STATE_NONE,
            self::STATE_IN_INSPECTION,
            self::STATE_INSPECTED,
            self::STATE_AUTHOR_ACCEPTED,
            self::STATE_CANCELED,
            self::STATE_NUMBER_REQUIRED,
        ];
    }

    public function getUsersWhoCanChangeState($newState)
    {
        $users = [];
        switch ($newState) {
            case Document::STATE_AUTHOR_ACCEPTED:
                if ((int)$this->data['state'] === Document::STATE_INSPECTED) {
                    $users[] = (int)$this->data['rey_muellifi'];
                } elseif ((int)$this->data['state'] === Document::STATE_IN_INSPECTION) {

                    $users[] = (int)$this->data['yoxlayan_shexs'];

                }
                break;
            case  Document::STATE_CANCELED:
                if ((int)$this->data['state'] === Document::STATE_IN_INSPECTION) {
                    $users[] = (int)$this->data['yoxlayan_shexs'];


                } elseif (((int)$this->data['state'] === Document::STATE_INSPECTED || (int)$this->data['state'] === self::STATE_AUTHOR_ACCEPTED) && !$this->hasRelatedAction()) {
                    $users[] = (int)$this->data['rey_muellifi'];
                }
                break;
            case Document::STATE_NUMBER_REQUIRED:
                if ((int)$this->data['state'] === Document::STATE_IN_INSPECTION){
                    $users[] = (int)$this->data['yoxlayan_shexs'];
                }
                break;
            case Document::STATE_INSPECTED:
                if ((int)$this->data['state'] === Document::STATE_NUMBER_REQUIRED){
                    $users[] = (int)$this->data['yoxlayan_shexs'];
                }
                break;
        }

        return $users;
    }

    public function canChangeState($newState)
    {


        $users = $this->getUsersWhoCanChangeState($newState);
        return $this->powerOfAttorney->canExecute($users);
    }

    public function setState($newState, $params = []): void
    {
        if (!in_array($newState, self::getListOfStates())) {
            throw new Exception("State $newState is not defined");
        }


        if(getProjectName() === TS && !$this->canChangeState($newState)){
            throw new Exception('You have no access to do the operation');
        }


        $users = $this->getUsersWhoCanChangeState($newState);

        $poa = $this->powerOfAttorney->getPowerOfAttorneysByExecutors($users);


        DB::query("UPDATE tb_daxil_olan_senedler SET state_before_canceled = state   WHERE id=" . $this->id . " AND state is not null");


        DB::update('tb_daxil_olan_senedler', [
            'state' => $newState
        ], $this->id);

        $historyParams = \Util\ArrayUtils::pick($params, [
            'note'
        ]);
        $historyParams['operation'] = 'state_changed_to_' . $newState;
        $historyParams['poa'] = $poa;


        if (self::STATE_CANCELED === (int)$newState && isset($params['changedByType'])) {
            $historyParams['operation'] .= '_by_' . $params['changedByType'];
            $this->history->create($historyParams);
        } else if (self::STATE_IN_INSPECTION === (int)$newState) {
            $historyParams['operation'] = 'yoxlamaya_gonderildi';
            $this->history->create($historyParams);
        } else if (self::STATE_INSPECTED === (int)$newState || (getProjectName() === TS && self::STATE_AUTHOR_ACCEPTED === (int)$newState)) {
            $historyParams['operation'] = 'yoxlayan_testiq';
            $this->history->create($historyParams);

            $historyParams['operation'] = 'rey_muelife_gonderildi';
            $this->history->create($historyParams);
        }
        else if (self::STATE_AUTHOR_ACCEPTED === (int)$newState) {
            $historyParams['operation'] = 'rey_muelifi_testiq';
            $this->history->create($historyParams);
        }
        else if (self::STATE_NUMBER_REQUIRED === (int)$newState) {
            $historyParams['operation'] = 'state_changed_to_6';
            $this->history->create($historyParams);
        }
    }

    public function getUsersWhoCanCancel()
    {
        if (false === (
                (int)$this->data['state'] === self::STATE_INSPECTED ||
                (int)$this->data['state'] === self::STATE_AUTHOR_ACCEPTED)
        ) {
            return [];
        }
        if ($this->hasRelatedAction()) {
            return [];
        }

        return [(int)$this->data['rey_muellifi']];
    }

    public function canCancel()
    {
        return $this->powerOfAttorney->canExecute($this->getUsersWhoCanCancel());
    }

    public function hasRelatedAction()
    {
        $id = $this->getId();

        $sql = sprintf("SELECT * FROM (
                            SELECT TOP 1 id FROM tb_derkenar WHERE daxil_olan_sened_id = $id
                            UNION 
                            SELECT TOP 1 id FROM tb_prodoc_muraciet WHERE daxil_olan_sened_id = $id
                      ) as a");

        return DB::query($sql)->fetchColumn() !== false;
    }

    public function setResult($result)
    {
        DB::update('tb_daxil_olan_senedler', [
            'netice' => $result
        ], $this->getId());
    }

    public function setStatus($newStatus)
    {
//        get document prev status
        $prevStatus = DB::fetchOneColumnBy('tb_daxil_olan_senedler','status',
            ["id"=>$this->id] );
        $prevStatus = (int)$prevStatus;
        $newStatus = (int)$newStatus;
        $this->setRecordColumnValue('status', $newStatus);

        if (!in_array($newStatus, [self::STATUS_ACIQ, self::STATUS_BAGLI])) {
            throw new Exception("Status $newStatus is not defined");
        }

        DB::update('tb_daxil_olan_senedler', [
            'status' => $newStatus
        ], $this->id);


        if ($prevStatus === self::STATUS_ACIQ && $newStatus === self::STATUS_BAGLI) {
            $this->requireResult();

            DB::update('tb_daxil_olan_senedler', [
                'baglanma_tarixi' => 'getdate()'
            ], $this->id, 'id', ['baglanma_tarixi']);
        }
    }

    private function requireResult()
    {
        // anamada T prosesi ile gedən daxili sənədlər statusu bağlandıqdan sonra ümumi şöbəyə netice getmeli deil
        if (getProjectName() === ANAMA && $this->haveDocumentOperation() !== "umumi_forma") {
            return;
        }

        $confirmationService = new Service\Confirmation\Confirmation($this);
        $gdUsers = getUsersAllowedToChangeResult($confirmationService);


        // umumi shobede kimse var
        if (isset($gdUsers[0])) {
            $type = 'umumi_shobe_netice';
            $userId = null;
        } else {
            if ((int)$this->data['netice'] > 0) {
                return;
            }

            $type = 'qeydiyyatchi_netice';
            $userId = $this->data['created_by'];
        }

        $confirmationService->createConfirmingUsers([
            [
                'type' => $type,
                'user_id' => $userId,
            ]
        ]);
    }

    public function getAllRelatedConfirmingUsers()
    {

    }

    public static function getStatusTitle($status)
    {
        switch ($status) {
            case null:
                return 'Yoxdur';
            case self::STATUS_ACIQ:
                return 'Açıq';
            case self::STATUS_BAGLI:
                return 'Bağlı';
            case self::STATE_IN_TRASH:
                return 'Ləğv olunub';
        }
    }

    public function canEdit()
    {

        return $this->powerOfAttorney->canExecute($this->getEditors());
    }

    public function legvEdeBiler()
    {
        return $this->powerOfAttorney->canExecute($this->legvEdenler());;
    }

    public function canDelete()
    {
        return (is_null($this->data['document_number_id']) || $this->data['document_number'] == "-") && $this->powerOfAttorney->canExecute((int)$this->data['created_by']);

    }

    public function senedNezaretEdeBiler()
    {
        return (int)$_SESSION['erpuserid'] === (int)$this->data['rey_muellifi'];
    }

    public function isBindToOutgoingDocument()
    {
        return (int)$this->info['outgoing_document_id'] > 0;
    }

    public function onNumberAcquire()
    {
        if (!$this->relatedToOutgoingDocument()) {
            return;
        }

        $oDoc = new OutgoingDocument($this->getRelatedOutgoingDocument());
        $oDoc->onAnswerReceive($this);
    }

    public static function create(array $data, User $user = null, $additionalData = null): Document
    {
        $ews = $data['editable_with_select'];
        $createDocNumber = isset($data['create_document_number']) ? $data['create_document_number'] : true;
        $data = \Util\ArrayUtils::omit($data, ['editable_with_select', 'create_document_number']);
        $data = \Util\ArrayUtils::defaults($data, [
            'sened_tip' => self::SENED_TIP_ICRA_UCHUN
        ]);

        $senedEynidir = self::TIP_FIZIKI === $data['tip'] && (int)$additionalData['tekrar_eyni'] === 2;

        if ($senedEynidir) {
            $repeatDocument = self::checkAndReturnRepeatDocument($additionalData['tekrar_eyni_sened_id'], $user);
            $data['rey_muellifi'] = $repeatDocument->data['rey_muellifi'];
        }

        $data['state'] = self::getState($data, $additionalData);
        $data['status'] = self::getDocumentStatus($data, $additionalData);

        if ($data['state'] === self::STATE_NONE) {
            $data['sened_tip'] = self::SENED_TIP_MELUMAT_UCHUN;
        }
        $related_incoming_document_id=0;
        if(isset($data['incoming_document_id'])){
            $related_incoming_document_id = $data['incoming_document_id'];
            unset($data['incoming_document_id']);
        }



        $self = parent::create($data, $user);

        if ($senedEynidir) {
            $repeatDocumentId = $repeatDocument->getId();
            $newId = $self->getId();
            $sql = "
                INSERT INTO tb_daxil_olan_senedler_elave_shexsler (tip, daxil_olan_sened_id, user_id, group_id)
                SELECT tip, $newId, user_id, group_id FROM tb_daxil_olan_senedler_elave_shexsler
                WHERE daxil_olan_sened_id = $repeatDocumentId
            ";
            DB::query($sql);
        }

        if($related_incoming_document_id) {

            DB::insert('tb_internal_document_relation', [
                'internal_document_id' => $self->getId(),
                'related_document_id' => $related_incoming_document_id,
                'related_document_type' => 'incoming',
                'type' => 1
            ]);



        }

        if (self::TIP_FIZIKI === $data['tip']) {
            $additionalData['daxil_olan_sened_id'] = $self->getid();

            DB::insert('tb_daxil_olan_senedler_fiziki', $additionalData);
        }

        $self->setCustomQuery('v_incoming_document_all', true);

        if (self::STATE_IN_INSPECTION === (int)$data['state']) {
            $self->history->create(['operation' => 'yoxlamaya_gonderildi']);
        } else if (self::STATE_INSPECTED === (int)$data['state'] || self::STATE_AUTHOR_ACCEPTED === (int)$data['state']) {
            $self->history->create(['operation' => 'rey_muelife_gonderildi']);
        }

        $internal_document_type = isset($data['internal_document_type_id']) ? DB::fetchColumn("SELECT extra_id FROM tb_prodoc_inner_document_type WHERE id = " . $data['internal_document_type_id']) : false;


        if ((int)$data['tip'] === self::TIP_DAXILI && (getProjectName() === TS && $internal_document_type != "power_of_attorney")) {
            return $self;
        }

        if ($data['state'] !== self::STATE_IN_INSPECTION && true === $createDocNumber) {
            require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
            $documentNumberGeneral = new DocumentNumberGeneral($self, [
                'manualDocumentNumber' => $data['document_number'],
                'editable_with_select' => $ews,
            ]);
            $documentNumberGeneral->assignNumber();
        }

        return $self;
    }

    public function relatedToOutgoingDocument()
    {
        return isset($this->data['outgoing_document_id']) && (int)$this->data['outgoing_document_id'];
    }

    public function getRelatedOutgoingDocument()
    {
        return (int)$this->data['outgoing_document_id'];
    }

    public function fullUpdateAllowed(): bool
    {
        return $this->hasRelatedAction() === false;
    }

    private static function checkAndReturnRepeatDocument($repeatDocumentId, $user)
    {
        $repeatDocument = new self($repeatDocumentId);
        $repeatDocument->setCustomQuery('v_daxil_olan_senedler_corrected', true);

        if (self::STATUS_BAGLI === (int)$repeatDocument->data['status']) {
            throw new RepeatOrSameDocumentException(sprintf('
                    Seçdiyiniz %s nömrəli sənəd bağlı olduğu üçün eyni sənəd əlavə edə bilmərsiniz
                ', $repeatDocument->data['document_number']));
        }

        require_once DIRNAME_INDEX . 'prodoc/model/LastExecutionDate/LastExecution.php';
        $nezaret_muddeti = DB::fetchOneColumnBy('tb_options', 'value', [
            'option_name' => 'nezaret_muddeti'
        ]);

        $lastExecution = new Model\LastExecution\LastExecution($user, $nezaret_muddeti === 'istehsalat_teqvimi');
        $led = new DateTime(date('d-m-Y H:i', strtotime($repeatDocument->data['icra_edilme_tarixi'])));
        $res = $lastExecution->getRemainingDaysByLastExecutionDate($led, false);

        if ($res <= 0) {
            throw new RepeatOrSameDocumentException(sprintf('
                    Seçdiyiniz %s nömrəli sənədin icra tarixi keçdiyi üçün eyni sənəd əlavə edə bilmərsiniz
                ', $repeatDocument->data['document_number']));
        }

        return $repeatDocument;
    }

    protected static function getState($data, $additionalData = null)
    {
        // eynidirse
        if (self::senedEynidir($data, $additionalData)) {
            $state = self::STATE_AUTHOR_ACCEPTED;
        } else {
            if ((int)$data['yoxlayan_shexs']) {
                $state = self::STATE_IN_INSPECTION;
            } else {
                if ((int)$data['rey_muellifi']) {
                    $state = (getProjectName() === TS) ? self::STATE_AUTHOR_ACCEPTED : self::STATE_INSPECTED;
                } else {
                    $state = self::STATE_NONE;
                }
            }
        }

        return $state;
    }

    protected static function getDocumentStatus($data, $additionalData = null)
    {
        if (array_key_exists('status', $data)) {
            $status = $data['status'];
        } else {
            if (self::senedEynidir($data, $additionalData)) {
                $status = self::STATUS_ACIQ;
            } else {
                if ((int)$data['yoxlayan_shexs']) {
                    $status = self::STATUS_ACIQ;
                } else {
                    if ((int)$data['rey_muellifi']) {
                        $status = self::STATUS_ACIQ;
                    } else {
                        $status = null;
                    }
                }
            }
        }

        return $status;
    }

    private static function senedEynidir($data, $additionalData)
    {
        return self::TIP_FIZIKI === $data['tip'] && isset($additionalData['tekrar_eyni']) && (int)$additionalData['tekrar_eyni'] === 2;
    }

    public function edit($data, $additionalData = null)
    {
        if (!$this->canEdit()) {
            throw new NotAllowedOperationException();
        }

        $originalData = $data;
        $data = \Util\ArrayUtils::omit($data, ['document_number', 'editable_with_select']);

        $data['tip'] = $this->data['tip'];

        if ($this->fullUpdateAllowed()) {
            if (self::senedEynidir($data, $additionalData) && $this->data['tekrar_eyni_sened_id'] !== $additionalData['tekrar_eyni_sened_id']) {
                $repeatDocument = self::checkAndReturnRepeatDocument($additionalData['tekrar_eyni_sened_id'],
                    new User());
                $data['rey_muellifi'] = $repeatDocument->data['rey_muellifi'];
            }

            $data['state'] = self::getState($data, $additionalData);
            $data['status'] = self::getDocumentStatus($data, $additionalData);
        } else {
            $data = \Util\ArrayUtils::pick($data, [
                'senedin_daxil_olma_tarixi',
                'senedin_tarixi',
                'gonderen_teshkilat',
                'gonderen_shexs',
                'gonderen_teshkilatin_nomresi',
                'mektubun_tipi',
                'mektubun_alt_tipi',
                'mektub_nezaretdedir',
                'vereq_sayi',
                'mektubun_qisa_mezmunu',
                'icra_edilme_tarixi',
                'yoxlayan_shexs',
                'document_number',
                'qeyd',
                'tibb_muessisesi',
                'nazalogiya',
                'mektubun_tipi_third',
                'mektubun_mezmunu'
            ]);

            // not required
            // ALWAYS equal to 3
            $data['state'] = self::STATE_AUTHOR_ACCEPTED;
        }

        if ($this->relatedToOutgoingDocument()) {
            $data = \Util\ArrayUtils::omit($data, [
                'outgoing_document_id'
            ]);
        }

        DB::update('tb_daxil_olan_senedler', $data, $this->getId());

        if (self::TIP_FIZIKI === (int)$this->getInfo()['tip']) {
            $additionalData['daxil_olan_sened_id'] = $this->getId();

            $individualDoc = DB::fetchOneBy('tb_daxil_olan_senedler_fiziki', [
                'daxil_olan_sened_id' => $this->getId()
            ]);

            if (false !== $individualDoc) {
                $additionalData['tekrar_eyni'] = $additionalData['tekrar_eyni_checked'];
                $additionalData = \Util\ArrayUtils::omit($additionalData, ['tekrar_eyni_checked']);
                DB::update('tb_daxil_olan_senedler_fiziki', $additionalData, $individualDoc['id']);
            }
        }

        $yoxlayanShexsVar = (int)$data['yoxlayan_shexs'];

        if (!$yoxlayanShexsVar && (int)$this->getData()['document_number_id'] === 0) {
            require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
            $documentNumberGeneral = new DocumentNumberGeneral($this, [
                'manualDocumentNumber' => $originalData['document_number'],
                'editable_with_select' => $originalData['editable_with_select'],
            ]);
            $documentNumberGeneral->assignNumber();
        }

//        check if there is "yoxlayan shex" to avoid sending notification to "rey muellifi"
        if(!$yoxlayanShexsVar){
            $this->history->create([
                'operation' => 'rey_muelife_gonderildi',
                'poa' => $this->powerOfAttorney->getPowerOfAttorneysByExecutors($this->getEditors())
            ]);
        }

        if ($this->fullUpdateAllowed()) {
            if (self::STATE_IN_INSPECTION === (int)$data['state']) {
                $this->history->create(['operation' => 'yoxlamaya_gonderildi']);
            } else {
                if (self::STATE_INSPECTED === (int)$data['state']) {
                    $this->history->create(['operation' => 'rey_muelife_gonderildi']);
                }
            }
        }
    }

    public function getEditors()
    {
        $editors = [];

        if (false === ((int)$this->data['status'] === self::STATUS_ACIQ || is_null($this->data['status']))) {
            return $editors;
        }

        if (false === (getProjectName() !== TS || $this->hasRelatedAction() === false)) {
            return $editors;
        }


        if((int)$this->data['state'] !== self::STATE_IN_TRASH){
            $editors[] = (int)$this->data['created_by'];
        }


        if ((int)$this->data['state'] === self::STATE_IN_INSPECTION) {
            $editors[] = (int)$this->data['yoxlayan_shexs'];
        }

        return $editors;
    }

    public function legvEdenler()
    {
        $executors = [];
        if ((int)$this->data['state'] === self::STATE_CANCELED && (!is_null($this->data['document_number_id']) || !$this->data['document_number'] == "-")) {
            $executors[] = (int)$this->data['created_by'];
        }


        return $executors;
    }

    public function legvEt($sebeb)
    {


        if (!$this->legvEdeBiler()) {
            throw new NotAllowedOperationException();
        }


        $executors = $this->legvEdenler();

        DB::update('tb_daxil_olan_senedler', [
            'state' => self::STATE_IN_TRASH
        ], $this->data['id'], 'id');

        $this->history->create([
            'operation' => 'legv_et',
            'note' => $sebeb,
            'poa' => $this->powerOfAttorney->getPowerOfAttorneysByExecutors($executors)
        ]);
    }

    public function delete()
    {
        if (!$this->canDelete()) {
            throw new NotAllowedOperationException();
        }

        DB::update('tb_daxil_olan_senedler', [
            'is_deleted' => 1
        ], $this->data['id'], 'id');


    }
    public function deleteByAdmin()
    {
        $document_number = DB::fetchColumn('SELECT document_number FROM v_daxil_olan_senedler WHERE id = '.$this->id);

        $log_text =  $document_number.' nömrəli sənəd  '.date('d M Y,  H:i').' tarixində silindi. ';

        $this->documentDeleteBackUp();


        $notifications = new bildirish_class();

        $notifications->deleteForDocument($this->id);

        DB::query("DELETE FROM tb_prodoc_testiqleyecek_shexs  WHERE (related_class = 'Document' OR related_class = 'IncomingDocument' ) AND related_record_id = " . $this->id);
        if(array_key_exists( 'document_number_id', $this->getInfo()) && !is_null($this->getInfo()['document_number_id']))
            DB::query("UPDATE tb_prodoc_document_number
                                SET reservation_status = 1 WHERE  id = " . $this->getInfo()['document_number_id']);

        DB::query("DELETE FROM tb_prodoc_certificate  WHERE document_id = " . $this->id);
        DB::query("DELETE FROM tb_daxil_olan_senedler  WHERE id = " . $this->id);

        $this->log->create([
            'operation' => Log::OPERATION_DELETE,
            'operator_id' => NULL,
            'log_text' =>$log_text
        ]);
        foreach ($this->getRelatedTasks() as $task_info){

            $task = new Task($task_info['id']);
            $task->delete();

        }

        return true;


    }
    public function documentDeleteBackUp(){

        $taskData = removeNumericKeys($this->getData());
        unset($taskData['id']);
        unset($taskData['incoming_document_id']);

        return DB::insertAndReturnId('tb_daxil_olan_senedler_deleted', $taskData);
    }

    public function getRelatedTasks($columnsOnly=false){

        if ($columnsOnly) {
            return DB::fetchColumnArray('SELECT id FROM v_derkenar WHERE daxil_olan_sened_id = '.$this->id);
        }else {
            return DB::fetchAll('SELECT id FROM v_derkenar WHERE daxil_olan_sened_id = '.$this->id);
        }
    }

    public function getRelatedDocuments()
    {
        $id = $this->getId();

        $sql = "
            SELECT
                (
                    CASE
                    WHEN t1.internal_document_id = $id THEN
                        (
                            (
                                CASE
                                WHEN t1.related_document_type = 'incoming' THEN
                                    t2.document_number
                                WHEN t1.related_document_type = 'outgoing' THEN
                                    t3.document_number
                                END
                            )
                        )
                    ELSE
                        t4.document_number
                    END
                ) AS document_number,
                (
                    CASE
                    WHEN t1.internal_document_id = $id THEN t1.related_document_id
                    ELSE t1.internal_document_id
                    END
                ) AS id,
                (
                    CASE
                    WHEN t1.internal_document_id = $id
                        THEN t1.related_document_type
                    ELSE
                        'incoming'
                    END
                ) AS type,
                (
                 CASE 
                 WHEN t1.type = 1 
                 THEN 
                    N'(Əlaqələndirilmiş sənəd)' 
                 ELSE N'' END
                ) as related_type,
                '' AS answer_is_not_required
            FROM
                tb_internal_document_relation AS t1
            
            LEFT JOIN v_daxil_olan_senedler_corrected AS t2
                ON t2.id = t1.related_document_id AND t1.related_document_type = 'incoming'
                
            LEFT JOIN v_chixan_senedler AS t3 
                ON t3.id = t1.related_document_id AND t1.related_document_type = 'outgoing'
            
            LEFT JOIN v_daxil_olan_senedler_corrected AS t4
                ON t4.id = t1.internal_document_id
            
            WHERE
                t1.internal_document_id = $id
            OR (
                t1.related_document_type = 'incoming'
                AND t1.related_document_id = $id
            )

            UNION

            SELECT
                tb2.document_number,
                tb2.id AS id,
                'outgoing' AS type,
                '' as related_type,
                answer_is_not_required
            FROM
                v_prodoc_outgoing_document_relation AS tb1
            LEFT JOIN v_chixan_senedler AS tb2 ON tb1.outgoing_document_id = tb2.id
            WHERE
                tb1.daxil_olan_sened_id = $id AND (tb2.is_deleted IS NULL OR tb2.is_deleted = 0)
            UNION 
            SELECT
                document_number,
                id,
                'outgoing' as type,
                '' as related_type,
                '' as answer_is_not_required
            FROM
                v_chixan_senedler
            WHERE
                id IN (
                    SELECT
                        outgoing_document_id
                    FROM
                        tb_daxil_olan_senedler
                    WHERE
                        id = $id
                )
        ";

        return DB::fetchAll($sql);
    }

    public function getMyTask($executor = null)
    {
        $relatedAcceptedTasks = $this->getRelatedAcceptedTasks();
        $taskExecutors = array_keys($relatedAcceptedTasks);

        if (!is_null($executor)) {
            // in_array

            if (!in_array($executor,$taskExecutors)){
                return null;
            }

            $taskExecutors = [$executor];
        }






        $executorId = $this->powerOfAttorney->canExecute($taskExecutors, true);
        if (false === $executorId) {

            return null;
        }

        if (isset($relatedAcceptedTasks[$executorId])) {
            return new Task($relatedAcceptedTasks[$executorId]['id']);
        }



        return null;
    }

    public function ustDerkenarlarinMesulShexsleri()
    {
        $relatedAcceptedTasks = $this->getRelatedMainAcceptedTasks();
        return array_keys($relatedAcceptedTasks);
    }
    public function butunDerkenarlarinMesulShexsleri()
    {

        $relatedAcceptedTasks = $this->getRelatedAcceptedTasks();
        return array_keys($relatedAcceptedTasks);
    }
    public function neticeQeydOlunub(): bool
    {
        return (int)$this->data['netice'] > 0;
    }

    public function derkenarYazaBilenShexshler()
    {
        $users = [];

        if ($this->neticeQeydOlunub()) {
            return [];
        }
        if ((int)$this->getInfo()['state'] !== Document::STATE_AUTHOR_ACCEPTED) {
            return $users;
        }



        $users[] = (int)$this->getInfo()['rey_muellifi'];

        if ($this->isInformative()) {
            return $users;
        }

        return array_merge($this->ustDerkenarlarinMesulShexsleri(), $users);
    }

    public function derkenarYazaBiler()
    {
        return $this->powerOfAttorney->canExecute($this->derkenarYazaBilenShexshler())&& !$this->neticeQeydOlunub();
    }

    public function senedHazirlayaBilenUserler()
    {
        if (getProjectName() === ANAMA) {
            $users = [];
            if ($this->neticeQeydOlunub()) {
                return [];
            }
            if ((int)$this->getInfo()['state'] !== Document::STATE_AUTHOR_ACCEPTED) {
                return [];
            }

            if ($this->isInformative()) {
                return [];
            }
            $users[] = (int)$this->getInfo()['rey_muellifi'];

            if ($this->isInformative()) {
                return $users;
            }
            return array_merge($this->ustDerkenarlarinMesulShexsleri(), $users);
        } else {
            if ($this->neticeQeydOlunub()) {
                return [];
            }
            if ((int)$this->getInfo()['state'] !== Document::STATE_AUTHOR_ACCEPTED) {
                return [];
            }

            if ($this->isInformative()) {
                return [];
            }

            return $this->butunDerkenarlarinMesulShexsleri();
        }
    }

    public function senedHazirlayaBiler()
    {
        return $this->powerOfAttorney->canExecute($this->senedHazirlayaBilenUserler())&& !$this->neticeQeydOlunub();
    }

    public function isInComing(){
        if($this->getData()['tip'] != Document::TIP_DAXILI){
            return true;
        }
        return false;
    }

    function isheTikeBilerElaveShertTS()
    {
        if ((int)$this->getInfo()['tip'] !== Document::TIP_DAXILI) {
            return false;
        }

        $umumi_forma = DB::fetchColumn("SELECT tb1.internal_document_type_id FROM tb_daxil_olan_senedler AS tb1 LEFT JOIN tb_prodoc_inner_document_type AS tb2 ON tb2.id = tb1.internal_document_type_id WHERE tb1.id = '".$this->getId()."' AND tb2.extra_id = 'umumi_forma' ");

        if (false === $umumi_forma) {
            return false;
        }
        $sherhle_bagli = DB::fetchColumn("SELECT tb2.sherqle_baqli FROM tb_umumi_forma AS tb1 LEFT JOIN tb_sened_novu AS tb2 ON tb2.id = tb1.sened_novu WHERE tb1.document_id = '".$this->getId()."' AND tb2.sherqle_baqli = '1'");

        if (false === $sherhle_bagli) {
            return false;
        }

        return true;
    }

    public function isheTikeBilenUserler()
    {
        if (getProjectName() === ANAMA) {
            $users = [];
            if ($this->neticeQeydOlunub()) {
                return [];
            }
            if ((int)$this->getInfo()['state'] !== Document::STATE_AUTHOR_ACCEPTED) {
                return [];
            }

            if ($this->isInformative()) {
                return [];
            }

            if (getProjectName() === TS && false === $this->isheTikeBilerElaveShertTS()) {
                return [];
            }

            $users[] = (int)$this->getInfo()['rey_muellifi'];

            if ($this->isInformative()) {
                return $users;
            }
            return array_merge($this->ustDerkenarlarinMesulShexsleri(), $users);
        } else {
            if ($this->neticeQeydOlunub()) {
                return [];
            }
            if ((int)$this->getInfo()['state'] !== Document::STATE_AUTHOR_ACCEPTED) {
                return [];
            }

            if ($this->isInformative()) {
                return [];
            }

            if (getProjectName() === TS && false === $this->isheTikeBilerElaveShertTS()) {
                return [];
            }

            return $this->butunDerkenarlarinMesulShexsleri();
        }
    }

    public function isheTikeBiler()
    {
        return $this->powerOfAttorney->canExecute($this->isheTikeBilenUserler()) && !$this->neticeQeydOlunub();
    }

    public function getRelatedAcceptedTasks()
    {
        return Task::getRelatedAcceptedTasksIndexedByExecutors($this);
    }

    public function getRelatedMainAcceptedTasks()
    {
        return Task::getRelatedMainAcceptedTasksIndexedByExecutors($this);
    }

    public function getRelatedInternalDocumentsHTMLTree($additionalRelatedDocs = [], $params = [])
    {
        $relatedDocuments = array_merge($additionalRelatedDocs, $this->getRelatedDocuments());
        $documentNumber = $this->record['document_number'];

        require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';
        return require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/related_internal_documents_tree.php';
    }

    public function getDetailedInformationHTML($params = null)
    {
        $id = $this->getId();

        if (is_null($params)) {
            $params = [];
        }
        require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';
        return require_once DIRNAME_INDEX . 'prodoc/templates/dashboard/internal_document_detailed_information_t.php';

    }

    public function canCreateAppeal()
    {
        return self::SENED_TIP_ICRA_UCHUN === (int)$this->record['sened_tip'];
    }

    public function isInformative()
    {
        return self::SENED_TIP_MELUMAT_UCHUN === (int)$this->record['sened_tip'];
    }

    public function onFullApprove()
    {
        if (!$this->isInformative()) {
            return;
        }

        $this->setStatus(self::STATUS_BAGLI);
    }

    public function getHistoryKey(): string
    {
        return 'document';
    }
    public function getLogKey():string {
        return 'incoming_document';
    }

    public function neticeniDaxilEdeBiler()
    {
        $currentUserId = (int)$this->currentUserId;

        $neticeniDaxilEdeBiler = false;
        if ((int)$this->getData()['rey_muellifi'] === $currentUserId) {
            require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
            $priv = new Privilegiya();
            $netice_bir_icrachi_olduqda_priv = $priv->getByExtraId('netice_bir_icrachi_olduqda');

            // rey muelifidirse ve privilegiyasi varsa, neticeni qeyd eliye biler
            $neticeniDaxilEdeBiler = $netice_bir_icrachi_olduqda_priv === 1;
        } else {
            $daxId = $this->getId();

            $sql = "
                SELECT id, mesul_shexs
                FROM v_derkenar
                WHERE daxil_olan_sened_id = $daxId
            ";

            foreach (DB::fetchAll($sql) AS $task) {
                $task = new Task($task['id']);

                // vekaletname lazimdir yoxsa yox (deqiqleshdirmek)?
                if ((int)$task->data['mesul_shexs'] !== $currentUserId) {
                    continue;
                }

                if ($task->isSubTask()) {
                    $mainTask = $task->getMainTask($task);
                    if ((int)$mainTask->data['specifiesResult']) {
                        $neticeniDaxilEdeBiler = true;
                    }
                } else {
                    if ((int)$task->data['specifiesResult']) {
                        $neticeniDaxilEdeBiler = true;
                    }
                }
            }
        }

        if ($neticeniDaxilEdeBiler) {
            return $neticeniDaxilEdeBiler;
        }

        // tapsiriq emridirse
        if ((int)$this->getInfo()['internal_document_type_id']) {
            require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
            $extraId = InternalDocument::getExtraIdById($this->getInfo()['internal_document_type_id']);

            if ('task_command' === $extraId) {
                $doc = DB::fetchOneColumnBy('tb_internal_document_relation', 'related_document_id', [
                    'internal_document_id' => $this->getId(),
                    'related_document_type' => 'incoming'
                ]);

                if ($doc !== false) {
                    $pasirigaBagliDaxilOlanSened = new Document($doc);
                    if ((int)$pasirigaBagliDaxilOlanSened->getData()['tip'] === Document::TIP_HUQUQI || (int)$pasirigaBagliDaxilOlanSened->getData()['tip'] === Document::TIP_FIZIKI) {
                        if ($pasirigaBagliDaxilOlanSened->neticeniDaxilEdeBiler()) {
                            $neticeniDaxilEdeBiler = true;
                        }
                    }
                }
            }
        }


        return $neticeniDaxilEdeBiler;
    }

    public function correctDocumentStatus()
    {
        $status = $this->getCorrectDocumentStatus();

        $this->setStatus($status);

        return $status;
    }
    public function canExecuteOperationClosesDocument(){

        $id = $this->getId();

        $creators = $this->relatedOutgoingDocumentsCreators();
        $executors = DB::arrayToSqlList( $creators);

        return DB::fetchColumn("
         (SELECT COUNT(0)
             FROM v_daxil_olan_senedler daxil_olan
                      LEFT JOIN v_derkenar ON v_derkenar.daxil_olan_sened_id = daxil_olan.id
             WHERE daxil_olan.id = {$id}
               AND ( mesul_shexs IN ( ".$executors." )
                OR daxil_olan.rey_muellifi IN ( ".$executors." )
            
            
                OR (
                 SELECT TOP 1 pa.from_user_id
                 FROM tb_prodoc_power_of_attorney AS pa
                 LEFT JOIN tb_daxil_olan_senedler AS document
                 ON pa.document_id = document.id
                 LEFT JOIN tb_prodoc_power_of_attorney_allowed_doc AS allowed_doc
                 ON pa.id = allowed_doc.power_of_attorney_id
                 WHERE (pa.to_user_id IN (".$executors."))
               AND ( pa.from_user_id = mesul_shexs )
               AND (
                 (
                 ( allowed_doc.doc_type = 1
               AND pa.created_at
                 < document.created_at)
                OR
                 (allowed_doc.doc_type = 2
               AND document.status =1)
                OR
                 (allowed_doc.doc_type = 3
               AND pa.created_at >= document.created_at)
            
                 )
               AND CAST(GETDATE() AS DATE) BETWEEN start_date
               AND ISNULL(end_date
                 , GETDATE()))
            
            
                 ) IN (".$executors.")
            
            
                OR (
                 SELECT TOP 1 pa.from_user_id
                 FROM tb_prodoc_power_of_attorney AS pa
                 LEFT JOIN tb_daxil_olan_senedler AS document
                 ON pa.document_id = document.id
                 LEFT JOIN tb_prodoc_power_of_attorney_allowed_doc AS allowed_doc
                 ON pa.id = allowed_doc.power_of_attorney_id
                 WHERE (pa.to_user_id IN (".$executors."))
               AND ( pa.from_user_id = daxil_olan.rey_muellifi )
               AND (
                 (
                 ( allowed_doc.doc_type = 1
               AND pa.created_at
                 < document.created_at)
                OR
                 (allowed_doc.doc_type = 2
               AND document.status =1)
                OR
                 (allowed_doc.doc_type = 3
               AND pa.created_at >= document.created_at)
            
                 )
               AND CAST(GETDATE() AS DATE) BETWEEN start_date
               AND ISNULL(end_date
                 , GETDATE()))
            
            
                 ) IN (".$executors.")
                 ))");

    }


    public function lastOperationClosesDocument()
    {
        $id = $this->getId();

        $executorsWhere = $this->canExecuteOperationClosesDocument();

        $sql = "
              SELECT TOP 1
                M.tip, 
                AO.dos_status
            FROM v_prodoc_muraciet M
            LEFT JOIN tb_prodoc_appeal_outgoing_document AO
             ON AO.appeal_id = M.id
            LEFT JOIN tb_chixan_senedler C
             ON C.id = AO.outgoing_document_id
            WHERE
             M.daxil_olan_sened_id = {$id} AND
              1 <> ( SELECT sened_tip FROM v_daxil_olan_senedler WHERE id = {$id} ) 
              
               AND
                  (
                 
                    (
                    
                    tip = 'sened_hazirla'
                    AND  0 < $executorsWhere 
                    AND
                        AO.dos_status IS NOT NULL 
                    ) 
                 OR 
                    (
                        tip = 'ishe_tik' AND
                        M.dos_status IS NOT NULL AND 
                        1 <> ( SELECT sened_tip FROM v_daxil_olan_senedler WHERE id = {$id} ) 
                    )
                )
                
            ORDER BY
            (
                CASE
                    WHEN C.id IS NOT NULL
                    THEN C.last_operation_date
                    ELSE M.created_at
                END
            ) DESC
        ";

        $lastOperation = DB::fetch($sql);

        $sql= "SELECT status FROM v_daxil_olan_senedler 
                WHERE id = {$id} AND 1 = sened_tip ";

        $appeal_doc_type = DB::fetchColumn($sql);


        if ($appeal_doc_type==Document::STATUS_BAGLI){


            return true;
        }

        if (false === $lastOperation) {
            return false;
        }

        if ($lastOperation['tip'] === "sened_hazirla" && (int)$lastOperation['dos_status'] === self::STATUS_ACIQ) {
            return false;
        }

        return true;
    }
    public function relatedOutgoingDocumentsCreators(){

        $sql = " SELECT DISTINCT relation.created_by
                    FROM v_prodoc_outgoing_document_relation relation
                             LEFT JOIN v_daxil_olan_senedler daxil_olan ON daxil_olan.id = relation.daxil_olan_sened_id
                    WHERE relation.daxil_olan_sened_id = ".$this->getId();

        $creators = DB::fetchColumnArray($sql);

        return !$creators ? [0] : $creators ;
    }

    public function hasTaskWithoutOperation()
    {
        $id = $this->getId();

        $sql = sprintf("
            SELECT TOP 1 D.id
            FROM tb_derkenar D
            LEFT JOIN
            (
                SELECT M.id, M.derkenar_id
                FROM v_prodoc_muraciet M
                LEFT JOIN tb_prodoc_appeal_outgoing_document AOD ON AOD.appeal_id = M.id
                LEFT JOIN tb_chixan_senedler C ON C.id = AOD.outgoing_document_id
                LEFT JOIN tb_daxil_olan_senedler RDOS ON RDOS.id = M.related_document_id
                WHERE
                (C.id IS NOT NULL AND C.is_deleted = 0) OR
                (RDOS.id IS NOT NULL AND RDOS.state NOT IN (%s)) OR
                (RDOS.id IS NULL AND C.id IS NULL)
            ) M
            ON D.id = M.derkenar_id
            LEFT JOIN tb_derkenar AS ALT_D
                ON D.id = ALT_D.parentTaskId
            WHERE
            D.daxil_olan_sened_id = $id AND
            M.id IS NULL AND
            ALT_D.id IS NULL
        ", self::STATE_IN_TRASH);

        $taskWithoutOperation = DB::fetch($sql);

        return false !== $taskWithoutOperation;
    }

    public function hasNonCompleteOperation()
    {
        $id = $this->getId();

        if (isset($this->cache['hasNonCompleteOperation'])) {
            return $this->cache['hasNonCompleteOperation'];
        }

        $activeAppealStatusOnlyApproved = Appeal::STATUS_TESTIQLEMEDE;
        $dosStatusAciq = self::STATUS_ACIQ;
        $dosStatusBagli = self::STATUS_BAGLI;
        $activeODStatusesList = sprintf("%s, %s",
            OutgoingDocument::STATUS_TESTIQLEMEDE,
            OutgoingDocument::STATUS_IMTINA_OLUNUB
        );
        $xosStatusCanceled = OutgoingDocument::STATUS_LEGV_OLUNUB;

        $sql = sprintf("
            SELECT TOP 1 M.id
            FROM v_prodoc_muraciet M
            LEFT JOIN tb_prodoc_appeal_outgoing_document AO
             ON AO.appeal_id = M.id
            LEFT JOIN tb_chixan_senedler C
             ON C.id = AO.outgoing_document_id
            LEFT JOIN tb_daxil_olan_senedler RDOS ON RDOS.id = M.related_document_id
            LEFT JOIN v_chixan_senedler outgoing_documents ON AO.outgoing_document_id = outgoing_documents.id
            WHERE
            (
                (C.id    IS NOT NULL AND (C.is_deleted IS NULL OR C.is_deleted = 0)) OR
                (RDOS.id IS NOT NULL AND RDOS.state NOT IN (5)) OR
                (RDOS.id IS NULL AND C.id IS NULL)
            )
            AND
            outgoing_documents.status <> $xosStatusCanceled
            AND 
            M.daxil_olan_sened_id = {$id}
            AND
            (
                (
                    M.tip = 'ishe_tik' AND
                    M.status IN ($activeAppealStatusOnlyApproved)
                )
                OR
                (
                    M.tip = 'sened_hazirla' AND
                    AO.dos_status = {$dosStatusBagli} AND
                    C.status IN ($activeODStatusesList)
                )
                OR
                (
                    M.tip = 'sened_hazirla' AND
                    AO.dos_status = {$dosStatusAciq}
                    AND
                    (
                        C.answer_is_not_required IS NULL OR 
                        C.answer_is_not_required = 0
                    )
                    AND
                    (
                        SELECT TOP 1 D.id
                        FROM tb_daxil_olan_senedler D
                        WHERE D.outgoing_document_id = C.id
                    ) IS NULL
                )
            )
          
        "
        );
        $achiqEmeliyyat = \DB::fetch($sql);

        return $this->cache['hasNonCompleteOperation'] = ($achiqEmeliyyat !== false);
    }
    public function getCorrectDocumentStatusForInformative(){
        foreach ($this->getTasks() as $task){

            if($task['approving_status']==Task::STATUS_TESTIQLEMEDE){
                return Document::STATUS_ACIQ;
            }
        }
        return Document::STATUS_BAGLI;
    }
    public function getCorrectDocumentStatus()
    {
        if ($this->isInformative()) {
            return $this->getCorrectDocumentStatusForInformative();
        }
        $hasNonCompleteOperation = $this->hasNonCompleteOperation();

        if ($hasNonCompleteOperation) {
            return self::STATUS_ACIQ;
        }
        $hasTaskWithoutOperation = $this->hasTaskWithoutOperation();

        if ($hasTaskWithoutOperation) {
            return self::STATUS_ACIQ;
        }

        if (!$this->haveToCheckLastClosingOperation()) {

            return self::STATUS_BAGLI;
        }


        if ($this->lastOperationClosesDocument()) {

            return self::STATUS_BAGLI;
        }


        return self::STATUS_ACIQ;
    }


    public function haveToCheckLastClosingOperation(): bool
    {
        $haveToCheckLastClosingOperation = \Service\Option\Option::getOrCreateValue(
            'haveToCheckLastClosingOperation',
            '1'
        );

        return $haveToCheckLastClosingOperation === '1';
    }

    public function haveDocumentOperation()
    {
        $document_type_id = (int)$this->getInfo()['internal_document_type_id'];
        return DB::fetchColumn("SELECT extra_id FROM tb_prodoc_inner_document_type WHERE id = " . $document_type_id);
    }
    public function getTasks(){
        return DB::fetchAll("SELECT * FROM tb_derkenar WHERE daxil_olan_sened_id = ".$this->getId());
    }
    public function getTaskIds(){
        return DB::fetchColumnArray("SELECT id FROM v_derkenar WHERE daxil_olan_sened_id = ".$this->getId());
    }
}