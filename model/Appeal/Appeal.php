<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 24.04.2018
 * Time: 12:22
 */
require_once DIRNAME_INDEX . 'prodoc/model/Appeal/IAppealAssociated.php';
require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation/AbstractEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';

use History\IHistory;
use History\History;
use PowerOfAttorney\IPowerOfAttorneyDocument;
use PowerOfAttorney\PowerOfAttorney;

class Appeal extends BaseEntity
    implements
    IConfirmable,
    IBaseEntity,
    IHistory,
    IPowerOfAttorneyDocument
{
    const TIP_ISHE_TIKILSIN = 'ishe_tik';
    const TIP_SENED_HAZIRLA = 'sened_hazirla';

    public function getHistoryKey(): string
    {
        return 'appeal';
    }

    public function getTableName()
    {
        return 'tb_prodoc_muraciet';
    }

    public function getApproveOrder(array $user)
    {
        $approveType = $user['type'];

        $approveOrder = [
            TestiqleyecekShexs::TIP_MESUL_SHEXS => 1,
            TestiqleyecekShexs::TIP_KURATOR => 2,
            TestiqleyecekShexs::TIP_REY_MUELIFI => 3,
        ];

        if (!array_key_exists($approveType, $approveOrder)) {
            throw new Exception("Approve type \"{$approveType}\" is not defined");
        }

        return $approveOrder[$approveType];
    }

    public function onFullApprove()
    {
        /// ishe tikilmesinin/sherhle baglanmasinin tam testiqidir
        /// Amma bu emeliyatin testiqlemesi ancaq TS da var
        $fromTask = (int)$this->info['derkenar_id'] > 0;

        if ($fromTask) {
            $task = DB::fetchById('tb_derkenar', $this->info['derkenar_id']);
            $incomingDocument = new Document($task['daxil_olan_sened_id']);
        } else {
            $incomingDocument = new Document($this->info['daxil_olan_sened_id']);
        }


        $incomingDocument->correctDocumentStatus();
        $this->setIncomingDocumentResult($incomingDocument);
    }

    private function setIncomingDocumentResult(Document $incomingDocument)
    {
        if (0 === (int)$this->info['netice_id']) {
            return;
        }

        DB::update('tb_daxil_olan_senedler', [
            'netice' => $this->info['netice_id']
        ], $incomingDocument->getId());
    }

    public static function getAppealTypesByExtraId($types)
    {
        $sql = sprintf("
            SELECT extra_id, id
            FROM tb_prodoc_muraciet_tip
            WHERE extra_id IN (
                %s
            )
        ", implode(' , ', array_map(function ($t) {
            return " '$t' ";
        }, $types)));

        return DB::fetchAllIndexed($sql, 'extra_id');
    }

    public static function getPollAppealId()
    {
        return DB::fetchOneColumnBy('tb_prodoc_muraciet_tip', 'id', [
            'extra_id' => 'sorgu'
        ]);
    }

    public static function getAllAppeals(IAppealAssociated $appealAssociated)
    {
        $sql = sprintf("
            SELECT *
            FROM tb_prodoc_muraciet
            WHERE %s = %s
        ", $appealAssociated->getColumnName(), $appealAssociated->getId());

        return DB::fetchAll($sql);
    }

    public static function getRelatedAppealIds(IAppealAssociated $appealAssociated)
    {
        $sql = sprintf("
            SELECT id
            FROM tb_prodoc_muraciet
            WHERE
            %s = %s
        ",
            $appealAssociated->getColumnName(),
            $appealAssociated->getId()
        );

        return DB::fetchColumnArray($sql);
    }

    public static function create(array $data, User $user = NULL)
    {
        $incomingDocumentStatusFromUser = NULL;
        if (array_key_exists('dos_status', $data)) {
            $incomingDocumentStatusFromUser = $data['dos_status'];
        }

        $appealData = \Util\ArrayUtils::pick($data, [
            'tip',
            'daxil_olan_sened_id',
            'derkenar_id',
            'netice_id',
            'note'
        ]);

        $appealData['created_by'] = $_SESSION['erpuserid'];
        $appealData['dos_status'] = Document::STATUS_BAGLI;
        $id = DB::insertAndReturnId('tb_prodoc_muraciet', $appealData);

        $self = new self($id, [
            'info' => $appealData
        ]);

        $self->setCustomQuery('v_prodoc_muraciet', true);

        $document = new Document($self->record['daxil_olan_sened_id']);


        if (self::TIP_SENED_HAZIRLA === $self->getInfo()['tip']) {


            require_once DIRNAME_INDEX . 'prodoc/model/AppealType.php';
            for ($i = 0, $len = count($data['outgoingDocuments']); $i < $len; ++$i) {
                $outgoingDocument = $data['outgoingDocuments'][$i];

                $incomingDocumentStatus = AppealType::getIncomingDocumentStatus(
                    $outgoingDocument->data['muraciet_tip_id'],
                    $incomingDocumentStatusFromUser
                );

                if (array_key_exists('document_elaqelendirme', $data)||!$document->canCreateAppeal() || !$document->senedHazirlayaBiler() ) {
                    $incomingDocumentStatus = (int) $data['document_elaqelendirme']==1 || !$document->canCreateAppeal() || !$document->senedHazirlayaBiler()  ? NULL : $incomingDocumentStatus;
                }


                DB::insert('tb_prodoc_appeal_outgoing_document', [
                    'outgoing_document_id' => $outgoingDocument->getId(),
                    'appeal_id' => $self->getId(),
                    'dos_status' => $incomingDocumentStatus
                ]);
            }
        } else if (self::TIP_ISHE_TIKILSIN === $self->getInfo()['tip']) {

            $poa_user_id = null;
            if(isset($data['poa_user_id'])) {
                $poa_user_id = $data['poa_user_id'];
                unset($data['poa_user_id']);
            }
            $document = new Document((int)$self->record['daxil_olan_sened_id']);

            $poa =new PowerOfAttorney( $document, $_SESSION['erpuserid'], new User());

            if (!$document->isheTikeBiler()){
                return false;
            }


            $executors = [];

            if (!is_null($poa_user_id)) {
                $executors[] = $poa_user_id;
            } else {
                $executors = $document->butunDerkenarlarinMesulShexsleri();
            }


            $history = new History($self);
            $history->create([
                'operation' => 'ishe_tikilsin',
                'note' => $appealData['note'],
                'poa'=> $poa->getPowerOfAttorneysByExecutors($executors)
            ]);

            if (getProjectName() === TS || getProjectName() === AZERCOSMOS || getProjectName() === ANAMA) {
                if (!array_key_exists('confirming_users', $data)) {
                    $data['confirming_users'] = [];
                }

                $confirmingUsers = [];
                for ($i = 0, $len = count($data['confirming_users']); $i < $len; ++$i) {
                    $confirmingUsers[] = [
                        'user_id' => $data['confirming_users'][$i],
                        'type' => 'razilashdiran',
                        'order' => 2
                    ];
                }

                if (isset($appealData['derkenar_id']) && $appealData['derkenar_id'] > 0) {
                    $task = new Task($appealData['derkenar_id']);

                    if($task->isSubTask()){
                        $parentTaskId = new Task($task->getData()['parentTaskId']);

                        $confirmingUsers[] = [
                            'user_id' =>  $parentTaskId->getData()['mesul_shexs'],
                            'type' => 'mesul_shexs',
                            'order' => 1
                        ];
                    }

                    $sql = sprintf("
                        SELECT DISTINCT user_id, tip
                            FROM v_derkenar_elave_shexsler
                            WHERE (derkenar_id  in (SELECT (CASE WHEN parentTaskId != 0 AND parentTaskId IS NOT NULL THEN parentTaskId ELSE id END) as id FROM v_derkenar WHERE id =%s )) AND tip IN ('kurator', 'ishtrakchi')
                                                ", $appealData['derkenar_id']);

                    foreach (DB::fetchAll($sql) as $derkenarElaveShexs) {
                        $confirmingUsers[] = [
                            'user_id' => $derkenarElaveShexs['user_id'],
                            'type' => $derkenarElaveShexs['tip'],
                            'order' => 2
                        ];
                    }
                }

                $confirmation = new \Service\Confirmation\Confirmation($self);
                $confirmation->createConfirmingUsers($confirmingUsers);
            }

        }

        return $self;
    }

    function wasRejected(){

        return DB:: fetchColumn("SELECT 1 FROM tb_prodoc_testiqleyecek_shexs WHERE status = 2 AND related_class='Appeal' AND related_record_id = ".$this->id);
    }

    /**
     * @param array $existingConfirmingUsers
     * @param array $incomingDocuments
     * @return array
     */
    private function getConfirmingUsersTS(array $existingConfirmingUsers = [], $incomingDocuments = [])
    {
        $this->setCustomQuery('v_prodoc_muraciet', true);

        $existingConfirmingUsersWithoutSender = [];
        $sender = false;
        foreach ($existingConfirmingUsers as $confirmingUser) {
            if ($confirmingUser['type'] === "kim_gonderir") {
                $sender = (int)$confirmingUser['user_id'];
            } else {
                $existingConfirmingUsersWithoutSender[] = $confirmingUser;
            }
        }

        $existingConfirmingUsers = $existingConfirmingUsersWithoutSender;


        for ($i = 0, $len = count($incomingDocuments); $i < $len; ++$i) {
            $taskId   = NULL;
            $myTask = getMyTaskByDocumentId($incomingDocuments[$i]);

            if (!is_null($myTask)) {

                $taskId = $myTask['id'];

                $task = new Task($taskId);

                if ($task->isSubTask()) {
                    $mainTask = $task->getMainTask($task);

                    $curators = DB::fetchAllBy('v_derkenar_elave_shexsler', [
                        'derkenar_id' => $mainTask->getId()
                    ]);

                    $existingConfirmingUsers[] = [
                        'type' => TestiqleyecekShexs::TIP_MESUL_SHEXS,
                        'user_id' => $mainTask->info['mesul_shexs']
                    ];
                } else {
                    $curators = DB::fetchAllBy('v_derkenar_elave_shexsler', [
                        'derkenar_id' => $taskId
                    ]);
                }

                foreach (array_filter($curators, function ($c) {
                    return $c['tip'] === "kurator";
                }) AS $curator) {
                    $existingConfirmingUsers[] = [
                        'user_id' => $curator['user_id'],
                        'type' => TestiqleyecekShexs::TIP_KURATOR
                    ];
                }

                foreach (array_filter($curators, function ($c) {
                    return $c['tip'] === "ishtrakchi";
                }) AS $curator) {
                    $existingConfirmingUsers[] = [
                        'user_id' => $curator['user_id'],
                        'type' => 'ishtrakchi'
                    ];
                }
            }
            else
            {
                // Sərbəst XOS yaradan zaman istənilən sayda DOS/DS
                // bağlantı quranda bütün dərkənarların "İcraya nəzarət edən şəxs"
                // və "Həmicraçı"-larına təsdiqə getməlidir

                // TODO: check for rey muelifi

                if (0 === count($incomingDocuments)) {
                    throw new UnexpectedValueException(sprintf("'incomingDocuments' array is empty"));
                }

                $sql = sprintf("
                SELECT DES.user_id, DES.tip
                FROM v_derkenar AS D
                INNER JOIN v_derkenar_elave_shexsler DES ON DES.derkenar_id = D.id
                INNER JOIN tb_daxil_olan_senedler AS DOS ON DOS.id = D.daxil_olan_sened_id
                WHERE
                DOS.id IN (%s) AND
                (D.parentTaskId = 0 OR D.parentTaskId IS NULL) AND
                DES.tip IN ('kurator', 'ishtrakchi') AND
                DOS.rey_muellifi <> %s
            ", DB::arrayToSqlList($incomingDocuments), $_SESSION['erpuserid']);

                $curators = DB::fetchAll($sql);

                foreach (array_filter($curators, function ($c) {
                    return $c['tip'] === "kurator";
                }) AS $curator) {
                    $existingConfirmingUsers[] = [
                        'user_id' => $curator['user_id'],
                        'type' => TestiqleyecekShexs::TIP_KURATOR
                    ];
                }

                foreach (array_filter($curators, function ($c) {
                    return $c['tip'] === "ishtrakchi";
                }) AS $curator) {
                    $existingConfirmingUsers[] = [
                        'user_id' => $curator['user_id'],
                        'type' => 'ishtrakchi'
                    ];
                }
            }
        }

        $rey_muellifi = (int)DB::fetchOneColumnBy('tb_daxil_olan_senedler', 'rey_muellifi', [
            'id' => $this->info['daxil_olan_sened_id']
        ]);

        if ($rey_muellifi > 0) {
            if ((int)$this->data['created_by'] !== $rey_muellifi) {
                $existingConfirmingUsers[] = [
                    'type' => TestiqleyecekShexs::TIP_REY_MUELIFI,
                    'user_id' => $rey_muellifi
                ];
            }

            if ($sender !== FALSE && $sender !== $rey_muellifi) {
                $existingConfirmingUsers[] = [
                    'type' => 'kim_gonderir',
                    'user_id' => $sender
                ];

                $existingConfirmingUsers[] = [
                    'type' => 'kim_gonderir',
                    'user_id' => $sender,
                    'order' => 5
                ];
            }
        }

        $existingConfirmingUsers[] = [
            'type' => TestiqleyecekShexs::TIP_UMUMI_SHOBE,
            'user_id' => NULL,
            'order' => 3
        ];

        $existingConfirmingUsers[] = [
            'type' => TestiqleyecekShexs::TIP_UMUMI_SHOBE,
            'user_id' => NULL,
            'order' => 6
        ];

        return $existingConfirmingUsers;
    }

    private function getConfirmingUsersAzerCosmos(array $existingConfirmingUsers = [], $incomingDocuments = [])
    {
        $taskId = (int)$this->info['derkenar_id'];

        if ($taskId) {
            $task = new Task($taskId);

            if ($task->isSubTask()) {
                $mainTask = $task->getMainTask($task);

                $curators = DB::fetchAllBy('v_derkenar_elave_shexsler', [
                    'derkenar_id' => $mainTask->getId()
                ]);

                $existingConfirmingUsers[] = [
                    'type' => TestiqleyecekShexs::TIP_MESUL_SHEXS,
                    'user_id' => $mainTask->info['mesul_shexs']
                ];
            } else {
                $curators = DB::fetchAllBy('v_derkenar_elave_shexsler', [
                    'derkenar_id' => $taskId
                ]);
            }

            foreach (array_filter($curators, function ($c) {
                return $c['tip'] === "kurator";
            }) AS $curator) {
                $existingConfirmingUsers[] = [
                    'user_id' => $curator['user_id'],
                    'type' => TestiqleyecekShexs::TIP_KURATOR
                ];
            }

            foreach (array_filter($curators, function ($c) {
                return $c['tip'] === "ishtrakchi";
            }) AS $curator) {
                $existingConfirmingUsers[] = [
                    'user_id' => $curator['user_id'],
                    'type' => 'ishtrakchi'
                ];
            }
        }
        else {
            // Sərbəst XOS yaradan zaman istənilən sayda DOS/DS
            // bağlantı quranda bütün dərkənarların "İcraya nəzarət edən şəxs"
            // və "Həmicraçı"-larına təsdiqə getməlidir

            // TODO: check for rey muelifi

            if (0 === count($incomingDocuments)) {
                throw new UnexpectedValueException(sprintf("'incomingDocuments' array is empty"));
            }

            $sql = sprintf("
                SELECT DES.user_id, DES.tip
                FROM v_derkenar AS D
                INNER JOIN v_derkenar_elave_shexsler DES ON DES.derkenar_id = D.id
                INNER JOIN tb_daxil_olan_senedler AS DOS ON DOS.id = D.daxil_olan_sened_id
                WHERE
                DOS.id IN (%s) AND
                (D.parentTaskId = 0 OR D.parentTaskId IS NULL) AND
                DES.tip IN ('kurator', 'ishtrakchi') AND
                DOS.rey_muellifi <> %s
            ", DB::arrayToSqlList($incomingDocuments), $_SESSION['erpuserid']);

            $curators = DB::fetchAll($sql);

            foreach (array_filter($curators, function ($c) {
                return $c['tip'] === "kurator";
            }) AS $curator) {
                $existingConfirmingUsers[] = [
                    'user_id' => $curator['user_id'],
                    'type' => TestiqleyecekShexs::TIP_KURATOR
                ];
            }

            foreach (array_filter($curators, function ($c) {
                return $c['tip'] === "ishtrakchi";
            }) AS $curator) {
                $existingConfirmingUsers[] = [
                    'user_id' => $curator['user_id'],
                    'type' => 'ishtrakchi'
                ];
            }
        }

        return $existingConfirmingUsers;
    }

    private function getConfirmingUsersGeneral(array $existingConfirmingUsers = [])
    {
        $taskId = (int)$this->info['derkenar_id'];

        if ($taskId) {
            $task = new Task($taskId);

            if ($task->isSubTask()) {
                $mainTask = $task->getMainTask($task);

                $curators = DB::fetchAllBy('v_derkenar_elave_shexsler', [
                    'derkenar_id' => $mainTask->getId(),
                    'tip' => 'kurator'
                ]);

                $existingConfirmingUsers[] = [
                    'type' => TestiqleyecekShexs::TIP_MESUL_SHEXS,
                    'user_id' => $mainTask->info['mesul_shexs']
                ];
            } else {
                $curators = DB::fetchAllBy('v_derkenar_elave_shexsler', [
                    'derkenar_id' => $taskId,
                    'tip' => 'kurator'
                ]);
            }

            foreach ($curators AS $curator) {
                $existingConfirmingUsers[] = [
                    'user_id' => $curator['user_id'],
                    'type' => TestiqleyecekShexs::TIP_KURATOR
                ];
            }

            if (getProjectName() !== SN) {
                $rey_muellifi = (int)DB::fetchOneColumnBy('tb_daxil_olan_senedler', 'rey_muellifi', [
                    'id' => $this->info['daxil_olan_sened_id']
                ]);

                if ($rey_muellifi > 0) {
                    $existingConfirmingUsers[] = [
                        'type' => TestiqleyecekShexs::TIP_REY_MUELIFI,
                        'user_id' => $rey_muellifi
                    ];
                }
            }
        }

        return $existingConfirmingUsers;
    }

    public function getConfirmingUsers(array $existingConfirmingUsers = [], $incomingDocuments = [])
    {
        if (getProjectName() === TS || getProjectName() === AP) {
            return $this->getConfirmingUsersTS($existingConfirmingUsers, $incomingDocuments);
        } elseif (getProjectName() === AZERCOSMOS) {
            return $this->getConfirmingUsersAzerCosmos($existingConfirmingUsers, $incomingDocuments);
        } else {
            return $this->getConfirmingUsersGeneral($existingConfirmingUsers);
        }
    }

    /**
     * @param IAppealAssociated $appealAssociated
     * @return mixed
     */
    public static function getLastAppeal(IAppealAssociated $appealAssociated)
    {
        $sql = sprintf("
            SELECT TOP 1 *
            FROM tb_prodoc_muraciet
            WHERE %s = %s
            ORDER BY created_at DESC
        ", $appealAssociated->getColumnName(), $appealAssociated->getId());

        return DB::fetch($sql);
    }

    /**
     *  Eger chixan sened testiqlemededirse (STATUS_TESTIQLEMEDE) ve ya imtina olunub (STATUS_IMTINA_OLUNUB),
     *  chixan sened aciq sayilir
     *
     * @param IAppealAssociated $appealAssociated
     * @return bool
     */
    public static function hasOpenedOutgoingDocuments(IAppealAssociated $appealAssociated)
    {
        $sql = sprintf("
            SELECT COUNT(*)
            FROM v_prodoc_outgoing_document_relation AS tb1
            LEFT JOIN v_chixan_senedler AS tb2 ON tb1.outgoing_document_id = tb2.id
            WHERE
            tb1.%s = %s AND
            (tb2.status = %s OR tb2.status = %s)
        ",
            $appealAssociated->getColumnName(),
            $appealAssociated->getId(),
            OutgoingDocument::STATUS_TESTIQLEMEDE,
            OutgoingDocument::STATUS_IMTINA_OLUNUB
        );

        return (int)DB::fetchColumn($sql) > 0;
    }

    public function getStatus()
    {
        $fromTask = (int)$this->info['derkenar_id'] > 0;

        if ($fromTask) {
            $task = DB::fetchById('tb_derkenar', $this->info['derkenar_id']);
            $incomingDocument = new Document($task['daxil_olan_sened_id']);
        } else {
            $incomingDocument = new Document($this->info['daxil_olan_sened_id']);
        }

        return (int)$incomingDocument->getData()['status'];
    }

    public function getDate(): DateTime
    {

        $fromTask = (int)$this->info['derkenar_id'] > 0;

        if ($fromTask) {
            $task = DB::fetchById('tb_derkenar', $this->info['derkenar_id']);
            $incomingDocument = new Document($task['daxil_olan_sened_id']);
        } else {
            $incomingDocument = new Document($this->info['daxil_olan_sened_id']);
        }
        return new DateTime($incomingDocument->getData()['created_at']);
    }
}