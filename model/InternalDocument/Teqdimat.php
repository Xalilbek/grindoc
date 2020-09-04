<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 05.07.2018
 * Time: 12:16
 * Salam
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

class Teqdimat extends BaseEntity implements IBaseEntity
{
    public function getTableName()
    {
        return 'tb_prodoc_teqdimat';
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    public static function create(array $data, User $user = null)
    {
        $taskCommandData = ArrayUtils::pick($data, [
            'kim',
            'kime',
            'qisa_mezmun',
            'melumat_metni',
            'tesdiqleme_geden_userler',
            'imtinaSebebi',
            'imtinaEden',
            'rehberler',
            'status',
            'created_by',
        ]);

        $taskCommandData['created_by'] = $user->getSessionUserId();

        return parent::create($taskCommandData);
    }

    public function edit($data)
    {
        DB::update('tb_prodoc_teqdimat', $data, $this->getId());
    }
}