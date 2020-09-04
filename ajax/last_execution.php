<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 25.06.2018
 * Time: 12:47
 */

use Model\LastExecution\LastExecution;

session_start();
include_once '../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/LastExecutionDate/LastExecution.php';

$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

try {
    $executionDaysNum   = get('executionDaysNum');
    $executionDate      = get('executionDate');
    $executionStartDate = get('executionStartDate');
    $tip                = get('tip');
    $option_name        = '';

    if($tip == 2)
    {
        $option_name = 'nezaret_muddeti_fiziki';
    }
    elseif ($tip == 3)
    {
        $option_name = 'nezaret_muddeti_daxili';
    }
    else
    {
        $option_name = 'nezaret_muddeti';
    }

    $nezaret_muddeti = DB::fetchOneColumnBy('tb_options', 'value', [
       'option_name' => $option_name
    ]);



    $lastExecution = new LastExecution(
        $user,
        $nezaret_muddeti === 'istehsalat_teqvimi',
        new DateTime($executionStartDate)
    );

    $result = [];
    if (!is_null($executionDaysNum)) {
        if ((int)$executionDaysNum > 1000) {
            throw new Exception();
        }

        $res = $lastExecution->getLastExecutionDateByDaysNum($executionDaysNum, true);
        if ($res instanceof DateTime) {

            $result['lastExecutionDate'] = $res->format('d-m-Y');
            $result['nonWorkingDaysNum'] = 0;
        } else {

            $result['lastExecutionDate'] = $res['lastExecutionDate']->format('d-m-Y');
            $result['nonWorkingDaysNum'] = $res['nonWorkingDaysNum'];
        }

    } else if (!is_null($executionDate)) {
        $res = $lastExecution->getRemainingDaysByLastExecutionDate(new DateTime($executionDate), true);

        if (is_array($res)) {
            $result['remainingDaysNum']  = $res['remainingDaysNum'];
            $result['nonWorkingDaysNum'] = $res['nonWorkingDaysNum'];
        } else {
            $result['remainingDaysNum'] = $res;
            $result['nonWorkingDaysNum'] = 0;
        }

    } else {
        throw new Exception();
    }

    print json_encode($result);
} catch (Exception $e) {
    $user->error_msg('There is an error!');
}