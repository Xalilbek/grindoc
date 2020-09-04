<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 15.05.2018
 * Time: 15:09
 */

namespace Model\DocumentNumber\DocumentNumberGeneral;

require_once 'IDocument.php';

interface IIncomingDocument extends IDocument
{
    function getSenderPerson();

    /**
     * @param string $usedFor
     * @return mixed
     */
    function getSenderPersonFirstSurnameLetter($usedFor = null);
}