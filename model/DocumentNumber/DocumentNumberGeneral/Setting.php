<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 14.05.2018
 * Time: 11:48
 */
namespace Model\DocumentNumber\DocumentNumberGeneral;

require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/BaseEntity.php';
require_once DIRNAME_INDEX . 'prodoc/model/BaseEntity/IBaseEntity.php';

use BaseEntity;
use IBaseEntity;
use User;
use DB;
use Exception;
use Util\ArrayUtils;

class Setting extends BaseEntity implements IBaseEntity
{
    const VALUE_ALL = -1;

    public function getTableName()
    {
        return 'tb_prodoc_document_number_pattern';
    }

    /**
     * Returns subquery which transforms
     * type (to which companies are related) of companies to list of companies by left join
     *
     * @return string
     */
    public static function getCorrectedSettingSubquery(): string
    {
        $sql = "
            SELECT id, extra_id
            FROM tb_prodoc_document_number_pattern_option_list
            WHERE extra_id IN ('qurum_novu', 'adiyati_qurum')
        ";
        $options = DB::fetchAllIndexed($sql, 'extra_id');

        return "
            SELECT
                setting.id,
                setting.active_from,
                setting.initial_number,
                setting.editable,
                setting.editable_with_select,
                setting.repeat_appeal,
                setting.is_deleted,
                setting.pattern_prefix,
                setting.pattern,
                setting.created_by,
                setting.created_at,
                setting.TenantId,
                setting.direction,
                setting.initial_number_for_next_years,
                setting.set_number_after_approval,
                (CASE
                    WHEN setting.option_id = {$options['qurum_novu']['id']}
                    THEN companies.id
                    ELSE option_value.option_value_id
                 END) AS option_value_id,
                (CASE
                    WHEN setting.option_id = {$options['qurum_novu']['id']}
                    THEN {$options['adiyati_qurum']['id']}
                    ELSE setting.option_id
                END) AS option_id
            FROM
                tb_prodoc_document_number_pattern AS setting
                LEFT JOIN tb_prodoc_document_number_pattern_option_value AS option_value
                 ON setting.id = option_value.document_number_patter_id
                LEFT JOIN tb_CustomersCompany AS companies
                 ON setting.option_id = {$options['qurum_novu']['id']} AND option_value.option_value_id = companies.Tipi
        ";
    }

    public static function create(array $data, User $user = null, $checkForDuplications = true)
    {
        try {
            DB::beginTransaction();

            $documentNumberPatternData = ArrayUtils::pick($data, [
                'option_id',
                'pattern_prefix',
                'pattern',
                'direction',
            ]);

            $self = parent::create($documentNumberPatternData, $user);

            self::checkForExcessData($data['option_values']);
            self::checkForExcessData($data['document_types']);

            if ($checkForDuplications)
                self::checkForDuplications($data);

            for ($i = 0, $len = count($data['option_values']); $i < $len; ++$i) {
                DB::insert('tb_prodoc_document_number_pattern_option_value', [
                    'option_value_id' => $data['option_values'][$i],
                    'document_number_patter_id' => $self->getId()
                ]);
            }

            for ($i = 0, $len = count($data['document_types']); $i < $len; ++$i) {
                DB::insert('tb_prodoc_document_number_pattern_document_type', [
                    'document_type_id' => $data['document_types'][$i],
                    'document_number_patter_id' => $self->getId()
                ]);
            }

            DB::commit();
            return $self;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function edit(array $data, $checkForDuplications = true)
    {
        try {
            DB::beginTransaction();

            $documentNumberPatternData = ArrayUtils::pick($data, [
                'option_id',
                'active_from',
                'initial_number',
                'editable',
                'editable_with_select',
                'repeat_appeal',
                'pattern_prefix',
                'pattern'
            ], true);

            DB::update($this->getTableName(), $documentNumberPatternData, $this->getId());

            $id = $this->getId();

            self::checkForExcessData($data['option_values']);
            self::checkForExcessData($data['document_types']);

            if ($checkForDuplications)
                self::checkForDuplications($data);

            DB::query("DELETE FROM tb_prodoc_document_number_pattern_option_value WHERE document_number_patter_id = $id");
            for ($i = 0, $len = count($data['option_values']); $i < $len; ++$i) {
                DB::insert('tb_prodoc_document_number_pattern_option_value', [
                    'option_value_id' => $data['option_values'][$i],
                    'document_number_patter_id' => $this->getId()
                ]);
            }

            DB::query("DELETE FROM tb_prodoc_document_number_pattern_document_type WHERE document_number_patter_id = $id");
            for ($i = 0, $len = count($data['document_types']); $i < $len; ++$i) {
                DB::insert('tb_prodoc_document_number_pattern_document_type', [
                    'document_type_id' => $data['document_types'][$i],
                    'document_number_patter_id' => $this->getId()
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function editAdditionalData(array $data)
    {
        try {
            DB::beginTransaction();
            DB::update($this->getTableName(), $data, $this->getId());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private static function checkForExcessData(array $idList): array
    {
        if (in_array(self::VALUE_ALL, $idList, true) && count($idList) > 1) {
            throw new Exception('"Hamısı" seçimi olduğu üçün, ayri seçimlər əlavə oluna bilməz!');
        }

        return $idList;
    }

    public static function checkForDuplications(array $data)
    {
        $duplications = self::getDuplications($data);

        if (count($duplications) > 0) {
            throw new Exception('There is a duplicate setting!');
        }
    }

    /**
     * If duplications exist, returns an array of them
     *
     * @param array $data
     * @return array
     */
    public static function getDuplications(array $data): array
    {
        $duplications = [];

        $uniqueStrings = [];
        $optionValuesCount  = count($data['option_values']);
        $documentTypesCount = count($data['document_types']);

        for ($i = 0; $i < $optionValuesCount; ++$i) {
            for ($j = 0; $j < $documentTypesCount; ++$j) {
                $uniqueStringElements = [
                    $data['direction'],
                    $data['option_id'],
                    $data['option_values'][$i],
                    $data['document_types'][$j],
                ];
                $uniqueStrings[] = implode("-", $uniqueStringElements);
            }
        }

        if (0 === count($uniqueStrings)) {
            return $duplications;
        }

        $correctedSettingSubquery = Setting::getCorrectedSettingSubquery();

        $uniqueStrings = implode(',', array_map(function ($uniqueString) {
            return DB::quote($uniqueString);
        }, $uniqueStrings));

        $sql = sprintf("
                SELECT setting.*
                FROM ($correctedSettingSubquery) AS setting
                LEFT JOIN tb_prodoc_document_number_pattern_document_type AS doc_type
                 ON setting.id = doc_type.document_number_patter_id
                WHERE
                CONCAT(
                    setting.direction,       '-',
                    setting.option_id,       '-',
                    setting.option_value_id, '-',
                    doc_type.document_type_id
                ) IN ($uniqueStrings)
        ");

        foreach (DB::fetchAll($sql) as $settingRecord) {
            $duplications[] = new Setting($settingRecord['id'], [
                'info' => $settingRecord
            ]);
        }

        return $duplications;
    }
}