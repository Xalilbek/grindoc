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
require_once DIRNAME_INDEX . 'prodoc/service/History/IHistory.php';
require_once DIRNAME_INDEX . 'prodoc/service/History/History.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/IPowerOfAttorneyDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
use BaseEntity;
use IBaseEntity;
use DB;
use User;
use Util\ArrayUtils;
use Util\Date;
use History\History;
use PowerOfAttorney\PowerOfAttorney;


class UmumiForma extends BaseEntity implements IBaseEntity
{

    protected $history;
    protected $powerOfAttorney;
    protected $document;




    public function getTableName()
    {
        return 'tb_umumi_forma';
    }

    public static function getIsDeletedColumnName()
    {
        return NULL;
    }

    public function getHistoryKey(): string
    {
        return 'document';
    }

    public function getStatus()
    {

        return is_null((int)$this->document->getData()['status'])?1:(int)$this->document->getData()['status'];
    }



    public function getDate(): \DateTime
    {


        return new \DateTime($this->document->getData()['created_at']);
    }

    public static function create(array $data, User $user = null)
    {

        $dosRequestData = ArrayUtils::pick($data, [
            'qeyd'
        ]);

        $dosData['type'] = 'umumi_forma';
        $dosData['qeyd'] = $dosRequestData['qeyd'];

        // insert to tb_daxil_olan_senedler
        $internalDoc = \InternalDocument::createNew($dosData);


        $taskCommandData = ArrayUtils::pick($data, [
            'senedin_tarixi',
            'sened_novu',
            'qisa_mezmun',
            'qeyd',
            'imzali',
            'icra_edilme_tarixi'
        ]);
        $taskCommandData['document_id'] = $internalDoc->getId();
        $taskCommandData['created_by'] = $user->getSessionUserId();

        $executors = [];


        if (array_key_exists('poa_user_id', $data) && (int)$data['poa_user_id']) {
            $executors[] = $data['poa_user_id'];
            unset($data['poa_user_id']);
        }
        $self = parent::create($taskCommandData);

        $powerOfAttorney = new PowerOfAttorney($internalDoc,$user->getId(),$user);
        $history = new History($internalDoc);
        $history->create([
            'operation' => 'registration',
            'poa' => $powerOfAttorney->getPowerOfAttorneysByExecutors($executors)

        ]);

        $internalDoc->assignNumber($data);

        return $self;
    }



    public function edit($data, User $user = null)
    {
        DB::update('tb_umumi_forma', $data, $this->getId());

        $internal = new \InternalDocument($this->getData()['document_id']);
        $history = new History($internal);
        $powerOfAttorney = new PowerOfAttorney($internal,$user->getId(),$user);

        $history->create([
            'operation' => 'document_edit',
            'poa' => $powerOfAttorney->getPowerOfAttorneysByExecutors($internal->getEditors())
        ]);

    }
}