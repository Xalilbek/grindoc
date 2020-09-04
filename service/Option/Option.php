<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 01.10.2018
 * Time: 14:23
 */

namespace Service\Option;

use DB;

class Option
{
    private static $options = null;

    public static function getOrCreateValue($optionName, $defaultValue)
    {
        $user = new \User();

        if (is_null(self::$options)) {
            $tenantFilter = $user->getQueryTenantFilter('selectCheckMsk');
            $sql = "
                SELECT *
                FROM tb_options
                WHERE
                $tenantFilter
            ";

            self::$options = DB::fetchAllIndexed($sql, 'option_name');
        }

        if (array_key_exists($optionName, self::$options)) {
            $option = self::$options[$optionName];
        } else {
            $option = false;
        }

        if (FALSE === $option) {
            DB::insert('tb_options', [
                'option_name' => $optionName,
                'value' => $defaultValue,
                'TenantId' => $user->getActiveTenantId()
            ]);
            self::$options[$optionName] = $defaultValue;

            return $defaultValue;
        }

        return $option['value'];
    }
}