<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 10.08.2018
 * Time: 11:20
 */

namespace Model\InternalDocument;

require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';

use BaseEntity;
use Document;
use Exception;
use History\IHistory;
use IBaseEntity;
use DB;
use IConfirmable;
use Service\Confirmation\Confirmation;
use Task;
use TestiqleyecekShexs;
use User;
use Util\ArrayUtils;
use Util\Date;

class Act extends BaseEntity
    implements
    IBaseEntity,
    IConfirmable,
    IHistory
{
    protected $internal_document_id;

    public function getTableName()
    {
        return 'tb_daxil_olan_senedler';
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }
    public function setInternalDocumentId(int $id)
    {
        return $this->internal_document_id = $id;
    }

    public function getInternalDocumentId():int
    {
        return $this->internal_document_id;
    }
    public static function create(array $data, User $user = null)
    {
        $ptsData = ArrayUtils::pick($data, [
                'act_type',
                'created_at',
                'sheher_id',
                'kend_id',
                'nov',
                'miqdar',
                'yerustu_erazi',
                'yeralti_erazi',
                'neqliyyat_nomresi',
                'mehv_etme',
                'qeyd',
                'tehvil_veren',
                'tehvil_alan_orqan',
                'tehvil_alan_shexs',
                'document_id',
                'task_command_id',

        ]);

        $id= DB::insertAndReturnId('tb_prodoc_aktlar', $ptsData);

        $self = new static($ptsData['document_id']);

        $self->setInternalDocumentId($id);

        $self->createConfirmation($data['task_command_id']);

        $tsId = DB::fetchOneColumnBy('tb_prodoc_testiqleyecek_shexs', 'id', [
            'related_record_id' => $data['task_command_id'],
            'related_class' => 'Model\InternalDocument\Act',
            'tip' => 'akt',
            'user_id' => $_SESSION['erpuserid'],
            'status' => 0
        ]);
        if($ptsData['task_command_id']){
            DB::query("
                 UPDATE tb_daxil_olan_senedler
                    SET document_number = CONCAT( 'Akt/',
                      (SELECT document_number FROM tb_daxil_olan_senedler WHERE id =" . $ptsData['task_command_id'] . ")
                      )
                    WHERE id=".$ptsData['document_id']."
        ");

        }


        if (false !== $tsId) {
            require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
            $testiqleyecekShexs = new TestiqleyecekShexs($tsId);
            $testiqleyecekShexs->testiqle();
        } else {
            DB::insert('tb_prodoc_testiqleyecek_shexs', [
                'related_class' => 'Model\InternalDocument\Act',
                'related_record_id' => $data['task_command_id'],
                'tip' => 'akt',
                'user_id' => $_SESSION['erpuserid'],
                'status' => 1,
                'order' => NULL
            ]);
        }

        return $self;
    }

    private function createConfirmation($taskCommandDocumentId)
    {
        $sql = "
            SELECT created_by
            FROM tb_daxil_olan_senedler
            WHERE id = $taskCommandDocumentId
        ";
        $rm = DB::fetchColumn($sql);

        $confirmingUsers = [];
        $confirmingUsers[] = [
            'user_id' => $rm,
            'type' => TestiqleyecekShexs::TIP_MESUL_SHEXS
        ];

        $confirmation = new Confirmation($this);
        $confirmation->addNewConfirmingUsers($confirmingUsers, false);
    }

    public function edit($data)
    {
        $ptsData = ArrayUtils::omit($data, ['task_command_id', 'document_id']);

        DB::update('tb_prodoc_aktlar', $ptsData, $this->getId(), 'document_id');

        $confirmation = new Confirmation($this);
        $confirmation->removeAllConfirmations();

        $this->createConfirmation($data['task_command_id']);
    }

    function getHistoryKey(): string
    {
        return 'document';
    }
}