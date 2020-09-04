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

class PartlamamishTekSursat extends BaseEntity
    implements
    IBaseEntity,
    IConfirmable,
    IHistory
{
    public function getTableName()
    {
        return 'tb_daxil_olan_senedler';
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    public static function create(array $data, User $user = null)
    {
        $ptsData = ArrayUtils::pick($data, [
            'hesabat_id',
            'te_adi',
            'document_id',
            'hesabatin_tarixi',
            'rayon_id',
            'sheher_id',
            'bashlangic_noqtenin_koordinatlari',
            'sherq',
            'shimal',
            'dkps',
            'kps',
            'phs_tesviri',
            'veziyyet_zererleshdirilmish',
            'veziyyet_isharelenmish',
            'veziyyet_aparilmish',
            'aparilmish_qurgunun_xususiyyetleri',
            'chirklenmish_erazinin_tesviri',
            'veziyyet_aparilmish',
        ]);

        DB::insertAndReturnId('tb_prodoc_partlamamish_tek_sursat', $ptsData);

        $self = new static($ptsData['document_id']);
        $self->createConfirmation($data['te_id']);

        $tsId = DB::fetchOneColumnBy('tb_prodoc_testiqleyecek_shexs', 'id', [
            'related_record_id' => $data['te_id'],
            'related_class' => 'Model\InternalDocument\TaskCommand',
            'tip' => 'hesabat_ver_pts',
            'user_id' => $_SESSION['erpuserid'],
            'status' => 0
        ]);

        if (false !== $tsId) {
            require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
            $testiqleyecekShexs = new TestiqleyecekShexs($tsId);
            $testiqleyecekShexs->testiqle();
        } else {
            DB::insert('tb_prodoc_testiqleyecek_shexs', [
                'related_class' => 'Model\InternalDocument\TaskCommand',
                'related_record_id' => $data['te_id'],
                'tip' => 'hesabat_ver_pts',
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
 
    public function onStatusChange($newStatus, Confirmation $confirmation)
    {
        parent::onStatusChange($newStatus, $confirmation);

        if (IConfirmable::STATUS_TESTIQLENIB !== $newStatus) {
            return;
        }

        $taskCommandDocumentId = DB::fetchOneColumnBy('tb_internal_document_relation',
            'related_document_id',
            [ 'internal_document_id' => $this->getId() ]
        );

        if ($this->taskinHesabtalariTestiqlenib($taskCommandDocumentId)) {
            $doc = new Document($taskCommandDocumentId);
            $doc->setStatus(Document::STATUS_BAGLI);

            updateOperationStatusOfRelatedDocuments($taskCommandDocumentId);
            correctStatusAndResultOfRelateDocuments($taskCommandDocumentId);

        }
    }

    public function taskinHesabtalariTestiqlenib($taskCommandId)
    {
        $sql = "
            SELECT COUNT(*)
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE tip = 'hesabat_ver_pts' AND related_record_id = $taskCommandId AND
            related_class = 'Model\InternalDocument\TaskCommand'
        ";
        $hesabatVerecekShexslerinSayi = (int)DB::fetchColumn($sql);

        $sql = "
            SELECT R.internal_document_id
            FROM tb_internal_document_relation AS R
            LEFT JOIN tb_daxil_olan_senedler DAX
             ON DAX.id = r.internal_document_id
            LEFT JOIN tb_prodoc_inner_document_type TYPE
             ON TYPE.id = DAX.internal_document_type_id
            WHERE R.related_document_id = $taskCommandId
            AND TYPE.extra_id = 'hesabat_yarat'
        ";
        $tapshirigaBaliHesabatlar = DB::fetchColumnArray($sql);

        if ($hesabatVerecekShexslerinSayi > count($tapshirigaBaliHesabatlar)) {
            return false;
        }

        $sql = sprintf("
            SELECT COUNT(*)
            FROM tb_daxil_olan_senedler
            WHERE status = 2 AND id IN (%s)
        ", implode(',', $tapshirigaBaliHesabatlar));
        $testiqlenmishHesabatlarSay = (int)DB::fetchColumn($sql);

        if ($hesabatVerecekShexslerinSayi > $testiqlenmishHesabatlarSay) {
            return false;
        }

        return true;
    }

    public function edit($data)
    {
        $ptsData = ArrayUtils::omit($data, ['te_id', 'document_id']);

        DB::update('tb_prodoc_partlamamish_tek_sursat', $ptsData, $this->getId(), 'document_id');

        $confirmation = new Confirmation($this);
        $confirmation->removeAllConfirmations();

        $this->createConfirmation($data['te_id']);
    }

    function getHistoryKey(): string
    {
        return 'document';
    }
}