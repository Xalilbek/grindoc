<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 05.07.2018
 * Time: 15:06
 */

namespace PowerOfAttorney;

use DateTime;

interface IPowerOfAttorneyDocument
{
    function getStatus();
    function getDate(): DateTime;
}