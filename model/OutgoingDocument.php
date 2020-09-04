<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 09.04.2018
 */

require_once DIRNAME_INDEX . 'prodoc/model/IConfirmable.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation/AbstractEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/IOutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
require_once DIRNAME_INDEX . 'prodoc/service/Log/ILog.php';
require_once DIRNAME_INDEX . 'prodoc/service/Log/Log.php';
require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
require_once DIRNAME_INDEX . '/utils/utils.php';
require_once DIRNAME_INDEX . 'class/class.bildirish.php';


use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;
use Model\DocumentNumber\DocumentNumberGeneral\IOutgoingDocument;
use History\IHistory;
use History\History;
use Log\ILog;
use Log\Log;
use Util\ArrayUtils;
use PowerOfAttorney\IPowerOfAttorneyDocument;
use PowerOfAttorney\PowerOfAttorney;

class OutgoingDocument extends BaseEntity
    implements
    IConfirmable,
    IBaseEntity,
    IOutgoingDocument,
    IHistory,
    IPowerOfAttorneyDocument,
    ILog

{
    const TABLE_NAME = 'tb_chixan_senedler';

    const TEYINAT_AIDIYATTI_ORQAN = 3;
    const TEYINAT_TABELI_QURUM = 5;
    const TEYINAT_FIZIKI_SHEXS = 4;

    const STATUS_LEGV_OLUNUB = 4;

    const ISTIQAMET_SORGU = 1;
    const ISTIQAMET_YONLENDIRME = 2;
    const ISTIQAMET_CAVAB_MEKTUBU = 3;

    protected $history;
    protected $log;
    protected $powerOfAttorney;

    public function __construct($id = null, $params = null)
    {
        parent::__construct($id, $params);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->history = new History($this);
        $this->log = new Log($this);
        $this->powerOfAttorney = new PowerOfAttorney($this, $_SESSION['erpuserid'], new User());
    }

    public function setPowerOfAttorney(PowerOfAttorney $powerOfAttorney)
    {
        // TODO: should be removed
        // $this->powerOfAttorney = $powerOfAttorney;
    }

    public function getStatus()
    {
        return (int)$this->data['status'];
    }

    public function getDate(): DateTime
    {
        return new DateTime($this->data['created_at']);
    }

    public function getLoggedData(string $operation)
    {
        if ('registration' === $operation || 'edit' === $operation) {
            $this->setCustomQuery('v_chixan_senedler', true);

            return ArrayUtils::pick($this->record, [
                'teyinat_ad', 'gonderen_teshkilat_ad'
            ]);
        }

        return null;
    }

    public function getDepartmentIndex()
    {
        $departmentIndex = '';

        if ((int)$this->record['kim_gonderir']) {
            $sql = "
                SELECT [index]
                FROM tb_prodoc_department_index
                WHERE dep_id = (
                    SELECT shobe_id
                    FROM tb_users
                    WHERE USERID = %s
                ) AND is_deleted = 0
            ";

            $index = DB::fetchColumn(sprintf($sql, $this->record['kim_gonderir']));

            if (false !== $index) {
                $departmentIndex = $index;
            }
        }

        return $departmentIndex;
    }

    public function getCityIndex()
    {
        $cityIndex = '';

        if (
            self::TEYINAT_FIZIKI_SHEXS === (int)$this->record['teyinat'] &&
            (int)$this->record['gonderen_shexs'] > 0
        ) {
            $sql = "
                SELECT [index]
                FROM tb_prodoc_city_index
                WHERE city_id = (
                    SELECT TOP 1 region
                    FROM v_daxil_olan_senedler_fiziki
                    WHERE 
                    muraciet_eden = %s AND
                    region > 0 AND
                    region IS NOT NULL
                    ORDER BY created_at DESC
                ) AND is_deleted = 0
            ";

            $index = DB::fetchColumn(sprintf($sql, $this->record['gonderen_shexs']));

            if (false !== $index) {
                $cityIndex = $index;
            }
        }

        return $cityIndex;
    }

    public function getDocumentNumberColumnName()
    {
        return null;
    }

    public function getTableName()
    {
        return 'tb_chixan_senedler';
    }

    public function getDirection()
    {
        return 'outgoing';
    }

    public function getDocumentType()
    {
        return (int)$this->record['muraciet_tip_id'];
    }

    public function getDocumentNumberId()
    {
        return (int)$this->record['document_number_id'];
    }

    public function setDocumentNumberId(int $id): void
    {
        $this->setRecordColumnValue('document_number_id', $id);
    }

    public function getOption()
    {
        $optionList = DB::fetchAllIndexed('SELECT extra_id, id FROM tb_prodoc_document_number_pattern_option_list', 'extra_id');

        $sourceTypeMap = [];
        $sourceTypeMap[self::TEYINAT_AIDIYATTI_ORQAN] = $optionList['adiyati_qurum']['id'];
        $sourceTypeMap[self::TEYINAT_TABELI_QURUM]    = $optionList['tabeli_qurum']['id'];
        $sourceTypeMap[self::TEYINAT_FIZIKI_SHEXS]    = $optionList['vetendash']['id'];

        return $sourceTypeMap[(int)$this->record['teyinat']];
    }

    public function getOptionValue()
    {
        if (self::TEYINAT_FIZIKI_SHEXS === (int)$this->record['teyinat']) {
            return (int)DB::fetchOneColumnBy('tb_prodoc_muraciet_eden_tip', 'id', [
                'extra_id' => 'person'
            ]);
        } else {
            return (int)$this->record['gonderen_teshkilat'];
        }
    }
    public function getStatusTitle(){
        $info = $this->getData();
        if ($this->getStatus()==self::STATUS_LEGV_OLUNUB){
            return 'Ləğv olunub';
        }elseif ($info['is_sended']==1){
            return 'Göndərilib';
        }
        elseif ($info['is_sended']==0){
            return 'Göndərilməyib';
        }else{
            return 'Yoxdur';
        }
    }
    public function setStatus($status, $note = NULL)
    {
//        if (
//            ($status === self::STATUS_TESTIQLEMEDE || $status === self::STATUS_LEGV_OLUNUB) &&
//            !$this->duzelishVeLegvEdeBiler()
//        ) {
//            throw new Exception('Access error!');
//        }

        DB::update('tb_chixan_senedler', [
            'status' => $status
        ], $this->info['id']);
    }


    public function getEditors()
    {
        $editors = [];

        if ($this->powerOfAttorney->canExecute((int)$this->data['created_by']) &&
            (int)$this->data['status'] === self::STATUS_IMTINA_OLUNUB) {
            $editors[] = (int)$this->data['created_by'];
        }

        return $editors;
    }

    public function edit($data)
    {

        if (!$this->duzelishEdeBiler()) {
            throw new Exception();
        }

        $imzalayan_shexsler = $data['kim_gonderir'];
        $data['kim_gonderir'] = null;

        DB::update('tb_chixan_senedler', $data, $this->getId());

        $this->history->create([
            'operation' => 'edit',
            'poa' => $this->powerOfAttorney->getPowerOfAttorneysByExecutors($this->getEditors())
        ]);

        foreach ($imzalayan_shexsler as $shexs_id){
            \DB::insert('tb_chixan_senedler_imzalayan_shexsler', [
                'user_id' => $shexs_id,
                'document_id' => $this->getId(),
            ]);
        }
    }

    public function legvEt($note)
    {
        $this->setStatus(OutgoingDocument::STATUS_LEGV_OLUNUB, $note);
        $this->history->create(['operation' => 'legv_et', 'note' => $note]);
        $this->fixLastOperation();

        return $this->closeRelatedIncomingDocumentsIfOperationsAreDone();
    }

    public function fixLastOperation()
    {
        DB::update(self::getTableName(), [
            'last_operation_date' => 'getdate()'
        ], $this->getId(), 'id', ['last_operation_date']);
    }

    public function delete($note)
    {
        if (!$this->canDelete()) {
            throw new NotAllowedOperationException();
        }

        DB::update('tb_chixan_senedler', [
            'is_deleted' => 1
        ], $this->getId());

        $this->history->create(['operation' => 'delete', 'note' => $note]);
        $this->fixLastOperation();

        return $this->closeRelatedIncomingDocumentsIfOperationsAreDone();
    }

    public static function createWithAdditionalParams(array $data, User $user = null, $params = [])
    {


        $poa_user_id = null;
        if(isset($data['poa_user_id'])) {
            $poa_user_id = $data['poa_user_id'];

        }
        unset($data['poa_user_id']);

        $incomingDocumentId = null;
        if(isset($params['daxil_olan_sened_id'][0])) {
            $incomingDocumentId = $params['daxil_olan_sened_id'][0];

        }

        if (!is_null($user)) {
            $data['created_by'] = $user->getSessionUserId();
            $data['TenantId']   = $user->getActiveTenantId();
        }

        if (null !== static::getIsDeletedColumnName()) {
            $data[static::getIsDeletedColumnName()] = 0;
        }

        $imzalayan_shexsler = $data['kim_gonderir'];
        $data['kim_gonderir'] = null;
        $id = DB::insertAndReturnId((new static())->getTableName(), $data);

        $self = new static($id);
        $self->setInfo($data);

        foreach ($imzalayan_shexsler as $shexs_id){
            \DB::insert('tb_chixan_senedler_imzalayan_shexsler', [
                'user_id' => $shexs_id,
                'document_id' => $id,
            ]);
        }



        if($incomingDocumentId && !is_null($poa_user_id)&& $poa_user_id>0){

            $document = new Document((int)$incomingDocumentId);

            $poa =new PowerOfAttorney( $document, $_SESSION['erpuserid'], new User());

            if (!$document->senedHazirlayaBiler()){
                throw new NotAllowedOperationException();
            }

            if (!is_null($poa_user_id)) {
                $executors[] = $poa_user_id;
            } else {
                $executors = array();
            }

            if ($self instanceof IHistory) {
                $history = new History($self);
                $history->create([
                    'operation' => 'registration',
                    'note' => array_key_exists('note', $params) ? $params['note'] : NULL,
                    'poa'=> $poa->getPowerOfAttorneysByExecutors($executors)
                ]);
            }
        }else{
            if ($self instanceof IHistory) {
                $history = new History($self);
                $history->create([
                    'operation' => 'registration',
                    'note' => array_key_exists('note', $params) ? $params['note'] : NULL
                ]);
            }
        }



        return $self;
    }



    public function deleteByAdmin(){
        $document_number = DB::fetchColumn('SELECT document_number FROM v_chixan_senedler WHERE id = '.$this->id);

        $log_text =  $document_number.' nömrəli sənəd  '.date('d M Y,  H:i').' tarixində silindi. ';
        $this->documentDeleteBackUp();

        if(array_key_exists( 'document_number_id', $this->getInfo()) && !is_null($this->getInfo()['document_number_id']))
            DB::query("UPDATE tb_prodoc_document_number
                                SET reservation_status = 1 WHERE  id = " . $this->getInfo()['document_number_id']);

        $notifications = new bildirish_class();

        $notifications->deleteForOutgoingDocument($this->id);

        DB::query("DELETE FROM tb_chixan_senedler  WHERE id = " . $this->id);

        $this->log->create([
            'operation' => Log::OPERATION_DELETE,
            'operator_id' => NULL,
            'log_text' =>$log_text
        ]);

        return true;

    }

    public function documentDeleteBackUp(){

        $taskData = removeNumericKeys($this->getData());
        unset($taskData['id']);

        return DB::insertAndReturnId('tb_chixan_senedler_deleted', $taskData);
    }
    public function duzelishVeLegvEdeBiler()
    {
        return $this->powerOfAttorney->canExecute((int)$this->data['created_by']);
    }

    public function legvEdeBilenler()
    {
        $users = [];

        if ((int)$this->getData()['status'] !== self::STATUS_TESTIQLENIB) {
            return $users;
        }

        $users[] = (int)$this->data['created_by'];

        return $users;
    }

    public function legvEdeBiler()
    {
        return
            $this->powerOfAttorney->canExecute($this->legvEdeBilenler())
            ;
    }

    public function duzelishEdeBiler()
    {
        return
            $this->powerOfAttorney->canExecute((int)$this->data['created_by']) &&
            (int)$this->data['status'] === self::STATUS_IMTINA_OLUNUB
            ;
    }

    public function canDelete()
    {
        return
            (int)$this->data['document_number_id'] === 0 &&
            (int)$this->data['status'] === self::STATUS_IMTINA_OLUNUB &&
            (int)$this->data['is_deleted'] === 0 &&
            $this->powerOfAttorney->canExecute((int)$this->data['created_by'])
            ;
    }

    public function canAddFile()
    {
        $priv = new  Privilegiya();

        return $this->data['is_sended'] != 1 && $priv->getByExtraId('yekun_senedsiz');
    }

    public function bindToIncomingDocument()
    {
        $appeal = DB::fetchOneColumnBy('tb_prodoc_appeal_outgoing_document', 'id', [
            'outgoing_document_id' => $this->getId()
        ]);

        return FALSE !== $appeal;
    }

    public function getOptionalAppealTypeChecked(){

        $sql = "
            SELECT dos.document_number , xos_relation.dos_status 
            FROM tb_prodoc_appeal_outgoing_document xos_relation
            LEFT JOIN tb_prodoc_muraciet appeal ON appeal.id = xos_relation.appeal_id
            LEFT JOIN v_daxil_olan_senedler dos ON dos.id = appeal.daxil_olan_sened_id
            WHERE xos_relation.outgoing_document_id = ".DB::quote($this->getId());
        $appeal =DB::fetchAll($sql);


        return  $appeal;
    }

    public function getSenders()
    {
        $users = [];

        if ($this->isSended()) {
            return $users;
        }

        if ((int)$this->data['status'] !== self::STATUS_TESTIQLENIB) {
            return $users;
        }

        if(getProjectName()===TS){
            $users = getUsersAllowedToChangeResult();
        }
        else{
            if ($this->bindToIncomingDocument()) {
                $sql = sprintf("
                SELECT DOS.created_by
                FROM v_prodoc_outgoing_document_relation REL
                LEFT JOIN tb_daxil_olan_senedler DOS
                 ON DOS.id = REL.daxil_olan_sened_id
                WHERE REL.outgoing_document_id = %s
            ", $this->getId());
                $users[] = DB::fetchColumnArray($sql);
            } else {
                $users[] = (int)$this->data['created_by'];
            }
        }

        return $users;
    }

    public function canSendDocument()
    {
        return $this->powerOfAttorney->canExecute($this->getSenders());
    }

    public function isSended()
    {
        return (int)$this->data['is_sended'] === 1;
    }

    public function isAnswerNotRequired()
    {
        return (int)$this->data['answer_is_not_required'] === 1;
    }

    public function canChangeAnswerIsNotRequired()
    {
        return
            $this->data['status'] == self::STATUS_TESTIQLENIB &&
            $this->powerOfAttorney->canExecute((int)$this->data['created_by']) &&
            $this->isAnswerNotRequired() === FALSE &&
            $this->isSended() &&
            $this->answerIsNotRequiredForType() &&
            $this->hasNoAnswer()
            ;
    }

    public function hasNoAnswer()
    {
        return !$this->hasAnswer();
    }

    public function hasAnswer()
    {
        return FALSE !== DB::fetchOneColumnBy('tb_daxil_olan_senedler', 'id', [
                'outgoing_document_id' => $this->getId()
            ]);
    }

    private function answerIsNotRequiredForType()
    {
        return 1 === (int)DB::fetchOneColumnBy('tb_prodoc_muraciet_tip', 'cavab_gozlenilmir', [
                'id' => $this->data['muraciet_tip_id']
            ]);
    }

    public function setSend()
    {
        if (!$this->canSendDocument())
        {
            throw new Exception('Access error!');
        }

        DB::update('tb_chixan_senedler', [
            'is_sended' => 1
        ], $this->info['id']);

        $this->history->create(['operation' => 'chixan_sened_gonder']);
    }

    public function onAnswerReceive($incomingDocument)
    {
        $this->fixLastOperation();
        $this->closeRelatedIncomingDocumentsIfOperationsAreDone();
    }

    public function answerIsNotRequired($note = null)
    {
        if (!$this->canChangeAnswerIsNotRequired())
        {
            throw new Exception('Access error!');
        }

        DB::update('tb_chixan_senedler', [
            'answer_is_not_required' => 1
        ], $this->getId());

        $this->history->create(['operation' => 'answer_is_not_required', 'note' => $note]);
        $this->fixLastOperation();

        return $this->closeRelatedIncomingDocumentsIfOperationsAreDone();
    }

    private function setIncomingDocumentResult(Appeal $appeal, Document $incomingDocument)
    {
        if (0 === (int)$appeal->info['netice_id']) {
            return;
        }

        $relatedOutgoingDocuments = self::getOutgoingDocumentsByAppealId($appeal->getId());

        $approvedDocumentsNumber = 0;
        foreach ($relatedOutgoingDocuments as $relatedOutgoingDocument) {
            $approvedDocumentsNumber += ((int)$relatedOutgoingDocument['status'] === self::STATUS_TESTIQLENIB);
        }

        if ($approvedDocumentsNumber === count($relatedOutgoingDocuments)) {
            $incomingDocument->setResult((int)$appeal->info['netice_id']);
        }
    }

    private function getRelatedAppeals(): array
    {
        $sql = 'SELECT t2.*
                FROM tb_prodoc_appeal_outgoing_document AS t1
                LEFT JOIN v_prodoc_muraciet AS t2 ON t2.id = t1.appeal_id
                WHERE outgoing_document_id = %s';

        $appeals = DB::fetchAll(sprintf($sql, $this->getId()));

        $appealObjects = [];
        foreach ($appeals as $appealRecord) {
            $appealObjects[] = new Appeal($appealRecord['id'], [
                'info' => $appealRecord
            ]);
        }

        return $appealObjects;
    }

    public function closeRelatedIncomingDocumentsIfOperationsAreDone()
    {
        $affectedDocuments = [];
        $appeals = $this->getRelatedAppeals();

        foreach ($appeals as $appeal) {
            $incomingDocumentId = (int)$appeal->info['daxil_olan_sened_id'];
            $incomingDocument = new Document($incomingDocumentId);

            // internal document
            if ((int)$incomingDocument->getInfo()['internal_document_type_id']) {
                continue;
            } else {
                $prevStatus = $incomingDocument->getStatus();
                $status = $incomingDocument->correctDocumentStatus();

                $affectedDocuments[] = [
                    'doc' => $incomingDocument,
                    'isClosed' => $status === Document::STATUS_BAGLI && $prevStatus === Document::STATUS_ACIQ,
                    'operationsCompleted' => !$incomingDocument->hasNonCompleteOperation()
                ];

                $this->setIncomingDocumentResult($appeal, $incomingDocument);
            }
        }

        return $affectedDocuments;
    }

    public function onFullApprove()
    {
        $appeals = $this->getRelatedAppeals();

        foreach ($appeals as $appeal) {
            $incomingDocumentId = (int)$appeal->info['daxil_olan_sened_id'];
            $incomingDocument = new Document($incomingDocumentId);

            // tapsiriq emridirse
            if ((int)$incomingDocument->getInfo()['internal_document_type_id']) {
                require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
                $extraId = InternalDocument::getExtraIdById($incomingDocument->getInfo()['internal_document_type_id']);

                if ('task_command' === $extraId) {
                    $doc = DB::fetchOneColumnBy('tb_internal_document_relation', 'related_document_id', [
                        'internal_document_id' => $incomingDocument->getId(),
                        'related_document_type' => 'incoming'
                    ]);

                    if ($doc !== FALSE) {
                        $tapsirigaBagliDaxilOlanSened = new Document($doc);
                        // $this->setIncomingDocumentStatus($appeal, $tapsirigaBagliDaxilOlanSened);
                        $this->setIncomingDocumentResult($appeal, $tapsirigaBagliDaxilOlanSened);
                    }
                } else {
                    $incomingDocument->correctDocumentStatus();

                    // TODO: remove the method and add the logic to view
                    $this->setIncomingDocumentResult($appeal, $incomingDocument);
                }
            } else {
                $incomingDocument->correctDocumentStatus();

                // TODO: remove the method and add the logic to view
                $this->setIncomingDocumentResult($appeal, $incomingDocument);
            }
        }

    }

    public function getApproveOrder(array $user)
    {
        if (getProjectName() === TS || getProjectName() === AP) {
            return $this->getApproveOrderTS($user);
        }
        elseif (getProjectName() === AZERCOSMOS) {
            return $this->getApproveOrderAzerCosmos($user);
        }
        else {
            return $this->getApproveOrderGeneral($user);
        }
    }

    private function getApproveOrderTS(array $user)
    {
        $approveType = $user['type'];

        $approveOrder = [
            TestiqleyecekShexs::TIP_MESUL_SHEXS => 1,
            TestiqleyecekShexs::TIP_IMZALAYAN_SHEXS => 2,
            TestiqleyecekShexs::TIP_RAZILASHDIRAN   => 2,
            "kim_gonderir" => 2,
            TestiqleyecekShexs::TIP_KURATOR => 2,
            "ishtrakchi" => 2,
            // UMUMI_SHOBE - 3,
            TestiqleyecekShexs::TIP_REY_MUELIFI => 4,
            // IMZALAYAN_SHEXS - 5,
            // UMUMI_SHOBE - 6,
            "umumi_shobe_nomre" => 7
        ];

        if (!array_key_exists($approveType, $approveOrder)) {
            throw new Exception("Approve type \"{$approveType}\" is not defined");
        }

        return $approveOrder[$approveType];
    }

    private function getApproveOrderAzerCosmos(array $user)
    {
        $approveType = $user['type'];

        $approveOrder = [
            TestiqleyecekShexs::TIP_REDAKT_EDEN => 4,
            TestiqleyecekShexs::TIP_RAZILASHDIRAN => 2,
            TestiqleyecekShexs::TIP_VISA_VEREN => 3,
            TestiqleyecekShexs::TIP_MESUL_SHEXS => 1,
            TestiqleyecekShexs::TIP_KURATOR => 2,
            "kim_gonderir" => 5,
            "ishtrakchi" => 2
        ];

        if (!array_key_exists($approveType, $approveOrder)) {
            throw new Exception("Approve type \"{$approveType}\" is not defined");
        }

        return $approveOrder[$approveType];
    }

    private function getApproveOrderGeneral(array $user)
    {
        $approveType = $user['type'];

        $approveOrder = [
            TestiqleyecekShexs::TIP_REDAKT_EDEN => 1,
            TestiqleyecekShexs::TIP_RAZILASHDIRAN => 2,
            TestiqleyecekShexs::TIP_VISA_VEREN => 3,
            TestiqleyecekShexs::TIP_MESUL_SHEXS => 4,
            TestiqleyecekShexs::TIP_KURATOR => 5,
            TestiqleyecekShexs::TIP_REY_MUELIFI => 6,
            "kim_gonderir" => 7,
            TestiqleyecekShexs::TIP_CHAP_EDEN => 8
        ];

        if (!array_key_exists($approveType, $approveOrder)) {
            throw new Exception("Approve type \"{$approveType}\" is not defined");
        }

        return $approveOrder[$approveType];
    }

    public static function getOutgoingDocumentsByAppealId($appealId)
    {
        $sql = sprintf("SELECT
                    t2.*
                FROM
                    tb_prodoc_appeal_outgoing_document AS t1
                LEFT JOIN tb_chixan_senedler AS t2 ON t2.id = t1.outgoing_document_id
                WHERE
                    t1.appeal_id = %s
        ", $appealId);

        return DB::fetchAll($sql);
    }

    public static function getOpenedAndApprovedOutgoingDocuments($onlySQL = false, array $additionalCriteria = NULL,$soz = NULL)
    {
        $additionalCriteria = DB::performCriteria(is_null($additionalCriteria) ? [] : $additionalCriteria, true);

        if ($soz != NULL){
            $soz =  "AND document_number COLLATE Azeri_Cyrillic_100_CI_AI LIKE '%$soz%'";
        }
        $sql = sprintf("
            SELECT 
                v_chixan_senedler.*
            FROM v_chixan_senedler
            WHERE
            status = %s AND 
            is_deleted = 0
            AND
            (
              SELECT COUNT(*)
              FROM tb_daxil_olan_senedler
              WHERE outgoing_document_id = v_chixan_senedler.id
            ) = 0 AND %s %s
        ",
            self::STATUS_TESTIQLENIB,
            $additionalCriteria,
            $soz

        );

        return $onlySQL ? $sql : DB::fetchAllIndexed($sql, 'id');
    }

    public function onApprove(array $approvingUser, $confirmationService)
    {
        if (getProjectName() === TS || getProjectName() === AP) {
            $this->onApproveTS($approvingUser, $confirmationService);
        }
        elseif (getProjectName() === AZERCOSMOS) {
            $this->onApproveAZERCOSMOS($approvingUser);
        }

        parent::onApprove($approvingUser, $confirmationService);
    }

    public function onApproveTS(array $approvingUser, $confirmationService)
    {
        $documentNumberWasDefined = (int)$this->getData()['document_number_id'] > 0;
        $whoEntersNumber = $approvingUser['tip'] === TestiqleyecekShexs::TIP_UMUMI_SHOBE && $approvingUser['order'] == 6;

        if ($documentNumberWasDefined || (!$whoEntersNumber)) {
            return;
        }

        $documentNumberGeneral = new DocumentNumberGeneral($this);

        if (
            $documentNumberGeneral->isNumberEditable() ||
            $documentNumberGeneral->isNumberEditableWithSelect()
        ) {
            // elave eliyirik nomrenin elave olunmasini

            $confirmationService->createConfirmingUsers([[
                'type'    => 'umumi_shobe_nomre',
                'user_id' => NULL,
            ]]);
        } else {
            // nomresi yoxdur ve elnen hecne yazilmir!
            $documentNumberGeneral->assignNumber(false);
        }

    }

    public function onApproveAZERCOSMOS(array $approvingUser)
    {
        $documentNumberWasDefined = (int)$this->getData()['document_number_id'] > 0;
        $whoEntersNumber = $approvingUser['tip'] === TestiqleyecekShexs::KIM_GONDERIR;

        if ($documentNumberWasDefined || (!$whoEntersNumber)) {
            return;
        }

        $documentNumberGeneral = new DocumentNumberGeneral($this);
        $documentNumberGeneral->assignNumber(false);
    }

    public function getHistoryKey(): string
    {
        return 'outgoing_document';
    }

    public function getLogKey():string {
        return 'outgoing_document';
    }

    public function getExtraIdOfType()
    {
        return DB::fetchOneColumnBy('tb_prodoc_muraciet_tip', 'extra_id', [
            'id' => $this->getData()['muraciet_tip_id']
        ]);
    }

    public function isCanceled()
    {
        return (int)$this->data['status'] === self::STATUS_IMTINA_OLUNUB;
    }

    public function getParticipantsOutgoingDocument($id){

        $confirmation = new \Service\Confirmation\Confirmation($this);

        $role_users = \Util\ArrayUtils::indexGroupped(array_filter($confirmation->getApprovingUsers(), function ($approvingUser) {
            return $approvingUser;
        }),'type',true);

        $sql = "SELECT t.user_id as imzalayan_shexs FROM tb_chixan_senedler_imzalayan_shexsler t  WHERE t.document_id = ".$id."";

        $generalDepartmentUsers = getGeneralDepartmentUsersWithPrivileges();
        foreach (DB::fetchColumnArray($sql) as $shexs){
            $role_users['imzalayan_shexs'][] = ['user_id' => $shexs];
        }

        foreach ($generalDepartmentUsers as $generalDepartmentUser){
            $role_users['umumi_Shobe'][] = ['user_id' => $generalDepartmentUser['USERID']];
        }


        $role_users['created_by'][] = ['user_id' => $this->getData()['created_by']];

        return $role_users;
    }
}