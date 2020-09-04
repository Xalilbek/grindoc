<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 04.05.2018
 * Time: 14:22
 */

namespace Util;

class ArrayUtils
{
    public static function pick(array $array, array $keys, $removeNonExistingKeys = false): array
    {
        $newArray = [];

        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                if ($removeNonExistingKeys) {
                    continue;
                }

                $array[$key] = NULL;
                continue;
            }

            $newArray[$key] = $array[$key];
        }

        return $newArray;
    }

    public static function omit(array $array, array $keys): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            if (in_array($key, $keys)) {
                continue;
            }

            $newArray[$key] = $value;
        }

        return $newArray;
    }

    public static function removeByValue(array $array, $value): array
    {
        return array_diff($array, [$value]);
    }
    public static function indexGroupped($array, $indexBy = null, $associative = false)
    {
        $resultSet = array();
        foreach ($array as $record) {
            $index = $record[$indexBy];

            if (!isset($resultSet[$index])) {
                $resultSet[$index] = [];
            }
            if($associative){
                $resultSet[$index][] = $record;
            }else{
                $resultSet[$index] = $record;
            }
        }

        return $resultSet;
    }

    public static function defaults(array $array, array $defaultValues)
    {
        foreach ($defaultValues as $key => $defaultValue) {
            if (!array_key_exists($key, $array)) {
                $array[$key] = $defaultValue;
            }
        }

        return $array;
    }

    public static function find(array $array, callable $callback)
    {
        foreach ($array as $key => $item) {
            if (true === $callback($item)) {
                return $item;
            }
        }

        return null;
    }
}