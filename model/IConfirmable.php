<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 13.04.2018
 * Time: 11:39
 */
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';

use Service\Confirmation\Confirmation;

interface IConfirmable
{
    const STATUS_TESTIQLEMEDE   = 1;
    const STATUS_TESTIQLENIB    = 2;
    const STATUS_IMTINA_OLUNUB  = 3;

    function getId():int;
    function getApproveOrder(array $user);
    function onStatusChange($newStatus, Confirmation $confirmation);
    function getStatusColumnName();
    function onApprove(array $confirmingUser, $confirmationService);
    function onCancel(array $confirmingUser, $confirmationService);
    function onFullApprove();
}