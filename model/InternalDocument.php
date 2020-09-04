<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 25.05.2018
 * Time: 14:14
 */

require_once DIRNAME_INDEX . '/prodoc/model/DocumentNumber/DocumentNumberGeneral/IDocument.php';
require_once DIRNAME_INDEX . '/prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . '/prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . '/prodoc/model/Document.php';

use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;
use Model\DocumentNumber\DocumentNumberGeneral\IDocument;

class InternalDocument extends Document
    implements
    IDocument,
    IBaseEntity
{
    private $option;
    private $typeId;

    private static $types;

    private static $tableNameMap = [
        'arayish' => 'tb_prodoc_certificate',
        'mezuniyet_emri' => 'tb_prodoc_vacation_orders',
        'ezamiyet_emri' => 'tb_prodoc_business_trip',
        'emek_muqavilesine_xitam_emri' => 'tb_emrler',
        'mezuniyyete_gore_kompensasiya' => 'tb_emrler',
        'icaze' => 'tb_icazeler',
        'ish_taphsirigi' => 'tb_gorushler',
        'emek_muqavilesi' => 'tb_proid_labor_contracts',
        'mezuniyet_erizesi' => 'tb_mezuniyyetler',
        'xitam_erizesi' => 'tb_prodoc_formlar_xitam_erizesi',
        'ishe_xitam_erizesi' => 'tb_proid_employe_petition',
        'ishe_qebul_erizesi' => 'tb_proid_employe_petition',
        'bashqa_ishe_kechirtme_erizsesi' => 'tb_proid_employe_petition',
        'ezamiyyet_erizesi' => 'tb_ezamiyyetler',
        'xestelik_vereqi' => 'tb_xestelik_vereqi',
        'avans_telebi' => 'tb_avanslar',
        'emekhaqqi_avansi' => 'tb_emekhaqqi_avansi',
        'avans_hesabati' => 'tb_avans_hesabat',
        'ishe_qebul_emri'=> 'tb_emrler',
        'power_of_attorney' => 'tb_prodoc_power_of_attorney',
        'task_command' => 'tb_prodoc_task_command',
        'umumi_forma'  => 'tb_umumi_forma',
        'satin_alma'  => 'tb_prodoc_satinalma_sifaris',
        'icra_sexsin_deyisdirilmesi'  => 'tb_icra_sexsin_deyisdirilmesi',
        'icra_muddeti_deyisdirilmesi'  => 'tb_icra_muddeti_deyisdirilmesi',
        'teqdimat'  => 'tb_prodoc_teqdimat',
        'hesabat_yarat'  => 'tb_prodoc_partlamamish_tek_sursat',
        'create_act'  => 'tb_prodoc_aktlar',
        'elave_razilashdirma'  => 'tb_prodoc_elave_razilashdirma',
        'protask_taprsiriq_child'  => 'tb_protask_task_document',
    ];

    public static $columns = [
        'tb_prodoc_certificate' => [
            'belong_to' => 'employe'
        ],
        'tb_prodoc_vacation_orders' => [
            'belong_to' => 'employe'
        ],
        'tb_emrler' => [
            'belong_to' => 'user_id'
        ],
        'tb_icazeler' => [
            'belong_to' => 'user_id'
        ],
        'tb_gorushler' => [
            'belong_to' => 'user_id'
        ],
        'tb_proid_labor_contracts' => [
            'belong_to' => 'employe'
        ],
        'tb_mezuniyyetler' => [
            'belong_to' => 'user_id'
        ],
        'tb_prodoc_formlar_xitam_erizesi' => [
            'belong_to' => NULL
        ],
        'tb_proid_employe_petition' => [
            'belong_to' => 'employe'
        ],
        'tb_ezamiyyetler' => [
            'belong_to' => 'user_id'
        ],
        'tb_xestelik_vereqi' => [
            'belong_to' => 'user_id'
        ],
        'tb_avanslar' => [
            'belong_to' => 'user_id'
        ],
        'tb_emekhaqqi_avansi' => [
            'belong_to' => 'user_id'
        ],
        'tb_avans_hesabat' => [
            'belong_to' => 'kim'
        ],
        'tb_prodoc_power_of_attorney' => [
            'belong_to' => 'from_user_id'
        ],
        'tb_prodoc_satinalma_sifaris' => [
            'belong_to' => 'sifarisci'
        ],
        'tb_icra_sexsin_deyisdirilmesi' => [
            'belong_to' => 'yeni_icra_eden_sexs'
        ],
        'tb_icra_muddeti_deyisdirilmesi' => [
            'belong_to' => NULL
        ],
        'tb_prodoc_teqdimat' => [
            'belong_to' => 'kim'
        ],
        'tb_prodoc_elave_razilashdirma' => [
            'belong_to' => 'emekdash'
        ],
    ];

    private static $columnNameMap = [
        'arayish' => 'order_number',
        'mezuniyet_emri' => 'order_number',
        'ezamiyet_emri' => 'order_number',
        'emek_muqavilesine_xitam_emri' => 'emrin_nomresi',
        'mezuniyyete_gore_kompensasiya' => 'emrin_nomresi',
        'ish_taphsirigi' => NULL,
        'icaze' => NULL,
        'emek_muqavilesi' => NULL,
        'ishe_xitam_erizesi' => 'number',
        'ishe_qebul_erizesi' => 'number',
        'bashqa_ishe_kechirtme_erizsesi' => 'number',
        'ezamiyyet_erizesi' => NULL,
        'mezuniyet_erizesi' => 'order_number',
        'xitam_erizesi'=>'document_number',
        'xestelik_vereqi' => 'document_number',
        'avans_telebi' => 'document_number',
        'emekhaqqi_avansi' => 'senedin_nomresi',
        'avans_hesabati' => NULL,
        'ishe_qebul_emri'=>'emrin_nomresi'
    ];

    public function __construct($id = NULL, $params = NULL)
    {
        parent::__construct($id, $params);

        $this->typeId = $this->getData()['internal_document_type_id'];
    }

    public function getDirection()
    {
        return 'internal';
    }

    public function getOption()
    {
        if (is_null($this->option)) {
            $sql = "
                SELECT id FROM tb_prodoc_inner_document_type
                WHERE id = (
                    SELECT parent_id FROM tb_prodoc_inner_document_type WHERE id = %s
                )
            ";

            $this->option = DB::fetchColumn(sprintf($sql, $this->typeId));
        }

        return $this->option;
    }

    public function getOptionValue()
    {
        return $this->typeId;
    }

    public static function getTableNames()
    {
        return self::$tableNameMap;
    }

    public static function getTableNameByType($type)
    {
        return self::$tableNameMap[$type];
    }

    public function getDocumentNumberId()
    {
        return $this->record['document_number_id'];
    }

    public function getDocumentType()
    {
        $extraId = self::getExtraIdById($this->typeId);

        if ($extraId === "umumi_forma") {
            $rData = $this->getRelatedData();
            return (int)$rData['sened_novu'];
        }

        return 0;
    }

    public function getSenderPersonFirstSurnameLetter($usedFor = null)
    {
        return null;
    }

    public function getSenderPerson()
    {
        return null;
    }

    private static function fillTypes()
    {
        self::$types = DB::fetchAllIndexed(
            'SELECT * FROM tb_prodoc_inner_document_type',
            'extra_id'
        );
    }

    public static function getExtraIdById($id)
    {
        if (is_null(self::$types)) {
            self::fillTypes();
        }

        return array_search($id, array_column(self::$types, 'id', 'extra_id'));
    }

    public static function getIdByExtraId($extraId)
    {
        if (is_null(self::$types)) {
            self::fillTypes();
        }

        if (!array_key_exists($extraId, self::$types)) {
            throw new Exception(sprintf('Type %s is not defined', $extraId));
        }

        return self::$types[$extraId]['id'];
    }

    public function getRelatedData()
    {
        $extraId   = self::getExtraIdById($this->typeId);
        $tableName = self::getTableNameByType($extraId);

        return DB::fetchOneBy($tableName, [
            'document_id' => $this->getId()
        ]);
    }

    public function getRelatedDataId()
    {
        $extraId   = self::getExtraIdById($this->typeId);
        $tableName = self::getTableNameByType($extraId);

        return DB::fetchOneColumnBy($tableName, 'id', [
            'document_id' => $this->getId()
        ]);
    }

    public  function  getInternalDocumentType(){
        if(isset($this->data['internal_document_type_id'])&&!is_null($this->data['internal_document_type_id'])){

           return DB::fetchColumn(" SELECT extra_id FROM tb_prodoc_inner_document_type WHERE id = ".$this->data['internal_document_type_id']);
        }

        return false;
    }
    public function getEditors()
    {
        $editors = [];

        if( ((
            (int)$this->data['state'] === self::STATE_CANCELED
                )
                &&
                (
                    FALSE !== DB::fetch(sprintf("SELECT TOP 1 id FROM tb_prodoc_formlar_tesdiqleme WHERE daxil_olan_sened_id = '%s' AND status = 2", $this->getId()))
                ))
            ||
            ('power_of_attorney'==$this->getInternalDocumentType())){

            $editors[] = (int)$this->data['created_by'];
        }

        return $editors;
    }

    public function canEdit()
    {

        return $this->powerOfAttorney->canExecute($this->getEditors());
    }

    public function legvEdeBiler()
    {
        $sifarisci = DB::fetch(sprintf("SELECT TOP 1 id FROM tb_prodoc_formlar_tesdiqleme WHERE daxil_olan_sened_id = '%s' AND status = 2 AND emeliyyat_tip<>'tesdiq_sifaris'", $this->getId()));

        return $this->powerOfAttorney->canExecute((int)$this->data['created_by']) && (int)$this->data['state'] === self::STATE_CANCELED && $sifarisci !== FALSE;
    }

    public static function createNew(array $data): InternalDocument
    {
        if (!array_key_exists('type', $data)) {
            throw new Exception('Ommited required parameter');
        }

        $type = $data['type'];
        $data = \Util\ArrayUtils::omit($data, ['type']);
        $data = \Util\ArrayUtils::defaults($data, [
            'sened_tip' => self::SENED_TIP_ICRA_UCHUN
        ]);

        $user = new User();

        $typeId = DB::fetchOneColumnBy('tb_prodoc_inner_document_type', 'id', [
            'extra_id' => $type
        ]);

        if (is_null($typeId)) {
            throw new Exception(sprintf('Type %s is not defined', $data['type']));
        }

        $data['state'] = 0;
        $data['status'] = self::STATUS_ACIQ;
        $data['tip'] = self::TIP_DAXILI;
        $data['internal_document_type_id'] = $typeId;

        $data['created_by'] = $user->getSessionUserId();
        $data['TenantId']   = $user->getActiveTenantId();
        $data['is_deleted'] = 0;
        $id = DB::insertAndReturnId('tb_daxil_olan_senedler', $data);

        $self = new self($id);
        $self->setInfo($data);
        $self->setCustomQuery('v_incoming_document_all', true);

        return $self;
    }

    public function getOptionalAppealTypeChecked(){

        $sql = "
            SELECT dos.document_number , appeal.dos_status 
            FROM tb_prodoc_muraciet appeal
            LEFT JOIN v_daxil_olan_senedler dos ON dos.id = appeal.daxil_olan_sened_id
            WHERE appeal.related_document_id = ".DB::quote($this->getId());
        $appeal =DB::fetchAll($sql);


        return  $appeal;
    }

    public function assignNumber($requestData)
    {
        $requestData['document_number']     = get('document_number');
        $requestData['editable_with_select'] = get('editable_with_select');

        $ews = $requestData['editable_with_select'];

        if (getProjectName() === TS) {
            return false;
        }

        $documentNumberGeneral = new DocumentNumberGeneral($this, [
            'manualDocumentNumber' => $requestData['document_number'],
            'editable_with_select' => $ews,
        ]);
        return $documentNumberGeneral->assignNumber();
    }

}