<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 05.07.2018
 * Time: 12:16
 */
namespace PowerOfAttorney;

require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorneyException.php';

use BaseEntity;
use IBaseEntity;
use DB;
use User;
use Util\ArrayUtils;
use Util\Date;

class Setting extends BaseEntity implements IBaseEntity
{
    const NEXT_DOCS = 1;
    const ALL_OPENED_DOCS = 2;
    const PREVIOUS_CLOSED_DOCS = 3;

    public function getTableName()
    {
        return 'tb_prodoc_power_of_attorney';
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    private static function checkPOAExistence($data, $id = null)
    {
        $currentDocumentSQLFilter = '';
        if ((int)$id) {
            $currentDocumentSQLFilter = " AND pa.id <> $id ";
        }

        if (is_null($data['end_date'])) {
            $sql = "
                SELECT TOP 1
                    pa.id,
                    pa.start_date,
                    pa.end_date,
                    doc_num.document_number
                FROM tb_prodoc_power_of_attorney AS pa
                LEFT JOIN tb_daxil_olan_senedler AS doc
                 ON doc.id = pa.document_id
                LEFT JOIN tb_prodoc_document_number AS doc_num
                 ON doc.document_number_id = doc_num.id
                WHERE pa.from_user_id = {$data['to_user_id']} AND pa.to_user_id = {$data['from_user_id']}
                $currentDocumentSQLFilter
            ";
        } else {
            $sql = "
                SELECT TOP 1
                    pa.id,
                    pa.start_date,
                    pa.end_date,
                    doc_num.document_number
                FROM tb_prodoc_power_of_attorney AS pa
                LEFT JOIN tb_daxil_olan_senedler AS doc
                 ON doc.id = pa.document_id
                LEFT JOIN tb_prodoc_document_number AS doc_num
                 ON doc.document_number_id = doc_num.id
                WHERE pa.from_user_id = {$data['to_user_id']} AND pa.to_user_id = {$data['from_user_id']}
                AND (
                        (
                            pa.end_date IS NOT NULL AND (
                                (
                                    '{$data['start_date']}' >= pa.start_date AND
                                    '{$data['start_date']}' <= pa.end_date
                                ) OR
                                (
                                    '{$data['end_date']}'   >= pa.start_date AND
                                    '{$data['end_date']}'   <= pa.end_date
                                ) OR
                                (
                                    pa.start_date >= '{$data['start_date']}' AND
                                    pa.start_date <= '{$data['end_date']}'
                                ) OR
                                (
                                    pa.end_date >= '{$data['start_date']}' AND
                                    pa.end_date <= '{$data['end_date']}'
                                )
                            )
                        )
               )
                $currentDocumentSQLFilter
            ";
        }



//        if (is_null($data['end_date'])) {
//            $sql = "
//                SELECT TOP 1
//                    pa.id,
//                    pa.start_date,
//                    pa.end_date,
//                    doc_num.document_number
//                FROM tb_prodoc_power_of_attorney AS pa
//                LEFT JOIN tb_daxil_olan_senedler AS doc
//                 ON doc.id = pa.document_id
//                LEFT JOIN tb_prodoc_document_number AS doc_num
//                 ON doc.document_number_id = doc_num.id
//                WHERE ( pa.from_user_id = {$data['from_user_id']} OR pa.to_user_id = {$data['from_user_id']} OR pa.from_user_id = {$data['to_user_id']}  OR pa.to_user_id = {$data['to_user_id']} )
//                AND (
//                        (
//                            pa.end_date IS NOT NULL AND (
//                                (
//                                    '{$data['start_date']}' <= pa.end_date
//                                )
//                            )
//                        )
//                ) $currentDocumentSQLFilter
//            ";
////            OR pa.end_date IS NULL
//        } else {
//            $sql = "
//                SELECT TOP 1
//                    pa.id,
//                    pa.start_date,
//                    pa.end_date,
//                    doc_num.document_number
//                FROM tb_prodoc_power_of_attorney AS pa
//                LEFT JOIN tb_daxil_olan_senedler AS doc
//                 ON doc.id = pa.document_id
//                LEFT JOIN tb_prodoc_document_number AS doc_num
//                 ON doc.document_number_id = doc_num.id
//                WHERE ( pa.from_user_id = {$data['from_user_id']} OR pa.to_user_id = {$data['from_user_id']} OR pa.from_user_id = {$data['to_user_id']}  OR pa.to_user_id = {$data['to_user_id']} )
//                AND (
//                        (
//                            pa.end_date IS NOT NULL AND (
//                                (
//                                    '{$data['start_date']}' >= pa.start_date AND
//                                    '{$data['start_date']}' <= pa.end_date
//                                ) OR
//                                (
//                                    '{$data['end_date']}'   >= pa.start_date AND
//                                    '{$data['end_date']}'   <= pa.end_date
//                                ) OR
//                                (
//                                    pa.start_date >= '{$data['start_date']}' AND
//                                    pa.start_date <= '{$data['end_date']}'
//                                ) OR
//                                (
//                                    pa.end_date >= '{$data['start_date']}' AND
//                                    pa.end_date <= '{$data['end_date']}'
//                                )
//                            )
//                        )
//                ) $currentDocumentSQLFilter
//            ";
//
////            OR (
////                            pa.end_date IS NULL AND
////                            '{$data['end_date']}' >= pa.start_date
////                        )
//
//        }

        $existingPowerOfAttorney = DB::fetch($sql);

        if ($existingPowerOfAttorney !== FALSE) {
            throw new PowerOfAttorneyException(sprintf(
                "
                    Bu dövr üçün etibarnamə daha öncə əlavə olunub
                    Sənədin nömrəsi: %s
                    Dövrün başlaması: %s
                    Dövrün bitməsi: %s
                ",
                $existingPowerOfAttorney['document_number'],
                Date::formatDate($existingPowerOfAttorney['start_date']),
                is_null($existingPowerOfAttorney['end_date']) ?
                    'Birdəfəlik' :
                    Date::formatDate($existingPowerOfAttorney['end_date'])
            ));
        }
    }

    public static function create(array $data, User $user = null)
    {
//        self::hasPowerOfAttorney($data);

        if ($data['from_user_id'] === $data['to_user_id']) {
            throw new PowerOfAttorneyException('Eyni şəxsləri seçə bilmərsiz');
        }

        if(!is_null($data['end_date'])){

            self::checkPOAExistence($data);
        }

        $self = parent::create(ArrayUtils::omit($data, ['allowed_docs']));

        for ($i = 0, $len = count($data['allowed_docs']); $i < $len; ++$i) {
            DB::insert('tb_prodoc_power_of_attorney_allowed_doc', [
                'doc_type' => $data['allowed_docs'][$i],
                'power_of_attorney_id' => $self->getId()
            ]);
        }

        return $self;
    }

    public function hasPowerOfAttorney($data){

        if(is_null($data['end_date'])){
            $sql=" SELECT  CONCAT(Adi, ' ', Soyadi)  FROM tb_prodoc_power_of_attorney LEFT JOIN v_users ON to_user_id = USERID
                                    WHERE to_user_id = ".$data['to_user_id']." 
                                    AND (start_date = ".$data['start_date']." OR start_date < ".$data['start_date']." ) ";
        }else{
            $sql=" SELECT  CONCAT(Adi, ' ', Soyadi)  FROM tb_prodoc_power_of_attorney LEFT JOIN v_users ON to_user_id = USERID
                                    WHERE to_user_id = ".$data['to_user_id']." 
                                    AND (
                                    
                                    (start_date = '".$data['start_date']."' OR start_date < '".$data['start_date']."' ) OR ( end_date = '".$data['end_date']."' OR  end_date > '".$data['end_date']."' )
                                     
                                     
                                     )  ";
        }



        $has_power_of_attorney = DB::fetchColumn($sql);

        if ($has_power_of_attorney){
            throw new PowerOfAttorneyException('Seçilmiş tarixdə '.$has_power_of_attorney.' adına artıq vəkalətnamə mövcuddur. ');

        }



    }

    public function edit($data)
    {
        if ($data['from_user_id'] === $data['to_user_id']) {
            throw new PowerOfAttorneyException('Eyni şəxsləri seçə bilmərsiz');
        }

        self::checkPOAExistence($data, $this->getId());

        DB::update('tb_prodoc_power_of_attorney', ArrayUtils::omit($data, ['allowed_docs']), $this->getId());

        $sql = sprintf("
            DELETE FROM tb_prodoc_power_of_attorney_allowed_doc
            WHERE power_of_attorney_id = %s
        ", $this->getId());
        DB::exec($sql);

        for ($i = 0, $len = count($data['allowed_docs']); $i < $len; ++$i) {
            DB::insert('tb_prodoc_power_of_attorney_allowed_doc', [
                'doc_type' => $data['allowed_docs'][$i],
                'power_of_attorney_id' => $this->getId()
            ]);
        }
    }
}