<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 23.05.2018
 * Time: 12:27
 */

namespace Model\DocumentNumber\DocumentNumberGeneral;

require_once 'IDocument.php';

interface IOutgoingDocument extends IDocument
{
    function getCityIndex();
    function getDepartmentIndex();
}