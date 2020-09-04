<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 20.04.2018
 * Time: 17:24
 */

class AppealType
{
    const INCOMING_DOCUMENT_STATUS_OPEN_ALL   = 1;
    const INCOMING_DOCUMENT_STATUS_CLOSE_ALL  = 2;
    const INCOMING_DOCUMENT_STATUS_SELECTABLE = 3;

    const TYPE_RELATED_DOCUMENT = 1;


    public static function getIncomingDocumentStatus($appealTypeId, $selectedStatus = NULL): int
    {
        $appealType = DB::fetchById('tb_prodoc_muraciet_tip', $appealTypeId);

        $incomingDocumentStatus = (int)$appealType['dos_status'];

        if ($incomingDocumentStatus === self::INCOMING_DOCUMENT_STATUS_SELECTABLE) {
            return $selectedStatus;
        }

        if ($incomingDocumentStatus === 0) {
            return self::INCOMING_DOCUMENT_STATUS_CLOSE_ALL;
        }

        return $incomingDocumentStatus;
    }

    public static function hasSelectableStatusType(array $appealTypes): bool
    {
        $appealTypes = array_map('intval', $appealTypes);
        $sql = sprintf("
            SELECT dos_status
            FROM tb_prodoc_muraciet_tip
            WHERE id IN (%s)
        ", implode(',', $appealTypes));

        $statuses = DB::fetchColumnArray($sql);
        $statuses = array_map('intval', $statuses);

        return in_array(self::INCOMING_DOCUMENT_STATUS_SELECTABLE, $statuses);
    }

    public  static  function hasSelectableConnectingType(array $appealTypes): bool
    {
        $appealTypes = array_map('intval', $appealTypes);
        $sql = sprintf("
            SELECT elaqelendirme
            FROM tb_prodoc_muraciet_tip
            WHERE id IN (%s)
        ", implode(',', $appealTypes));

        $types = DB::fetchColumnArray($sql);
        $types = array_map('intval', $types);

        return in_array(self::TYPE_RELATED_DOCUMENT, $types);
    }

}