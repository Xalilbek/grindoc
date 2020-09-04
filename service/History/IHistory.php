<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 14.06.2018
 * Time: 16:43
 */

namespace History;


interface IHistory
{
    function getId():int;
    function getHistoryKey():string;

    /**
     * @param string $operation
     * @return array|null
     */
    function getLoggedData(string $operation);
}