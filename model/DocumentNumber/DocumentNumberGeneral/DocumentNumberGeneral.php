<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 15.05.2018
 * Time: 14:23
 */

namespace Model\DocumentNumber\DocumentNumberGeneral;

use DB;
use Exception;
use Service\Option\Option;

require_once 'Setting.php';

class DocumentNumberGeneral
{
    const STATUS_NUMBER_NOT_APPROVED = 0;
    const STATUS_NUMBER_APPROVED = 1;

    const RESERVATION_STATUS_FREE = 1;
    const RESERVATION_STATUS_RESERVED = 2;
    const RESERVATION_STATUS_ASSIGNED = 3;

    const DEFAULT_RESERVATION_PERIOD_MINUTES = 180;

    const INITIAL_ADDITIONAL_SERIAL_NUMBER = 2;

    private $document;
    private $currentYear;
    private $setting;
    private $additionalData;

    public function __construct(IDocument $document, $additionalData = [])
    {
        $this->document = $document;
        $this->currentYear = date('Y');
        $this->additionalData = $additionalData;
        $this->userId = $_SESSION['erpuserid'];
    }

    public function getSetting()
    {
        if (!is_null($this->setting)) {
            return $this->setting;
        }

        $correctedSettingSubquery = Setting::getCorrectedSettingSubquery();

        $sql = "
            SELECT TOP 1 setting.*
            FROM ($correctedSettingSubquery) AS setting
            LEFT JOIN tb_prodoc_document_number_pattern_document_type AS doc_type
             ON setting.id = doc_type.document_number_patter_id
            WHERE
            (setting.direction = '%s') AND
            (setting.option_id =  %s        OR setting.option_id         = -1) AND
            (setting.option_value_id = %s   OR setting.option_value_id   = -1) AND
            (doc_type.document_type_id = %s OR doc_type.document_type_id = -1) AND
            GETDATE() >= setting.active_from
            ORDER BY
            doc_type.document_type_id    DESC,
            setting.option_value_id      DESC,
            setting.option_id            DESC
        ";

        $settingRecord = DB::fetch(sprintf($sql,
            $this->document->getDirection(),
            $this->document->getOption(),
            $this->document->getOptionValue(),
            $this->document->getDocumentType()
        ), true, \PDO::FETCH_ASSOC);

        if (false === $settingRecord) {
            throw new Exception('Nömrələnmə tənzimlənməyib!');
        }

        $this->setting = new Setting($settingRecord['id'], [
            'info' => $settingRecord
        ]);

        return $this->setting;
    }

    /**
     * @param Setting|null $setting
     * @return int|bool
     * @throws Exception
     */
    public function getLastSerialNumber(Setting $setting = null)
    {
        if (is_null($setting)) {
            $setting = $this->getSetting();
        }

        if (false === $setting) {
            throw new Exception('Document number setting is not configured!');
        }

        $senderPersonFirstSurnameLetterFilter = "";
        if ($this->document instanceof IIncomingDocument) {
            $senderPersonFirstSurnameLetter = $this->document->getSenderPersonFirstSurnameLetter('serial_number');

            if (is_string($senderPersonFirstSurnameLetter) && strlen($senderPersonFirstSurnameLetter) > 0) {
                $senderPersonFirstSurnameLetterFilter = sprintf(" AND sender_person_surname_first_letter = N'%s' ",
                    $senderPersonFirstSurnameLetter
                );
            }
        }

        $sql = "SELECT MAX(serial_number)
                FROM tb_prodoc_document_number
                WHERE document_number_pattern_id = %s AND year = %s {$senderPersonFirstSurnameLetterFilter}";
        $lastSerialNumber = DB::fetchColumn(sprintf($sql, $setting->getId(), $this->currentYear));

        if (null === $lastSerialNumber) {
            return null;
        }

        return (int)$lastSerialNumber;
    }

    /**
     * @param Setting|null $setting
     * @return int
     * @throws Exception
     */
    public function getCurrentSerialNumber(Setting $setting = null): int
    {
        if (is_null($setting)) {
            $setting = $this->getSetting();
        }

        if (false === $setting) {
            throw new Exception('Document number setting is not configured!');
        }

        $lastSerialNumber = $this->getLastSerialNumber($setting);

        if (null === $lastSerialNumber) {
            $initialNumber = $this->documentExists($setting) ?
                $setting->record['initial_number_for_next_years'] :
                $setting->record['initial_number'];

            if (is_null($initialNumber)) {
                $currentSerialNumber = 1;
            } else {
                $currentSerialNumber = (int)$initialNumber;
            }
        } else {
            $currentSerialNumber = $lastSerialNumber + 1;
        }

        return $currentSerialNumber;
    }

    private function documentExists(Setting $setting): bool
    {
        return false !== DB::fetchOneBy('tb_prodoc_document_number', [
           'document_number_pattern_id' => $setting->getId()
        ]);
    }

    public function isNumberEditable()
    {
        $setting = $this->getSetting();

        $isNumberEditable = (int)$setting->getData()['editable'];

        return !!$isNumberEditable;
    }

    public function isNumberEditableWithSelect()
    {
        $setting = $this->getSetting();

        $isNumberEditableWithSelect = (int)$setting->getData()['editable_with_select'];

        return !!$isNumberEditableWithSelect;
    }

    public function getCurrentDocumentNumber($returnExtraData = false, $checkEditability = true)
    {
        $setting = $this->getSetting();

        $isNumberEditable = (int)$setting->getData()['editable'];
        $isNumberEditableWithSelect = (int)$setting->getData()['editable_with_select'];
        $repeatAppeal     = (int)$setting->getData()['repeat_appeal'];

        $additionalSerialNumber = null;
        $senderPerson = null;
        $documentNumber = null;
        $serialNumber= null;
        $senderPersonSurnameFirstLetter = NULL;

        if (!isset($this->additionalData['editable_with_select'])) {
            $this->additionalData['editable_with_select'] = false;
        }

        if ($checkEditability && ($isNumberEditable || ($isNumberEditableWithSelect && (int)$this->additionalData['editable_with_select']))) {
            $serialNumber = null;
            $documentNumber = isset($this->additionalData['manualDocumentNumber']) ? $this->additionalData['manualDocumentNumber'] : '';
        } else {

            if ($this->document instanceof IIncomingDocument) {
                $senderPerson = (int)$this->document->getSenderPerson();

                if ($repeatAppeal && $senderPerson) {
                    $currentYear = $this->currentYear;
                    $sql = sprintf("
                        SELECT TOP 1 document_number, serial_number
                        FROM tb_prodoc_document_number
                        WHERE
                        sender_person_id = '$senderPerson' AND
                        year = '$currentYear' AND
                        additional_serial_number IS NULL
                        ORDER BY created_at ASC
                    ", $this->currentYear);

                    $senderPersonFirstDocumentNumber = DB::fetch($sql);

                    if (false !== $senderPersonFirstDocumentNumber) {
                        $sql = "SELECT MAX(additional_serial_number)
                            FROM tb_prodoc_document_number
                            WHERE sender_person_id = '$senderPerson' AND
                            year = $currentYear AND
                            additional_serial_number IS NOT NULL
                            ";
                        $lastAdditionalSerialNumber = DB::fetchColumn($sql);

                        if (is_null($lastAdditionalSerialNumber)) {
                            $additionalSerialNumber = self::INITIAL_ADDITIONAL_SERIAL_NUMBER;
                        } else {
                            $additionalSerialNumber = $lastAdditionalSerialNumber + 1;
                        }

                        $documentNumber = $senderPersonFirstDocumentNumber['document_number'] . " / " . $additionalSerialNumber;
                        $serialNumber = $senderPersonFirstDocumentNumber['serial_number'];
                    }
                }

                $senderPersonSurnameFirstLetter = $this->document->getSenderPersonFirstSurnameLetter('serial_number');
            }

            if (is_null($documentNumber)) {
                $serialNumber = $this->getCurrentSerialNumber($setting);
                $pattern = $setting->record['pattern_prefix'] . $setting->record['pattern'];

                $cityIndex       = '-';
                $departmentIndex = '-';

                if ($this->document instanceof IOutgoingDocument) {
                    $cityIndex       = $this->document->getCityIndex();
                    $departmentIndex = $this->document->getDepartmentIndex();
                }

                $spFirstSurnameLetter = '-';
                if ($this->document instanceof IIncomingDocument) {
                    $spFirstSurnameLetter = $this->document->getSenderPersonFirstSurnameLetter();
                }

                $documentNumber = str_replace(
                    ['$seria$', '$il$', '$seher$', '$shobe$', '$soyad$'],
                    [$serialNumber, $this->currentYear, $cityIndex, $departmentIndex, $spFirstSurnameLetter],
                    $pattern
                );
            }

        }

        if ($returnExtraData) {
            return [
                'documentNumber'   => $documentNumber,
                'serialNumber'     => $serialNumber,
                'parent_id'        => null,
                'sender_person_id' => $senderPerson,
                'additionalSerialNumber' => $additionalSerialNumber,
                'sender_person_surname_first_letter' => $senderPersonSurnameFirstLetter
            ];
        } else {
            return $documentNumber;
        }
    }

    private function checkNumberForRepeat($num)
    {
        $repeatNum = DB::fetchOneBy('tb_prodoc_document_number', [
            'document_number' => $num
        ]);

        if (FALSE !== $repeatNum) {
            throw new Exception('Bu nömrəli sənəd artıq qeydiyyatdan keçib');
        }
    }

    private function saveDocumentNumber($data)
    {
        $setting = $this->getSetting();

        if (!array_key_exists('reserved_by', $data)) {
            $data['reserved_by'] = null;
        }

        $documentNumberData = [
            'year' => $this->currentYear,
            'set_number_after_approval'  => $setting->record['set_number_after_approval'],
            'document_number_pattern_id' => $setting->getId(),
            'serial_number'      => $data['serialNumber'],
            'sender_person_id'   => $data['sender_person_id'],
            'parent_id'          => $data['parent_id'],
            'document_number'    => $data['documentNumber'],
            'additional_serial_number'           => $data['additionalSerialNumber'],
            'sender_person_surname_first_letter' => $data['sender_person_surname_first_letter'],
            'reservation_status' => $data['reservation_status'],
            'created_by' => $this->userId,
            'reserved_by' => $data['reserved_by'],
            'reserved_at' => $data['reserved_at'],
        ];

        // the code smells bad :(
        // turning off standard approving feature
        if (getProjectName() !== TS) {
            if (1 === (int)$setting->record['set_number_after_approval']) {
                $documentNumberData['approved'] = self::STATUS_NUMBER_NOT_APPROVED;
            }
        }

        return (int)DB::insertAndReturnId(
            'tb_prodoc_document_number',
            $documentNumberData,
            ['reserved_at']
        );
    }

    public function getExistingFreeNumber()
    {
        $setting = $this->getSetting();

        $senderPersonFirstSurnameLetterFilter = "";
        if ($this->document instanceof IIncomingDocument) {
            $senderPersonFirstSurnameLetter = $this->document->getSenderPersonFirstSurnameLetter('serial_number');

            if (is_string($senderPersonFirstSurnameLetter) && strlen($senderPersonFirstSurnameLetter) > 0) {
                $senderPersonFirstSurnameLetterFilter = sprintf(" AND sender_person_surname_first_letter = N'%s' ",
                    $senderPersonFirstSurnameLetter
                );
            }
        }

        $reservationPeriod = (int)Option::getOrCreateValue('brond_muddet', self::DEFAULT_RESERVATION_PERIOD_MINUTES);

        $sql = sprintf("
                    SELECT
                        TOP 1
                        id,
                        document_number AS documentNumber,
                        serial_number AS serialNumber,
                        parent_id,
                        sender_person_id,
                        additional_serial_number AS additionalSerialNumber,
                        sender_person_surname_first_letter
                    FROM tb_prodoc_document_number
                    WHERE
                    document_number_pattern_id = %s AND 
                    year = %s
                    {$senderPersonFirstSurnameLetterFilter} AND
                    (
                        reservation_status = %s OR
                        (
                            reservation_status = %s AND
                            DATEDIFF(MINUTE, reserved_at, GETDATE()) > $reservationPeriod
                        )
                    )
                    ORDER BY created_at ASC
                ",
                $setting->getId(),
                $this->currentYear,
                self::RESERVATION_STATUS_FREE,
                self::RESERVATION_STATUS_RESERVED
        );

        $existingFreeNumber = DB::fetch($sql);

        if (false === $existingFreeNumber) {
            return null;
        }

        return $existingFreeNumber;
    }

    public function reserveNumber()
    {
        try {
            $notInTransaction = !DB::inTransaction();

            if ($notInTransaction) {
                DB::exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
                DB::beginTransaction();
            }

            $data = $this->getExistingFreeNumber();

            if (null !== $data) {
                DB::update('tb_prodoc_document_number', [
                    'reservation_status' => self::RESERVATION_STATUS_RESERVED,
                    'reserved_by' => $this->userId,
                    'reserved_at' => 'GETDATE()'
                ], $data['id'], 'id', ['reserved_at']);
            } else {
                $data = $this->getCurrentDocumentNumber(true, false);

                $data['reservation_status'] = self::RESERVATION_STATUS_RESERVED;
                $data['reserved_by'] = $this->userId;
                $data['reserved_at'] = 'GETDATE()';
                $data['id'] = $this->saveDocumentNumber($data);
            }

            if ($notInTransaction) {
                DB::commit();
            }

            return $data;
        } catch (Exception $exception) {
            if ($notInTransaction) {
                DB::rollBack();
            }

            throw $exception;
        }
    }

    public function assignReservedNumber($documentNumberId)
    {
        try {
            $notInTransaction = !DB::inTransaction();

            if ($notInTransaction) {
                DB::exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
                DB::beginTransaction();
            }

            $reservedDocNum = DB::fetchById('tb_prodoc_document_number', $documentNumberId);

            if (false === $reservedDocNum) {
                throw new Exception('Bron etdiyiniz nömrə mövüd deyil');
            }

            $canReserve = true;
//                (int)$reservedDocNum['reserved_by'] === (int)$_SESSION['erpuserid'] &&
//                (int)$reservedDocNum['reservation_status'] === DocumentNumberGeneral::RESERVATION_STATUS_RESERVED;

            if (false === $canReserve) {
                throw new Exception('Access error');
            }

            DB::update('tb_prodoc_document_number', [
                'reservation_status' => self::RESERVATION_STATUS_ASSIGNED
            ], $documentNumberId);

            $relatedTableData = [];
            $relatedTableData['document_number_id'] = $documentNumberId;

            DB::update($this->document->getTableName(), $relatedTableData, $this->document->getId());

            if ($notInTransaction) {
                DB::commit();
            }

            if (method_exists($this->document, 'onNumberAcquire')) {
                $this->document->onNumberAcquire();
            }

        } catch (Exception $exception) {
            if ($notInTransaction) {
                DB::rollBack();
            }

            throw $exception;
        }
    }

    public function assignNumber($checkEditability = true): int
    {
        try {
            $notInTransaction = !DB::inTransaction();

            if ($notInTransaction) {
                DB::exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
                DB::beginTransaction();
            }

            $data = $this->getExistingFreeNumber();

            if (null !== $data) {
                DB::update('tb_prodoc_document_number', [
                    'reservation_status' => self::RESERVATION_STATUS_ASSIGNED
                ], $data['id']);
            } else {
                $data = $this->getCurrentDocumentNumber(true, $checkEditability);

                if (is_null($data['serialNumber'])) {
                    $this->checkNumberForRepeat($data['documentNumber']);
                }

                $data['reservation_status'] = NULL;
                $data['reserved_by'] = NULL;
                $data['reserved_at'] = NULL;

                $data['id'] = $this->saveDocumentNumber($data);
            }

            $relatedTableData = [];
            $relatedTableData['document_number_id'] = $data['id'];

            $setting = $this->getSetting();
            if (null !== $this->document->getDocumentNumberColumnName()) {
                if (1 === (int)$setting->record['set_number_after_approval']) {
                    $documentNumberForRelatedTable = '-';
                } else {
                    $documentNumberForRelatedTable = $data['documentNumber'];
                }

                $relatedTableData[$this->document->getDocumentNumberColumnName()] = $documentNumberForRelatedTable;
            }

            DB::update($this->document->getTableName(), $relatedTableData, $this->document->getId());

            if ($notInTransaction) {
                DB::commit();
            }

            if (method_exists($this->document, 'onNumberAcquire')) {
                $this->document->onNumberAcquire();
            }

            return $data['id'];
        } catch (Exception $exception) {
            if ($notInTransaction) {
                DB::rollBack();
            }

            throw $exception;
        }

    }

    public function onDocumentApprove()
    {
        $id = $this->document->getDocumentNumberId();
        $documentNumber = DB::fetchById('tb_prodoc_document_number', $id);

        if (0 === (int)$documentNumber['set_number_after_approval']) {
            return;
        }

        DB::update('tb_prodoc_document_number', [
            'approved' => self::STATUS_NUMBER_APPROVED
        ], $id);

        if (null !== $this->document->getDocumentNumberColumnName()) {
            DB::update($this->document->getTableName(), [
                $this->document->getDocumentNumberColumnName() => $documentNumber['document_number']
            ], $this->document->getId());
        }
    }
}