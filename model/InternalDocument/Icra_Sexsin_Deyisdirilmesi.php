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
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';

use BaseEntity;
use DB;
use History\History;
use IBaseEntity;
use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;
use PowerOfAttorney\PowerOfAttorney;
use User;
use Util\ArrayUtils;

class IcraSexsinDeyisdirilmesi extends BaseEntity implements IBaseEntity
{




    public function getTableName()
    {
        return 'tb_icra_sexsin_deyisdirilmesi';
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    public static function create(array $data, User $user = null)
    {
        $dosRequestData = ArrayUtils::pick($data, [
            'qeyd',
            'yeni_icra_eden_sexs'
        ]);

        $dosData['type'] = 'icra_sexsin_deyisdirilmesi';
        $dosData['qeyd'] = $dosRequestData['qeyd'];
        $dosData['belong_to'] = $dosRequestData['yeni_icra_eden_sexs'];

        // insert to tb_daxil_olan_senedler
        $internalDoc = \InternalDocument::createNew($dosData);

        $icraShexsinDeyishdirilmesiData = ArrayUtils::pick($data, [
            'senedin_tarixi',
            'yeni_icra_eden_sexs',
            'qisa_mezmun',
            'qeyd',
            'related_document_id',
            // 'poa_user_id'
        ]);

        $icraShexsinDeyishdirilmesiData['document_id'] = $internalDoc->getId();
        $icraShexsinDeyishdirilmesiData['created_by'] = $user->getSessionUserId();
        $self = parent::create($icraShexsinDeyishdirilmesiData); // insert to tb_icra_sexsin_deyisdirilmesi

        $executors = [];


        if (array_key_exists('poa_user_id', $data) && (int)$data['poa_user_id']) {
            $executors[] = $data['poa_user_id'];
            unset($data['poa_user_id']);
        }

        insertFiles($internalDoc->getId());
        // creating POA

        $powerOfAttorney = new PowerOfAttorney($internalDoc,$user->getId(),$user);
        $history = new History($internalDoc);
        $history->create([
            'operation' => 'registration',
            'poa' => $powerOfAttorney->getPowerOfAttorneysByExecutors($executors)

        ]);

        $internalDoc->assignNumber($data);

        return $self;
    }

    public function edit($data,User $user = null)
    {
        DB::update('tb_icra_sexsin_deyisdirilmesi', $data, $this->getId());
        $internal = new \InternalDocument($this->getData()['document_id']);
        $history = new History($internal);
        $powerOfAttorney = new PowerOfAttorney($internal,$user->getId(),$user);

        $history->create([
            'operation' => 'document_edit',
            'poa' => $powerOfAttorney->getPowerOfAttorneysByExecutors($internal->getEditors())
        ]);
    }

    public function getHistoryKey(): string
    {
        return 'document';
    }
}