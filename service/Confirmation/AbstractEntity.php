<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 16.04.2018
 * Time: 17:04
 * UNSUSED
 */
//namespace Service\Confirmation;
//
//use IConfirmable;
//use IBaseEntity;
//use BaseEntity;
//use DB;
//use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;
//use Model\DocumentNumber\DocumentNumberGeneral\IDocument;
//
//require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
//require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';
//require_once DIRNAME_INDEX . 'prodoc/model/IConfirmable.php';
//require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
//
//// TODO: the file should live in the model directory (with appropriate namespace)
//
//abstract class AbstractEntity extends BaseEntity
//{
//    public function onStatusChange($newStatus)
//    {
//        if ($this instanceof IBaseEntity && null !== $this->getStatusColumnName()) {
//            DB::update($this->getTableName(), [
//                $this->getStatusColumnName() => $newStatus
//            ], $this->getId());
//        }
//
//        if ($this instanceof IDocument && IConfirmable::STATUS_TESTIQLENIB === $newStatus) {
//            $documentNumberGeneral = new DocumentNumberGeneral($this);
//            $documentNumberGeneral->onDocumentApprove();
//        }
//    }
//
//    public function onApprove()
//    {
//
//    }
//
//    public function onFullApprove()
//    {
//
//    }
//
//    public function getApproveOrder(array $user)
//    {
//        return 1;
//    }
//
//    public function getStatusColumnName()
//    {
//        return 'status';
//    }
//}