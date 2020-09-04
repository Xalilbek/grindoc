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

use BaseEntity;
use IBaseEntity;
use DB;
use User;
use Util\ArrayUtils;
use Util\Date;

class ElaveRazilashdirma extends BaseEntity implements IBaseEntity
{
    public function getTableName()
    {
        return 'tb_prodoc_elave_razilashdirma';
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    public static function create(array $data, User $user = null)
    {
        $taskCommandData = ArrayUtils::pick($data, [
            'senedin_tarixi',
            'qisa_mezmun',
            'qeyd',
            'document_id',
            'tesdiqleme_geden_userler',
            'imtinaSebebi',
            'imtinaEden',
            'rehberler',
            'status',
            'created_by',
            'emekdash',
            'muqavile',
            'elave_razilashdirnama_nomresi',
            'emeqhaqqina_elave',
            'emeqhaqqina_elave_valyuta',
            'ezamiyyet_muddeti',
            'sv_pin_kodu',
            'sv_nomresi',
            'sv_teqdim_eden_orqan',
            'emeqhaqqina_elave_valyuta',
            'unvan',
            'sened_tip'
        ]);

        $taskCommandData['created_by'] = $user->getSessionUserId();

        return parent::create($taskCommandData);
    }

    public function edit($data)
    {
        DB::update('tb_prodoc_elave_razilashdirma', $data, $this->getId());
    }
}