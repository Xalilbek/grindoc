<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 24.04.2018
 * Time: 10:43
 */

interface ITaskAssociated {
    public function getColumnName();
    public function getId();
    public function isInformative();
}