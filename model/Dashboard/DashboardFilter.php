<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 09.07.2018
 * Time: 13:19
 */

namespace Model\Dashboard;

require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/Setting.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/Person.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';

use Model\InternalDocument\TaskCommand;
use OutgoingDocument;
use Service\Confirmation\Confirmation;
use TestiqleyecekShexs;
use Document;
use Exception;
use PowerOfAttorney\Setting;
use PowerOfAttorney\Person;
use User;

class DashboardFilter
{
    private $filters = [];
    private $sessionUserId;
    private $cache;

    public function __construct($sessionUserId)
    {
        $userId = $sessionUserId;

        $documentAuthorFilter = sprintf(
            " ( pa(document.rey_muellifi) AND document.state NOT IN (%s) ) ",
            implode(',', [Document::STATE_IN_INSPECTION, Document::STATE_NUMBER_REQUIRED])
        );

        $this->sessionUserId = $sessionUserId;
        $this->filters = [
            'yeni' => [
                'document' => [
                    " pa(document.created_by) AND document.status <> 2 AND document.state IS NOT NULL AND document.state <> 0 AND document.state <> 4 AND document.state <> 5 AND (netice IS NULL OR netice = 0) 
                            AND
                             (SELECT TOP 1 id FROM v_prodoc_muraciet  AS muraciet WHERE muraciet.daxil_olan_sened_id = document.id AND pa(muraciet.created_by)) IS NULL
                    ",
                    sprintf("
                    document.state <> 4 AND document.state <> 5 AND (document.status IS NULL OR document.status = 1) 
                    AND (
                             (
                               $documentAuthorFilter OR
                               (
                                   (
                                        SELECT TOP 1 id
                                        FROM v_derkenar AS task
                                        WHERE task.daxil_olan_sened_id = document.id AND pa(task.mesul_shexs)
                                        AND status <> %s
                                   ) IS NOT NULL
                                   AND document.status <> 2
                               )
                             ) AND
                             
                             (
                                (SELECT TOP 1 id FROM v_derkenar         AS derkenar WHERE derkenar.daxil_olan_sened_id = document.id AND pa(derkenar.created_by)) IS NULL
                                AND
                                (
                                    SELECT TOP 1 history.id FROM tb_prodoc_history history
                                    LEFT JOIN v_derkenar derkenar ON history.related_record_id = derkenar.id AND history.related_key = 'Task' AND history.operation = 'registration'
                                    LEFT JOIN tb_prodoc_power_of_attorney poa ON poa.id = history.poa_id
                                    WHERE derkenar.daxil_olan_sened_id = document.id AND poa.to_user_id = derkenar.created_by AND ( derkenar.parentTaskId = 0 OR derkenar.parentTaskId IS NULL)
                                
                                ) IS NULL
                             
                             )                           
                              
                              
                              AND
                             (SELECT TOP 1 id FROM v_prodoc_muraciet  AS muraciet WHERE muraciet.daxil_olan_sened_id = document.id AND pa(muraciet.created_by)) IS NULL
                    )
                    ", \Task::STATUS_IMTINA_OLUNUB),
                    sprintf("
                        document.state <> 4 AND 
                       (document.status IS NULL OR document.status = %s)
                       AND
                       (
                            SELECT TOP 1 id
                            FROM tb_prodoc_formlar_tesdiqleme AS INT_DOC_CONFIRMATION
                            WHERE
                            INT_DOC_CONFIRMATION.daxil_olan_sened_id = document.id AND
                            INT_DOC_CONFIRMATION.status = 0 AND 
                            INT_DOC_CONFIRMATION.emeliyyat_tip NOT IN ('melumatlandirma', 'tesdiqleme', 'kurator', 'ishtrakchi', 'derkenar', 'viza', 'mesul_shexs_testiq') AND
                            pa(INT_DOC_CONFIRMATION.user_id)
                       ) IS NOT NULL
                    ", Document::STATUS_ACIQ),
                    "
                       (
                           SELECT TOP 1 id
                           FROM tb_prodoc_testiqleyecek_shexs confirming_user
                           WHERE
                           confirming_user.related_class = 'Model\InternalDocument\TaskCommand' AND
                           confirming_user.status = 0 AND
                           pa(confirming_user.user_id) AND
                           confirming_user.related_record_id = document.id AND
                           confirming_user.tip = 'hesabat_ver_pts'
                       ) IS NOT NULL
                    ",
                ],
                'outgoing_document' => sprintf(
                    " pa(document.created_by) AND document.status = %s ", OutgoingDocument::STATUS_TESTIQLEMEDE
                ),
            ],
            'testiqle' => [
                'document' => [
                    "
                       (
                           SELECT TOP 1 id
                           FROM tb_prodoc_testiqleyecek_shexs confirming_user
                           WHERE
                           confirming_user.related_class NOT IN ('Document', 'OutgoingDocument') AND
                           confirming_user.status = 0 AND
                           pa(confirming_user.user_id) AND
                           confirming_user.related_record_id = document.id AND
                           confirming_user.tip <> 'hesabat_ver_pts' AND
                           confirming_user.tip <> 'tanish_ol'
                       ) IS NOT NULL
                    ",
                    sprintf("
                       (document.status IS NULL OR document.status = %s)
                       AND
                       (
                            SELECT TOP 1 id
                            FROM tb_prodoc_formlar_tesdiqleme AS INT_DOC_CONFIRMATION
                            WHERE
                            INT_DOC_CONFIRMATION.daxil_olan_sened_id = document.id AND
                            INT_DOC_CONFIRMATION.status = 0 AND
                            pa(INT_DOC_CONFIRMATION.user_id) AND
                            (INT_DOC_CONFIRMATION.emeliyyat_tip = 'tesdiqleme' 
                             OR
                                        INT_DOC_CONFIRMATION.emeliyyat_tip = 'kurator'    OR 
                                        INT_DOC_CONFIRMATION.emeliyyat_tip = 'mesul_shexs_testiq'    OR 
                                        INT_DOC_CONFIRMATION.emeliyyat_tip = 'ishtrakchi' )
                            
                            AND 
                            INT_DOC_CONFIRMATION.qrup = (
                                    SELECT MIN(qrup)
                                    FROM tb_prodoc_formlar_tesdiqleme AS INT_DOC_CONFIRMATION
                                    WHERE
                                    INT_DOC_CONFIRMATION.daxil_olan_sened_id = document.id AND
                                    INT_DOC_CONFIRMATION.status = 0 AND
                                    (
                                        INT_DOC_CONFIRMATION.emeliyyat_tip = 'tesdiqleme' OR
                                        INT_DOC_CONFIRMATION.emeliyyat_tip = 'kurator'    OR 
                                        INT_DOC_CONFIRMATION.emeliyyat_tip = 'mesul_shexs_testiq' OR 
                                        INT_DOC_CONFIRMATION.emeliyyat_tip = 'ishtrakchi'
                                    ) 
                            )
                            AND ( SELECT top 1 id FROM tb_prodoc_formlar_tesdiqleme WHERE daxil_olan_sened_id = INT_DOC_CONFIRMATION.daxil_olan_sened_id AND status = 2 ) IS  NULL
                       ) IS NOT NULL
                    ", Document::STATUS_ACIQ),
                    getProjectName() === TS || getProjectName() === AZERCOSMOS || getProjectName() === ANAMA ?
                        "
                            (
                              SELECT TOP 1 confirming_user.id
                              FROM tb_prodoc_testiqleyecek_shexs confirming_user
                              INNER JOIN v_prodoc_muraciet MUR
                                ON 
                              MUR.id = confirming_user.related_record_id AND
                              MUR.daxil_olan_sened_id = document.id AND
                              confirming_user.related_class = 'Appeal' AND
                              confirming_user.status = 0 AND
                              pa(confirming_user.user_id)
                            ) IS NOT NULL
                        " :
                        " 1<>1 "
                ],
                'outgoing_document' =>
                    sprintf("
                       ( %s ) IS NOT NULL
                       ", $this->getConfirmingUserFilter([
                        TestiqleyecekShexs::TIP_REY_MUELIFI,
                        TestiqleyecekShexs::TIP_KURATOR,
                        TestiqleyecekShexs::TIP_MESUL_SHEXS,
                        TestiqleyecekShexs::TIP_UMUMI_SHOBE,
                        "ishtrakchi",
                        "sedr"
                    ], null, [0], true))
            ],
            'imtina_olunmush' => [
                'document' => [
                    sprintf(
                        " pa(document.created_by) AND document.state = %s ", Document::STATE_CANCELED
                    ),

                    sprintf(
                        " document.state = %s AND
                           (
                               SELECT TOP 1 id FROM tb_prodoc_history history
                                  WHERE history.related_key = 'document' AND
                                      history.related_record_id = document.id  AND
                                      history.operator_id = $userId
                           ) IS NOT NULL ",
                        Document::STATE_CANCELED
                    ),
                    "
                    (
                           SELECT TOP 1 id
                           FROM tb_prodoc_testiqleyecek_shexs confirming_user
                           WHERE
                           confirming_user.related_class NOT IN ('OutgoingDocument', 'Appeal') AND
                           confirming_user.status = 2 AND 
                           ( pa(document.created_by) OR pa(confirming_user.operator_id) )  AND
                           related_record_id = document.id
                       ) IS NOT NULL
                    ",
                    sprintf("
                    (
                        SELECT TOP 1 id
                        FROM v_derkenar AS task
                        WHERE task.daxil_olan_sened_id = document.id AND pa(task.created_by)
                        AND status = %s
                    ) IS NOT NULL
                   ", \Task::STATUS_IMTINA_OLUNUB)
                ],
                'outgoing_document' =>
                    [
                        "
                            (
                               SELECT TOP 1 id
                               FROM tb_prodoc_testiqleyecek_shexs confirming_user
                               WHERE
                               confirming_user.related_class NOT IN ('Document', 'Appeal') AND
                               2 = (SELECT TOP 1 status FROM tb_prodoc_testiqleyecek_shexs WHERE status = 2 AND related_record_id=document.id) AND 
                               ( pa(document.created_by) OR pa(confirming_user.operator_id) OR pa(confirming_user.user_id) ) AND
                               related_record_id = document.id
                            ) IS NOT NULL
                        ",
                        sprintf(" pa(document.created_by) AND document.status = %s ", OutgoingDocument::STATUS_IMTINA_OLUNUB)
                    ],
            ],
            'natamam_qeydiyyat' => [
                'document' => " pa(document.created_by) AND (document.state IS NULL OR document.state = 0) AND (document.outgoing_document_id IS NULL OR document.outgoing_document_id = 0) ",
            ],
            'gonderilmeyib' => [
                // issue #855
                'outgoing_document' => sprintf(
                    "
                        document.status = %s AND 
                        document.is_sended IS NULL AND
                        (
                            (
                                pa(document.created_by) AND
                                (
                                    SELECT TOP 1 REL.id
                                    FROM v_prodoc_outgoing_document_relation REL
                                    WHERE REL.outgoing_document_id = document.id
                                ) IS NULL
                            )
                            OR
                            (
                                SELECT TOP 1 REL.id
                                FROM v_prodoc_outgoing_document_relation REL
                                LEFT JOIN tb_daxil_olan_senedler DOS
                                 ON DOS.id = REL.daxil_olan_sened_id
                                WHERE
                                REL.outgoing_document_id = document.id AND 
                                pa(DOS.created_by)
                            ) IS NOT NULL
                        )
                    ",
                    OutgoingDocument::STATUS_TESTIQLENIB
                ),
            ],
            'yoxlama' => [
                'document' => sprintf(" pa(document.yoxlayan_shexs) AND document.state = %s ", Document::STATE_IN_INSPECTION),
            ],
            'netice' => [
                'document' =>
                    [
                        "
                       document.netice IS NULL AND
                       (
                           SELECT TOP 1 id
                           FROM v_derkenar
                           WHERE v_derkenar.daxil_olan_sened_id = document.id AND
                           pa(v_derkenar.mesul_shexs) AND
                           v_derkenar.specifiesResult = 1
                       ) IS NOT NULL
                    ",
                        sprintf("
                       ( %s ) IS NOT NULL
                        ", $this->getConfirmingUserFilter([
                            'umumi_shobe_netice', 'qeydiyyatchi_netice'
                        ], ['Document'])
                        ),
                        sprintf("
                            ( %s ) IS NOT NULL 
                            ", $this->getConfirmingUserFilterFormlar(['umumi_shobe_netice', 'icra_muddeti_deyisdirilmesi', 'icra_sexsin_deyisdirilmesi'], [
                            TestiqleyecekShexs::STATUS_TESTIQLEMEYIB,
                            TestiqleyecekShexs::STATUS_TESTIQLEYIB
                        ])),
                    ]
            ],
            'tanish_ol' => [
                'document' =>
                    [
                        sprintf("
                                ( %s ) IS NOT NULL
                            ", $this->getConfirmingUserFilter([TestiqleyecekShexs::TIP_TANISH_OL], ['IncomingDocument', 'Document','Task', TaskCommand::class])
                        ),
                        "
                            (
                                SELECT TOP 1 id
                                FROM tb_prodoc_formlar_tesdiqleme AS INT_DOC_CONFIRMATION
                                WHERE
                                INT_DOC_CONFIRMATION.daxil_olan_sened_id = document.id AND
                                INT_DOC_CONFIRMATION.status = 0 AND 
                                INT_DOC_CONFIRMATION.emeliyyat_tip = 'melumatlandirma' AND
                                pa(INT_DOC_CONFIRMATION.user_id)
                            ) IS NOT NULL
                        ",
                        " 
                           (
                               SELECT TOP 1 id FROM tb_prodoc_testiqleyecek_shexs testiqleme
                                  WHERE testiqleme.tip = 'tanish_ol' AND
                                      pa(testiqleme.user_id) AND testiqleme.status_changed_at IS NULL AND
                                       testiqleme.related_class = 'Task'
                                     AND ( SELECT TOP 1 derkenar.id FROM v_derkenar derkenar WHERE testiqleme.related_record_id = derkenar.id AND document.id = derkenar.daxil_olan_sened_id) IS NOT NULL

                           ) IS NOT NULL "

                    ]
            ],
            'visa_veren' => [
                'outgoing_document' => sprintf("
               ( %s ) IS NOT NULL
           ", $this->getConfirmingUserFilter([
                    TestiqleyecekShexs::TIP_VISA_VEREN
                ]))
            ],
            'imzala' => [
                'outgoing_document' => "
                    (
                      SELECT TOP 1 id
                      FROM tb_prodoc_testiqleyecek_shexs confirming_user
                      WHERE confirming_user.related_record_id = document.id
                        AND (SELECT TOP 1 status
                               FROM tb_prodoc_testiqleyecek_shexs
                               WHERE status = 2 AND related_record_id = document.id) IS NULL
                        AND confirming_user.related_class IN ('OutgoingDocument')
                        AND (confirming_user.user_id = $userId)
                        AND confirming_user.tip IN ('kim_gonderir')
                        AND confirming_user.status = 0
                    ) IS NOT NULL
                "
            ],
            'razilashdiran' => [
                'document' => "
                    (   document.id IN (
                            SELECT tesdiqlemeler.daxil_olan_sened_id FROM tb_prodoc_formlar_tesdiqleme tesdiqlemeler
                            WHERE pa(tesdiqlemeler.user_id) AND emeliyyat_tip = 'viza' AND status = 0
                        )
                    )
                
                ",

                'outgoing_document' => sprintf("
               ( %s ) IS NOT NULL
           ", $this->getConfirmingUserFilter([
                    TestiqleyecekShexs::TIP_RAZILASHDIRAN
                ]))
            ],
            'redaktor' => [
                'outgoing_document' => sprintf("
               ( %s ) IS NOT NULL
           ", $this->getConfirmingUserFilter([
                    TestiqleyecekShexs::TIP_REDAKT_EDEN
                ]))
            ],
            'redakt_eden' => [
                'outgoing_document' => sprintf("
               ( %s ) IS NOT NULL
           ", $this->getConfirmingUserFilter([
                    TestiqleyecekShexs::TIP_REDAKT_EDEN
                ]))
            ],
            'chap_eden' => [
                'outgoing_document' => sprintf("
               ( %s ) IS NOT NULL
           ", $this->getConfirmingUserFilter([
                    TestiqleyecekShexs::TIP_CHAP_EDEN
                ]))
            ],
            'aciq_men_gonderdiyim' => [
                'document' => sprintf(
                    " document.status = %s AND
               (
                   SELECT TOP 1 id
                   FROM tb_derkenar AS derkenar
                   WHERE document.id = derkenar.daxil_olan_sened_id AND
                   pa(derkenar.created_by)
               ) IS NOT NULL ",
                    Document::STATUS_ACIQ
                )
            ],
            'aciq_icraci_oldugum' => [
                'document' => sprintf(
                    " document.status = %s AND
               (
                   SELECT TOP 1 id
                   FROM tb_derkenar AS derkenar
                   WHERE document.id = derkenar.daxil_olan_sened_id AND
                   pa(derkenar.mesul_shexs)
               ) IS NOT NULL ",
                    Document::STATUS_ACIQ
                )
            ],
            'aciq_icrasina_nezaret_etdiyim' => [
                'document' => sprintf(
                    " document.status = %s AND
               (
                   SELECT TOP 1 derkenar.id
                   FROM tb_derkenar AS derkenar
                   LEFT JOIN tb_derkenar_elave_shexsler elave_shexsler
                   ON derkenar.id = elave_shexsler.derkenar_id
                   WHERE document.id = derkenar.daxil_olan_sened_id AND
                   pa(elave_shexsler.user_id) AND elave_shexsler.tip = 'kurator'
               ) IS NOT NULL ",
                    Document::STATUS_ACIQ
                )
            ],
            'aciq_hemicracisi_oldugum' => [
                'document' => sprintf(
                    " document.status = %s AND
               (
                  SELECT TOP 1 derkenar.id
                   FROM tb_derkenar AS derkenar
                   LEFT JOIN tb_derkenar_elave_shexsler elave_shexsler
                   ON derkenar.id = elave_shexsler.derkenar_id
                   WHERE document.id = derkenar.daxil_olan_sened_id AND
                   pa(elave_shexsler.user_id) AND elave_shexsler.tip = 'ishtrakchi'
               ) IS NOT NULL ",
                    Document::STATUS_ACIQ
                )
            ],
            'aciq_diger' => [
                'document' => sprintf(
                    " document.status = %s AND
               (
                   SELECT TOP 1 id
                   FROM tb_derkenar AS derkenar
                   WHERE document.id = derkenar.daxil_olan_sened_id AND
                   derkenar.created_by <> $userId
               ) IS NOT NULL ",
                    Document::STATUS_ACIQ
                )
            ],
            'aciq_cavab_gozlenilir' => [
                'document' => sprintf(
                    " document.status = %s AND
               (
                   SELECT TOP 1 od_relation.id
                   FROM v_prodoc_outgoing_document_relation AS od_relation
                   LEFT JOIN tb_daxil_olan_senedler AS daxil_olan_sened
                    ON od_relation.outgoing_document_id = daxil_olan_sened.outgoing_document_id
                   LEFT JOIN tb_chixan_senedler AS outgoing_document
                    ON outgoing_document.id = od_relation.outgoing_document_id
                   LEFT JOIN tb_prodoc_muraciet_tip AS muraciet_tip
                    ON muraciet_tip.id = outgoing_document.muraciet_tip_id
                   WHERE
                   od_relation.daxil_olan_sened_id = document.id AND
                   daxil_olan_sened.id IS NULL AND
                   muraciet_tip.extra_id = 'sorgu'
               ) IS NOT NULL ",
                    Document::STATUS_ACIQ
                )
            ],
            'butun_senedler' => [
                'document' => [
                    " pa(document.belong_to) ",
                    " (SELECT TOP 1 history_for_poa.id FROM tb_prodoc_history history_for_poa
                      LEFT JOIN tb_prodoc_power_of_attorney power ON history_for_poa.poa_id = power.id
                    WHERE history_for_poa.related_key= 'document' AND history_for_poa.related_record_id = document.id  AND ( history_for_poa.operator_id = $userId OR power.from_user_id = $userId )  )IS NOT NULL ",

                    "( SELECT TOP 1 id  FROM v_derkenar
                    WHERE v_derkenar.daxil_olan_sened_id = document.id  AND id in (SELECT related_record_id
                                                  FROM tb_prodoc_history
                                                  WHERE related_key= 'task'
                                                    AND operator_id = $userId )) IS NOT NULL",
                    "( SELECT TOP 1 v_derkenar.id  FROM v_derkenar
                    WHERE v_derkenar.daxil_olan_sened_id = document.id AND v_derkenar.id in  (SELECT tb_prodoc_muraciet.derkenar_id FROM tb_prodoc_muraciet WHERE tb_prodoc_muraciet.id in (SELECT related_record_id
                                                  FROM tb_prodoc_history
                                                   LEFT JOIN tb_prodoc_power_of_attorney power ON tb_prodoc_history.poa_id = power.id
                                                  WHERE related_key= 'appeal'
                                                    AND ( tb_prodoc_history.operator_id = $userId OR power.from_user_id = $userId OR power.to_user_id = $userId) ))) IS NOT NULL",
                    " pa(document.created_by) ",
                    $documentAuthorFilter,
                    " pa(document.yoxlayan_shexs) ",
                    sprintf(" (
                           SELECT TOP 1 derkenar.id
                           FROM tb_derkenar derkenar
                           LEFT JOIN v_derkenar_elave_shexsler derkenar_elave_shexsler
                            ON derkenar_elave_shexsler.derkenar_id = derkenar.id
                           WHERE
                           document.id = derkenar.daxil_olan_sened_id AND
                           (
                               (
                                   (pa(derkenar.mesul_shexs) OR pa(derkenar_elave_shexsler.user_id))
                                   AND derkenar.status <> %s
                               ) OR
                               (
                                   pa(derkenar.created_by) AND derkenar.status = %s
                               ) 
                           )
                        ) IS NOT NULL
                    ", \Task::STATUS_IMTINA_OLUNUB, \Task::STATUS_IMTINA_OLUNUB),
                    sprintf("
                            ( %s ) IS NOT NULL
                    ", $this->getConfirmingUserFilterFormlar('all', [
                        TestiqleyecekShexs::STATUS_TESTIQLEMEYIB,
                        TestiqleyecekShexs::STATUS_TESTIQLEYIB
                    ])),
                    sprintf("
                            ( %s ) IS NOT NULL
                    ", $this->getConfirmingUserFilter('all', 'PseudoIncomingInternalDocument', [
                        TestiqleyecekShexs::STATUS_TESTIQLEMEYIB,
                        TestiqleyecekShexs::STATUS_TESTIQLEYIB
                    ])),
                    getProjectName() === TS || getProjectName() === AZERCOSMOS || getProjectName() === ANAMA ?
                        "
                            (
                              SELECT TOP 1 confirming_user.id
                              FROM tb_prodoc_testiqleyecek_shexs confirming_user
                              INNER JOIN v_prodoc_muraciet MUR
                                ON 
                              MUR.id = confirming_user.related_record_id AND
                              MUR.daxil_olan_sened_id = document.id AND
                              confirming_user.related_class = 'Appeal' AND
                              pa(confirming_user.user_id)
                            ) IS NOT NULL
                        " :
                        " 1<>1 "
                ],
                'outgoing_document' => [
                    " pa(document.created_by) ",

                    " (SELECT TOP 1 history_for_poa.id FROM tb_prodoc_history history_for_poa
                      LEFT JOIN tb_prodoc_power_of_attorney power ON history_for_poa.poa_id = power.id
                    WHERE history_for_poa.related_key= 'outgoing_document' AND history_for_poa.related_record_id = document.id  AND ( history_for_poa.operator_id = $userId OR power.from_user_id = $userId )  )IS NOT NULL ",

                    " (
                          SELECT TOP 1 REL.id
                          FROM v_prodoc_outgoing_document_relation REL
                          LEFT JOIN tb_daxil_olan_senedler DOS
                           ON DOS.id = REL.daxil_olan_sened_id
                          WHERE
                          REL.outgoing_document_id = document.id AND 
                          pa(DOS.created_by)
                       ) IS NOT NULL
                    ",
                    sprintf(" pa(document.kim_gonderir) AND document.status <> %s ",
                        OutgoingDocument::STATUS_IMTINA_OLUNUB
                    ),

                    sprintf("
                            document.status <> %s AND ( %s ) IS NOT NULL
                    ", OutgoingDocument::STATUS_IMTINA_OLUNUB, $this->getConfirmingUserFilter('all', ['OutgoingDocument'], [
                        TestiqleyecekShexs::STATUS_TESTIQLEMEYIB,
                        TestiqleyecekShexs::STATUS_TESTIQLEYIB
                    ]))
                ]
            ],
            "yekun_senedsiz" => [
                'document' => " 1<>1 ",
                'outgoing_document' => sprintf(
                    "
                                (
                            SELECT
                            TOP 1 F.id
                            FROM
                                tb_files F
                            WHERE
                                (
                                    F.module_name = 'chixan_senedler_sened_fayl'
                                    OR F.module_name = 'chixan_senedler_esas_sened_fayl'
                                )
                            AND F.is_deleted = 0
                            AND F.module_entry_id = document.id
                        ) IS NULL
                        AND pa(document.created_by)
                        AND document.status = % s
                        AND (
                            document.is_sended = 0
                            OR document.is_sended IS NULL
                        )
                            ", OutgoingDocument::STATUS_TESTIQLENIB
                )
            ],
            "umumi_shobe" => [
                'document' => [
                    " 1 = 1 "
                ],
                'outgoing_document' => [
                    " 1 = 1 "
                ]
            ]
        ];
    }

    private function getGeneralDepartmentTypes()
    {
        return [
            'umumi_shobe',
            'umumi_shobe_netice',
            'umumi_shobe_nomre',
        ];
    }

    private function isRelatedToGeneralDepartmentType($type)
    {
        return in_array($type, $this->getGeneralDepartmentTypes(), true);
    }

    private function hasGeneralDepartmentType($types)
    {
        if ($types === "all") {
            return true;
        }

        foreach ($types as $type) {
            if (true === $this->isRelatedToGeneralDepartmentType($type)) {
                return true;
            }
        }

        return false;
    }

    private function getClassesSQLFilter($classes)
    {
        if ($classes === 'PseudoIncomingInternalDocument') {
            return "confirming_user.related_class NOT IN ('OutgoingDocument', 'Appeal')";
        }

        if (is_null($classes)) {
            $classes = [OutgoingDocument::class];
        }

        $classesSQLFilter = implode(', ', array_map(function ($class) {
            return "'$class'";
        }, $classes));

        return "confirming_user.related_class IN ($classesSQLFilter)";
    }

    private function getTypesSQLFilter($types)
    {
        if ($types === 'all') {
            return " 1 = 1 ";
        }

        if (!is_array($types)) {
            $types = [$types];
        }

        $typesIN = implode(',', array_map(function ($type) {
            return "'$type'";
        }, $types));

        return sprintf("confirming_user.tip IN (%s)  AND status = 0 ", $typesIN);
    }

    private function getConfirmingUserFilter($types, $classes = null, $statuses = [0], $imtina = false): string
    {
        $classesSQLFilter = $this->getClassesSQLFilter($classes);
        $typesSQLFilter = $this->getTypesSQLFilter($types);

        if ($this->hasGeneralDepartmentType($types)) {
            $generalDepartmentLeftJoin = $this->getGeneralDepartmentLeftJoin();

            if ($generalDepartmentLeftJoin === "") {
                $confirmingUserCondition = " pa(confirming_user.user_id) ";
            } else {
                $confirmingUserCondition = "
                (
                    (
                        (GD.Privilege = 'neticenin_qeyd_olunmasi_butun_senedler' OR GD.Privilege IS NULL) OR
                        (GD.Privilege = 'neticenin_qeyd_olunmasi_fiziki' AND document.umumi_tip = 2) OR
                        (GD.Privilege = 'neticenin_qeyd_olunmasi_huquqi' AND document.umumi_tip = 1)
                    )
                    AND
                    (
                        pa(confirming_user.user_id) OR pa(GD.user_id)
                    )                
                )
                ";
            }
        } else {
            $generalDepartmentLeftJoin = "";
            $confirmingUserCondition = " pa(confirming_user.user_id) ";
        }

        $statusFilter = " 1 = 1 ";
        $imtinaFilter = " 1 = 1 ";

        if (count($statuses) > 0) {
            $statusFilter = sprintf(
                " confirming_user.status IN (%s) ",
                implode(',', $statuses)
            );
        }


        if ($imtina) {
            $imtinaFilter = "
                            ((SELECT TOP 1 status
                                       FROM tb_prodoc_testiqleyecek_shexs
                                       WHERE status = 2 AND related_record_id = document.id) IS NULL
                            )";
        }


        return "
           SELECT TOP 1 id
           FROM tb_prodoc_testiqleyecek_shexs confirming_user
           $generalDepartmentLeftJoin
           WHERE


           ( 
                (
                    confirming_user.related_record_id = document.id AND confirming_user.related_class <> 'Task'
                )
           ) AND
           $statusFilter AND
           $classesSQLFilter AND
           $confirmingUserCondition AND
           $typesSQLFilter AND
           $imtinaFilter 
       ";
    }

    private function getConfirmingUserFilterFormlar($types, $statuses = [0]): string
    {
        $typesSQLFilter = $this->getTypesSQLFilter($types);

        if ($this->hasGeneralDepartmentType($types)) {
            $generalDepartmentLeftJoin = $this->getGeneralDepartmentLeftJoin('emeliyyat_tip');

            if ($generalDepartmentLeftJoin === "") {
                $confirmingUserCondition = " pa(confirming_user.user_id) ";
            } else {
                $confirmingUserCondition = "
                (
                    (
                        (GD.Privilege = 'neticenin_qeyd_olunmasi_butun_senedler' OR GD.Privilege IS NULL) OR
                        (GD.Privilege = 'neticenin_qeyd_olunmasi_fiziki' AND document.umumi_tip = 2) OR
                        (GD.Privilege = 'neticenin_qeyd_olunmasi_huquqi' AND document.umumi_tip = 1)
                    )
                    AND
                    (
                        pa(confirming_user.user_id) OR pa(GD.user_id)
                    )                
                )
                ";
            }
        } else {
            $generalDepartmentLeftJoin = "";
            $confirmingUserCondition = " pa(confirming_user.user_id) ";
        }

        $statusFilter = " 1 = 1 ";

        if (count($statuses) > 0) {
            $statusFilter = sprintf(
                " confirming_user.status IN (%s) ",
                implode(',', $statuses)
            );
        }

        return "
           SELECT TOP 1 id
           FROM tb_prodoc_formlar_tesdiqleme confirming_user
           $generalDepartmentLeftJoin
           WHERE
           confirming_user.daxil_olan_sened_id = document.id  AND
           $statusFilter AND
           $confirmingUserCondition AND
           $typesSQLFilter
       ";
    }

    private function getGeneralDepartmentLeftJoin($columnName = 'tip')
    {
        $query = "";
        $generalDepartmentUsers = getGeneralDepartmentUsersWithPrivileges();

        if (!isset($generalDepartmentUsers[0])) {
            return $query;
        }

        $generalDepartmentUsersSelect = array_map(function ($user) {
            return sprintf(" SELECT %s AS user_id, '%s' AS Privilege ", $user['USERID'], $user['Privilege']);
        }, $generalDepartmentUsers);

        $generalDepartmentUsersUnion = implode(' UNION ', $generalDepartmentUsersSelect);

        $generalTypes = implode(',', array_map(function ($type) {
            return " '$type' ";
        }, $this->getGeneralDepartmentTypes()));

        return "LEFT JOIN ( $generalDepartmentUsersUnion ) AS GD
               ON confirming_user.{$columnName} IN ($generalTypes)";
    }

    private function implodeFiltersWithOR($filters): string
    {
        if (!is_array($filters)) {
            return $filters;
        }

        return implode(' OR ', array_map(function ($filter) {
            return " ($filter) ";
        }, $filters));
    }

    private function fillMissedFilters($filters)
    {
        $allFilters = [
            'document',
            'outgoing_document'
        ];

        foreach ($allFilters as $filter) {
            if (!array_key_exists($filter, $filters)) {
                $filters[$filter] = " 1 <> 1 ";
            }
        }

        return $filters;
    }

    private function getPowerOfAttorneysAsProxy()
    {
        if (!isset($this->cache['power_of_attorneys'])) {
            $proxyPerson = new Person($this->sessionUserId, new User());
            $this->cache['power_of_attorneys'] = $proxyPerson->getPowerOfAttorneysAsProxy();
        }

        return $this->cache['power_of_attorneys'];
    }

    private function createPowerOfAttorneyFilter($columnName)
    {
        $userId = $this->sessionUserId;

        $conditions = [];
        $conditions[] = " $columnName = $userId ";

        foreach ($this->getPowerOfAttorneysAsProxy() as $powerOfAttorney) {
            $powerOfAttorneyFilter = sprintf(" $columnName IN (%s) ",
                implode(',', $powerOfAttorney['principals'])
            );

            $allowedDocsConditions = [];
            foreach ($powerOfAttorney['allowed_docs'] as $allowedDoc) {
                if (Setting::NEXT_DOCS === (int)$allowedDoc) {
                    $allowedDocsConditions[] = " (document.created_at > '{$powerOfAttorney['created_at']}') ";
                } elseif (Setting::ALL_OPENED_DOCS === (int)$allowedDoc) {
                    $allowedDocsConditions[] = " (document.status = 1) ";
                } elseif (Setting::PREVIOUS_CLOSED_DOCS === (int)$allowedDoc) {
                    $allowedDocsConditions[] = " (document.status = 2 AND document.created_at <= '{$powerOfAttorney['created_at']}') ";
                }
            }

            if (!empty($allowedDocsConditions)) {
                $powerOfAttorneyFilter = sprintf("$powerOfAttorneyFilter AND (%s)",
                    implode(' OR ', $allowedDocsConditions)
                );
            }

            $conditions[] = $powerOfAttorneyFilter;
        }

        $conditions = $this->implodeFiltersWithOR($conditions);

        return " ($conditions) ";
    }

    private function parseAndCreatePowerOfAttorneyFilter($filter)
    {
        $matches = [];
        preg_match_all('/pa\(([\w\.]+)\)/', $filter, $matches);
        $columnNames = array_unique($matches[1]);

        foreach ($columnNames as $columnName) {
            $paFilter = $this->createPowerOfAttorneyFilter($columnName);
            $filter = str_replace("pa($columnName)", $paFilter, $filter);
        }

        return $filter;
    }

    public function getFiltersByName($name)
    {
        if (!isset($this->filters[$name]) && $name <> 'bagli_daxil_olan_senedler' && $name <> 'bagli_daxili') {
            throw new Exception(sprintf('Filter name "%s" is not defined', $name));
        }

        if ($name === 'bagli_daxil_olan_senedler' || $name === 'bagli_daxili') {
            $filter = $this->filters['butun_senedler'];
        } else {
            $filter = $this->filters[$name];
        }

        $filter = array_map(function ($filter) {
            return $this->implodeFiltersWithOR($filter);
        }, $filter);
        $filter = array_map(function ($filter) {
            return $this->parseAndCreatePowerOfAttorneyFilter($filter);
        }, $filter);
        $filter = $this->fillMissedFilters($filter);

        if ($name === 'bagli_daxil_olan_senedler') {
            $filter['document'] = sprintf(" ({$filter['document']}) AND document.status = %s AND (document.tip = 1 OR document.tip = 2) ",
                Document::STATUS_BAGLI
            );
            $filter['outgoing_document'] = " 1 <> 1 ";
        } else if ($name === 'bagli_daxili') {
            $filter['document'] = sprintf(" ({$filter['document']}) AND document.status = %s AND (document.tip = 3) ",
                Document::STATUS_BAGLI
            );
            $filter['outgoing_document'] = " 1 <> 1 ";
        } else if ($name !== 'butun_senedler') {
            if ($name === 'umumi_shobe') {
                $filter['document'] = "(1 = 1)";
            }
//            $filter['document'] = sprintf(" ({$filter['document']}) AND (document.state <> %s OR document.state IS NULL)",
//                Document::STATE_IN_TRASH
//            );
        } else if ($name === 'butun_senedler') {
            $filter['document'] = sprintf(" 
                ({$filter['document']}) AND (
                    ((document.state <> %s) OR document.state IS NULL) OR
                    (document.state = %s AND pa(document.created_by)) OR
                    (document.state = %s AND pa(document.created_by))
                )",
                Document::STATE_IN_TRASH,
                Document::STATE_CANCELED,
                Document::STATE_IN_TRASH
            );
            $filter['document'] = $this->parseAndCreatePowerOfAttorneyFilter($filter['document']);
        }
        $filter = array_map(function ($filter) {
            return " ($filter) ";
        }, $filter);

        return $filter;
    }
}