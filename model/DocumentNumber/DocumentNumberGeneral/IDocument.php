<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 15.05.2018
 * Time: 14:36
 */

namespace Model\DocumentNumber\DocumentNumberGeneral;


interface IDocument
{
    function getDirection();
    function getDocumentType();
    function getTableName();
    function getDocumentNumberId();
    function getOption();
    function getOptionValue();

    function getDocumentNumberColumnName();
}