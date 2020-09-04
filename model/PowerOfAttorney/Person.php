<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 09.07.2018
 * Time: 9:03
 */

namespace PowerOfAttorney;

require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';

use DB;

class Person
{
    private $id;
    private $subordination;

    function __construct($id, $subordination = null)
    {
        $this->id = $id;
        $this->subordination = $subordination;
    }

    public function getPowerOfAttorneysAsProxy()
    {
        $sql = "
           SELECT
                pa.id,
                pa.from_user_id,
                pa.allowed_to_work_with_subordinate_users_docs,
                pa.created_at
           FROM tb_prodoc_power_of_attorney AS pa
           WHERE
           (to_user_id = {$this->id}) AND
           CAST(GETDATE() AS DATE) BETWEEN start_date AND ISNULL(end_date, GETDATE())
        ";

        $powerOfAttorneys = [];
        foreach (DB::fetchAll($sql) as $powerOfAttorney) {
            $powerOfAttorney['principals'] = [];
            $powerOfAttorney['principals'][] = $powerOfAttorney['from_user_id'];

            if (!is_null($this->subordination) && (int)$powerOfAttorney['allowed_to_work_with_subordinate_users_docs']) {
                $powerOfAttorney['principals'] = array_merge($this->subordination->getSubordinateEmployees(
                    $powerOfAttorney['from_user_id']
                ), $powerOfAttorney['principals']);
            }

            $powerOfAttorneys[$powerOfAttorney['id']] = $powerOfAttorney;
        }

        $this->setAllowedDocs($powerOfAttorneys);

        return $powerOfAttorneys;
    }

    public function setAllowedDocs(&$powerOfAttorneys)
    {
        $ids = array_keys($powerOfAttorneys);

        if (empty($ids)) {
            return;
        }

        $sql = sprintf("
            SELECT doc_type, power_of_attorney_id
            FROM tb_prodoc_power_of_attorney_allowed_doc
            WHERE power_of_attorney_id IN (%s)
        ", implode(',', $ids));

        foreach (DB::fetchAll($sql) as $allowedDocType) {
            $paId = $allowedDocType['power_of_attorney_id'];
            if (!isset($powerOfAttorneys[$paId]['allowed_docs'])) {
                $powerOfAttorneys[$paId]['allowed_docs'] = [];
            }

            $powerOfAttorneys[$paId]['allowed_docs'][] = $allowedDocType['doc_type'];
        }
    }
}