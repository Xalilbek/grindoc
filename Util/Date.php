<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 14.07.2018
 * Time: 11:25
 */

namespace Util;


class Date
{
    public static function formatDate($date, $format = 'd-m-Y')
    {
        if (is_null($date)) {
            return '-';
        }

        return date($format, strtotime($date));
    }

    public static function formatDateTime($date, $format = 'd-m-Y H:i')
    {
        if (is_null($date)) {
            return '-';
        }

        return date($format, strtotime($date));
    }
}