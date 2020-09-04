<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 05.07.2018
 * Time: 12:16
 */
namespace Model\InternalDocument;

require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
include_once DIRNAME_INDEX . 'prodoc/model/Document.php';
include_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/model/IConfirmable.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';

use BaseEntity;
use DateTime;
use Document;
use Exception;
use History\IHistory;
use IBaseEntity;
use DB;
use IConfirmable;
use PowerOfAttorney\IPowerOfAttorneyDocument;
use PowerOfAttorney\PowerOfAttorney;
use Service\Confirmation\Confirmation;
use Task;
use TestiqleyecekShexs;
use User;
use Util\ArrayUtils;

class TaskCommand extends BaseEntity
    implements
    IBaseEntity,
    IConfirmable,
    IHistory,
    IPowerOfAttorneyDocument
{
    public function getTableName()
    {
        return 'tb_daxil_olan_senedler';
    }

    public function getDate(): DateTime
    {
        return new DateTime($this->data['created_at']);
    }

    public function getStatus()
    {
        return (int)$this->data['status'];
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    public static function create(array $data, User $user = null)
    {
        $taskCommandData = ArrayUtils::pick($data, [
            'document_date',
            'movzu',
            'girish',
            'meqsed',
            'xususi_qeydler',
            'document_id',
            'rey_muellifi'
        ]);

        $tcId = DB::insertAndReturnId('tb_prodoc_task_command', $taskCommandData);

        $self = new static($taskCommandData['document_id']);

        $self->createIfBindToDocument($data, $user, $tcId);

        return $self;
    }

    public function edit(array $data, User $user=null){
        $taskCommandData = ArrayUtils::pick($data, [
            'document_date',
            'movzu',
            'girish',
            'meqsed',
            'xususi_qeydler',
            'document_id',
            'rey_muellifi'
        ]);

        $tap= DB::update('tb_prodoc_task_command', $taskCommandData, $this->getId(), 'document_id');
        $tc_id= DB::fetchColumn("SELECT id  FROM tb_prodoc_task_command WHERE document_id=".$this->getId());
        $confirmation = new Confirmation($this);
        $confirmation->removeAllConfirmations();

        $data['related_document_id'] =getConnectedIncomingDocumentId($this->getId());

        $self = new static($this->getId());

        DB::query(
            "DELETE 
                        FROM
                            tb_prodoc_task_command_hesabat_verenler 
                        WHERE
                            task_command_id = (
                        SELECT
                            id 
                        FROM
                            tb_prodoc_task_command 
                        WHERE
                            document_id = ". $this->getId()." 
                            )"
        );

        $self->createIfBindToDocument($data, $user, $tc_id);

        return $self;
    }

    private function createIfNotBindToDocument($data, $user)
    {
        $documentId = $data['document_id'];

        DB::update('tb_daxil_olan_senedler', [
            'state' => Document::STATE_AUTHOR_ACCEPTED
        ], $documentId);

        $taskData = [
            'parentTaskId' => 0,
            'daxil_olan_sened_id' => $documentId,
            'daxili_nezaret' => 0
        ];

        for ($i = 0, $len = count($data['kime']); $i < $len; ++$i) {
            $taskData['mesul_shexs'] = $data['kime'][$i];
            $taskData['nezaretde_saxlanilsin'] = (int)$data['icra_edilme_tarixi_disabled'][$i] === 1 ? 0 : 1;
            $taskData['son_icra_tarixi_var']   = $taskData['nezaretde_saxlanilsin'];

            if ((int)$data['icra_edilme_tarixi_disabled'][$i]) {
                $taskData['son_icra_tarixi'] = NULL;
            } else {
                $taskData['son_icra_tarixi'] = convertValueToSQLFormat('text.date', $data['icra_edilme_tarixi'][$i]);
            }

            $derkenar_metn = DB::findOrCreate('tb_derkenar_metnler', [
                'name' => $data['derkenar_metn'][$i],
                'deleted' => 0
            ]);
            $taskData['derkenar_metn_id'] = $derkenar_metn['id'];
            $taskData['ishtrakchi_shexsler'] = $data['melumat'];
            $taskData['created_by'] = $data['rey_muellifi'];

            Task::create($taskData);
        }

    }

    private function hesabatVerenleriYarat($data, $tcId)
    {
        $hesabatVerenlerData = [];
//        var_dump($data);exit();
        for ($i = 0, $len = count($data['kime']); $i < $len; ++$i) {
            $hesabatVerenlerData['kime'] = (int)$data['kime'][$i];
            $hesabatVerenlerData['derkenar_metn'] = $data['derkenar_metn'][$i];

            if ((int)$data['icra_edilme_tarixi_disabled'][$i]) {
                $hesabatVerenlerData['son_icra_tarixi'] = NULL;
            } else {
                $hesabatVerenlerData['son_icra_tarixi'] = convertValueToSQLFormat('text.date', $data['icra_edilme_tarixi'][$i]);
            }

            $hesabatVerenlerData['task_command_id'] = $tcId;

            DB::insert('tb_prodoc_task_command_hesabat_verenler', $hesabatVerenlerData);
        }
    }

    private function createIfBindToDocument($data, $user, $tcId)
    {
//        if (!isset($data['related_document_id']) || (int)$data['related_document_id'] <= 0) {
//            throw new \BaseException('Daxil olan sənədin seçilməsi vacib dir!');
//        }



        $this->hesabatVerenleriYarat($data, $tcId);

        $confirmingUsers = [];

        if ($data['related_document_id']) {
            $userId = $_SESSION['erpuserid'];

            $document = new Document($data['related_document_id']);
            $docAuthor = $document->getData()['rey_muellifi'];
            if((int)$docAuthor!=$userId){
                $confirmingUsers[] = [
                    'user_id' => $docAuthor,
                    'type' => TestiqleyecekShexs::TIP_REY_MUELIFI
                ];
            }
        }

        foreach ($data['melumat'] as $melutlanacaq) {
            $confirmingUsers[] = [
                'user_id' => $melutlanacaq,
                'type' => TestiqleyecekShexs::TIP_TANISH_OL
            ];
        }

        foreach ($data['kime'] as $melutlanacaq) {
            $confirmingUsers[] = [
                'user_id' => $melutlanacaq,
                'type' => TestiqleyecekShexs::TIP_HESABAT_VER_PARTLAMAMISH_TEK_SURAT
            ];
        }

        $confirmation = new Confirmation($this);
        $confirmation->addNewConfirmingUsers($confirmingUsers, false);

        // Send Notifications
        $daxil_olan_sened_id=$this->getId();
        $ishtirakchi_shexsler=$sql=pdof()->query("Select created_by,  
                    CONCAT(tb_users.Adi, ' ' , tb_users.Soyadi) as user_name, 
                    document_number 
                    from v_daxil_olan_senedler 
                    left JOIN tb_users on v_daxil_olan_senedler.created_by=tb_users.USERID where id=".$daxil_olan_sened_id)->fetch();

        foreach ($data['melumat'] as $ishtirakchi){

            $user->sendNotifications( true, true,
                'icraya_gonderildi',
                $ishtirakchi_shexsler[1], "",
                $daxil_olan_sened_id,
                $ishtirakchi,
                "ishtirakchi",
                "",
                "",
                "",
                $ishtirakchi_shexsler[2],
                "daxil_olan_sened",
                "derkenar"
            );
        }

        foreach ($data['kime'] as $mesul){
            $user->sendNotifications( true, true,
                'icraya_gonderildi',
                $sql[1], "",
                $daxil_olan_sened_id,
                $mesul,
                "derkenar_gonderildi",
                "",
                "",
                "",
                $sql[2],
                "daxil_olan_sened",
                "derkenar"
            );
        }
        $user->sendNotifications( true, true,
            'icraya_gonderildi',
            $sql[1], "",
            $daxil_olan_sened_id,
            $sql[0],
            "derkenar_gonderdi",
            "",
            "",
            "",
            $sql[2],
            "daxil_olan_sened",
            "derkenar"
        );
    }

    public function getApproveOrder(array $user)
    {
        $approveType = $user['type'];

        $approveOrder = [
            TestiqleyecekShexs::TIP_REY_MUELIFI => 1,
            TestiqleyecekShexs::TIP_TANISH_OL => 2,
            TestiqleyecekShexs::TIP_HESABAT_VER_PARTLAMAMISH_TEK_SURAT => 2,
        ];

        if (!array_key_exists($approveType, $approveOrder)) {
            throw new Exception("Approve type \"{$approveType}\" is not defined");
        }

        return $approveOrder[$approveType];
    }
//
//    public function edit($data)
//    {
//
//    }

    public function getStatusColumnName()
    {
        return NULL;
    }

    function getHistoryKey(): string
    {
        return 'document';
    }

    public function canEdit()
    {
        $poa = new PowerOfAttorney(
            new Document($this->data['id']),
            $_SESSION['erpuserid'],
            new User()
        );

        return
            (
                $poa->canExecute((int)$this->data['created_by']) && (int)$this->data['state'] === 4
            )
            ||
            (
                 $poa->canExecute((int)$this->data['created_by'])&& FALSE !== DB::fetch(sprintf("SELECT TOP 1 id FROM tb_prodoc_testiqleyecek_shexs WHERE related_record_id = '%s' AND related_class= N'Model\InternalDocument\TaskCommand' AND status = 2", $this->getId()))
            )
            ;
    }
}