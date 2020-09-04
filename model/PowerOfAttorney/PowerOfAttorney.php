<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 05.07.2018
 * Time: 15:05
 */

namespace PowerOfAttorney;
require_once DIRNAME_INDEX . 'prodoc/model/Exception/NotAllowedOperationException.php';

require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/Setting.php';

use DB;

class PowerOfAttorney
{
    const OPENED_DOC = 1;
    const CLOSED_DOC = 2;

    private $powerOfAttorneyDocument;
    private $userId;
    private $cache;
    private $subordination;

    public function __construct(
        IPowerOfAttorneyDocument $powerOfAttorneyDocument,
        int $userId,
        $subordination = null
    )
    {
        $this->powerOfAttorneyDocument = $powerOfAttorneyDocument;
        $this->userId = $userId;
        $this->subordination = $subordination;
    }

    private function getDocumentSQLFilter()
    {
        $documentDate   = $this->powerOfAttorneyDocument->getDate()->format('Y-m-d H:i');
        $documentStatus = $this->powerOfAttorneyDocument->getStatus();

        $nextDocs       = Setting::NEXT_DOCS;
        $allOpenedDocs  = Setting::ALL_OPENED_DOCS;
        $prevClosedDocs = Setting::PREVIOUS_CLOSED_DOCS;

        $statusOpenedDoc = self::OPENED_DOC;
        $statusClosedDoc = self::CLOSED_DOC;

        return "
            (
                SELECT TOP 1 id
                FROM tb_prodoc_power_of_attorney_allowed_doc AS allowed_doc
                WHERE
                pa.id = allowed_doc.power_of_attorney_id AND (
                    (allowed_doc.doc_type = $nextDocs       AND pa.created_at < '$documentDate') OR
                    (allowed_doc.doc_type = $allOpenedDocs  AND $documentStatus = $statusOpenedDoc) OR
                    (allowed_doc.doc_type = $prevClosedDocs AND $documentStatus = $statusClosedDoc AND pa.created_at >= '$documentDate')
                ) 
            ) IS NOT NULL
        ";
    }

    public function getPowerOfAttorneys()
    {
        if (isset($this->cache['power_of_attorneys'])) {
            return $this->cache['power_of_attorneys'];
        }

        $userId = $this->userId;
        $documentSQLFilter = $this->getDocumentSQLFilter();

        $sql = "
            SELECT
                pa.id,
                pa.from_user_id,
                pa.to_user_id,
                pa.parallelism,
                pa.allowed_to_work_with_subordinate_users_docs
            FROM tb_prodoc_power_of_attorney AS pa
            WHERE
            (to_user_id = $userId OR from_user_id = $userId) AND
            CAST(GETDATE() AS DATE) BETWEEN start_date AND ISNULL(end_date, GETDATE()) AND
            $documentSQLFilter
        ";

        return $this->cache['power_of_attorneys'] = DB::fetchAll($sql);
    }

    public function getPowerOfAttorneysAsDirectPrincipal(
        array $principals = null
    )
    {
        if (is_array($principals) && count($principals) === 0) {
            return [];
        }

        if (is_null($principals)) {
            $fromUserIsSQLFilter = "";
        } else {
            $principalsSQLList = implode(',', $principals);
            $fromUserIsSQLFilter = "(from_user_id IN ($principalsSQLList)) AND";
        }

        $documentSQLFilter = $this->getDocumentSQLFilter();

        $sql = "
            SELECT
                pa.document_id,
                pa.from_user_id,
                pa.to_user_id,
                pa.parallelism,
                pa.allowed_to_work_with_subordinate_users_docs,
                document_number.document_number
            FROM tb_prodoc_power_of_attorney AS pa
            LEFT JOIN tb_daxil_olan_senedler AS document
             ON pa.document_id = document.id
            LEFT JOIN v_prodoc_document_number document_number
             ON document_number.id = document.document_number_id
            WHERE
            $fromUserIsSQLFilter
            CAST(GETDATE() AS DATE) BETWEEN start_date AND ISNULL(end_date, GETDATE()) AND $documentSQLFilter
        ";

        return DB::fetchAllIndexed($sql, 'from_user_id');
    }

    public function getAllowedExecutors($withPOA = false)
    {
        if (isset($this->cache['executors'])) {
            if ($withPOA) {
                return $this->cache['executors'];
            } else {
                return array_column($this->cache['executors'], 'userId');
            }
        }

        $executors = [];

        $powerOfAttorneys = $this->getPowerOfAttorneys();

        if (empty($powerOfAttorneys)) {
            $executors[] = ['userId' => $this->userId, 'poa' => null];
        }

        foreach ($powerOfAttorneys as $powerOfAttorney) {
            if ($this->isPrincipal($powerOfAttorney)) {
                // TODO
                // it's a bug
                // remove from cache (above) and check based on users passed
                if ((int)$powerOfAttorney['parallelism'] === 1) {
                    $executors[] = ['userId' => $this->userId, 'poa' => $powerOfAttorney];
                }
            } else if ($this->isProxy($powerOfAttorney)) {
                $principal = $this->getPrincipal($powerOfAttorney);

                $executors[] = ['userId' => $this->userId, 'poa' => $powerOfAttorney];
                $executors[] = ['userId' => $principal,    'poa' => $powerOfAttorney];

                if ((int)$powerOfAttorney['allowed_to_work_with_subordinate_users_docs']) {
                    foreach ($this->getSubordinatesOfPrincipal($principal) as $principalSubordinate) {
                        $executors[] = ['userId' => $principalSubordinate, 'poa' => $powerOfAttorney];
                    }
                }
            }
        }

        $this->cache['executors'] = $executors;

        if ($withPOA) {
            return $executors;
        } else {
            return array_column($executors, 'userId');
        }
    }

    public function canExecute($executors, $returnExecutorId = false)
    {
        $allowedExecutors = $this->getAllowedExecutors();

        if (is_int($executors) || is_string($executors)) {
            $executors = [(int)$executors];
        }

        foreach ($executors as $executorUserId) {
            if (in_array($executorUserId, $allowedExecutors)) {
                if ($returnExecutorId) {
                    return $executorUserId;
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    public function getPowerOfAttorneysByExecutors($executors)
    {
        $allowedExecutors = $this->getAllowedExecutors(true);

        if (is_int($executors) || is_string($executors)) {
            $executors = [(int)$executors];
        }
            foreach ($executors as $executor){
                if($executor==$this->userId)
                    return array();
            }



        $allowedExecutorsLength = count($allowedExecutors);
        $poas = [];
        foreach ($executors as $executorUserId) {
            for ($i = 0; $i < $allowedExecutorsLength; ++$i) {
                if ((int)$executorUserId === (int)$allowedExecutors[$i]['userId']) {
                    $poas[] = $allowedExecutors[$i]['poa'];
                }
            }
        }

        return $poas;
    }

    public function getSubordinatesOfPrincipal($principal)
    {
        $subordinates = [];

        if (!is_null($this->subordination)) {
            $subordinates = $this->subordination->getSubordinateEmployees($principal);
        }

        return $subordinates;
    }

    public function getPrincipal($powerOfAttorney)
    {
        return (int)$powerOfAttorney['from_user_id'];
    }

    public function isPrincipal($powerOfAttorney)
    {
        return (int)$powerOfAttorney['from_user_id'] === (int)$this->userId;
    }

    public function isProxy($powerOfAttorney)
    {
        return (int)$powerOfAttorney['to_user_id'] === (int)$this->userId;
    }
}