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

class SatinAlmaXidmet extends BaseEntity implements IBaseEntity
{
    public function getTableName()
    {
        return 'tb_prodoc_satinalma_sifaris_xidmet';
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    public static function create(array $data, User $user = null)
    {
        $taskCommandData = ArrayUtils::pick($data, [
            'malin_kodu',
            'mal_adi',
            'olcu_vahidi',
            'miqdar',
            'mebleq',
            'parent_id',
            'gun'
        ]);

        return parent::create($taskCommandData);
    }

    public function edit($data)
    {
        DB::update('tb_prodoc_satinalma_sifaris_xidmet', $data, $this->getId());
    }
}