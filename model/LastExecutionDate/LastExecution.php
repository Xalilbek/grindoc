<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 25.06.2018
 * Time: 11:13
 */

namespace Model\LastExecution;

require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';

use DateTime;
use DateInterval;
use User;
use DB;
use Util\ArrayUtils;

class LastExecution
{
    private $cache = [];
    private $user;
    private $secondsInADay;
    private $startDate;

    public function __construct(User $user, $considerNonWorkingDays = true, DateTime $startDate = null)
    {
        $this->user = $user;
        $this->considerNonWorkingDays = $considerNonWorkingDays;
        $this->secondsInADay = 24 * 60 * 60;

        if (is_null($startDate)) {
            $this->startDate = new DateTime();
        } else {
            $this->startDate = $startDate;
        }
    }

    public function getLastExecutionDateByDaysNum($executionDaysNum, $returnNonWorkingDays = false)
    {
        if ($this->considerNonWorkingDays) {
            return $this->getLastExecutionDateByDaysNumWithNonWorkingDays($executionDaysNum, $returnNonWorkingDays);
        } else {
            return $this->getLastExecutionDateByDaysNumWithOutWorkingDays($executionDaysNum, $returnNonWorkingDays);
        }
    }

    public function getLastExecutionDateByDaysNumWithNonWorkingDays($executionDaysNum, $returnNonWorkingDays = false)
    {
        $lastExecutionDate = $this->startDate;

        $remainingSecondsOfCurrentDay = null;
        if (!$this->isNonWorkingDay($lastExecutionDate)) {
            $remainingSecondsOfCurrentDay = new DateInterval(sprintf('PT%sS',
                $this->secondsInADay - $this->getTodaysSeconds($lastExecutionDate)
            ));
        }

        $oneDayInterval  = new DateInterval('P1D');
        $todaysBeginning = new DateTime($lastExecutionDate->format('d-m-Y 00:00'));
        $lastExecutionDate = $todaysBeginning->add($oneDayInterval);

        $nonWorkingDaysNum = 0;
        while ($executionDaysNum > 0) {
            if ($this->isNonWorkingDay($lastExecutionDate)) {
                $lastExecutionDate->add($oneDayInterval);
                $nonWorkingDaysNum++;
                continue;
            }

            $lastExecutionDate->add($oneDayInterval);
            $executionDaysNum--;
        }

        if (!is_null($remainingSecondsOfCurrentDay)) {
            $lastExecutionDate->sub($remainingSecondsOfCurrentDay);
        }
        if ($returnNonWorkingDays) {
            return [
                'lastExecutionDate'  => $lastExecutionDate,
                'nonWorkingDaysNum' => $nonWorkingDaysNum
            ];
        } else {
            return $lastExecutionDate;
        }
    }

    private function getTodaysSeconds(DateTime $dateTime)
    {
        $hours   = $dateTime->format('H');
        $minutes = $dateTime->format('i');
        $seconds = $dateTime->format('s');

        return ($hours * 60 * 60) + ($minutes * 60) + $seconds;
    }

    public function getLastExecutionDateByDaysNumWithOutWorkingDays($executionDaysNum, $returnNonWorkingDays = false)
    {
        $lastExecutionDate = $this->startDate;
        $oneDayInterval = new DateInterval(sprintf('P%sD', $executionDaysNum));

        $lastExecutionDate->add($oneDayInterval);
        return $lastExecutionDate;
    }

    public function getRemainingDaysByLastExecutionDate(DateTime $lastExecutionDate, $returnNonWorkingDays = false)
    {
        if ($this->considerNonWorkingDays) {
            return $this->getRemainingDaysByLastExecutionDateWithNonWorkingDays($lastExecutionDate, $returnNonWorkingDays);
        } else {
            return $this->getRemainingDaysByLastExecutionDateWithoutNonWorkingDays($lastExecutionDate);
        }
    }

    public function getRemainingDaysByLastExecutionDateWithNonWorkingDays(DateTime $lastExecutionDate, $returnNonWorkingDays = false)
    {
        $nonWorkingDaysNum = 0;
        $remainingDaysNum = 1;

        $oneDayInterval = new DateInterval('P1D');
        $currentDateTime = $this->startDate;
        if ($this->isNonWorkingDay($currentDateTime)) {
            $todaysBeginning = new DateTime($currentDateTime->format('d-m-Y 00:00'));
            $currentDateTime = $todaysBeginning->add($oneDayInterval);
        }

        $lastExecutionDatetime = $lastExecutionDate->getTimestamp();
        while ($lastExecutionDatetime > $currentDateTime->getTimestamp()) {
            if ($this->isNonWorkingDay($currentDateTime)) {
                $currentDateTime->add($oneDayInterval);
                $nonWorkingDaysNum++;
                continue;
            }

            $currentDateTime->add($oneDayInterval);

            if ($currentDateTime->getTimestamp() > $lastExecutionDatetime) {
                break;
            }

            $remainingDaysNum++;
        }

        if ($returnNonWorkingDays) {
            return [
                'remainingDaysNum'  => $remainingDaysNum,
                'nonWorkingDaysNum' => $nonWorkingDaysNum
            ];
        } else {
            return $remainingDaysNum;
        }
    }

    public function getRemainingDaysByLastExecutionDateWithoutNonWorkingDays(DateTime $lastExecutionDate)
    {
        $cd = $this->startDate;
        $diffInSeconds = $lastExecutionDate->getTimestamp() - $cd->getTimestamp();

        $secondsInADay = 24 * 60 * 60;

        return floor($diffInSeconds / $secondsInADay);
    }

    public function isNonWorkingDay(DateTime $date): bool
    {
        if ($this->isWeekend($date)) {
            return true;
        }

        $holidays = array_map(function($holiday) {
            $holiday['start_date'] = new DateTime($holiday['start_date'] . ' 00:00');
            $holiday['end_date']   = new DateTime($holiday['end_date']   . ' 23:59');
            return $holiday;
        }, $this->getHolidays());

        foreach ($holidays as $holiday) {
            if ($this->dateInRange($date, $holiday['start_date'], $holiday['end_date']))
                return true;
        }

        return false;
    }

    public function isWeekend(DateTime $date)
    {
        $dayOfTheWeek = (int)$date->format('N');
        $year         = (int)$date->format('Y');

        $weekends = $this->getWeekends();
        $weekend = ArrayUtils::find($weekends, function ($weekend) use ($dayOfTheWeek, $year) {
            return (int)$weekend['day_of_the_week'] === $dayOfTheWeek && (int)$weekend['year'] === $year;
        });

        return $weekend !== null;
    }

    public function getWeekends()
    {
        if (isset($this->cache['weekends'])) {
            return $this->cache['weekends'];
        }

        $tenantId = $this->user->getActiveTenantId();

        $sql = "
            SELECT
             weekend.day_of_the_week, calendar.year 
            FROM tb_prodoc_weekend weekend
            LEFT JOIN tb_calendar calendar
             ON calendar.id = weekend.calendar_id
            WHERE calendar_id IN (
                SELECT id FROM tb_calendar WHERE TenantId = $tenantId 
            )
        ";

        return $this->cache['weekends'] = DB::fetchAll($sql);
    }

    public function getHolidays()
    {
        if (isset($this->cache['holidays'])) {
            return $this->cache['holidays'];
        }

        $tenantId    = $this->user->getActiveTenantId();
        $currentYear = date('Y');

        $sql = "
            SELECT
                calendar_off_days.tarix1 AS start_date,
                calendar_off_days.tarix2 AS end_date
            FROM tb_qeyri_ish_gunleri calendar_off_days
            WHERE
                calendar_off_days.TenantId = $tenantId AND
                calendar_off_days.deleted = 0
        ";

        return $this->cache['holidays'] = DB::fetchAll($sql);
    }

    public function dateInRange(
        DateTime $date,
        DateTime $startDate,
        DateTime $endDate
    )
    {
        return
            $date->getTimestamp() >= $startDate->getTimestamp() &&
            $date->getTimestamp() <= $endDate->getTimestamp()
        ;
    }
}