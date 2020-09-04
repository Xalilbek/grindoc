<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 24.04.2018
 * Time: 12:32
 */

interface IAppealAssociated {
    public function getColumnName();
    public function getId();
    public function canCreateAppeal();
}