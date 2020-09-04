<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 14.06.2018
 * Time: 16:43
 */

namespace Log;


interface ILog
{
    function getId():int;
    function getLogKey():string;

    /**
     * @param string $operation
     * @return array|null
     */
}