<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;



use DateInterval;
use DateTime;
use DateTimeZone;
use Yii;

class DateTimeUtility
{
    private static function getTimeZone($timeZone = null)
    {
        return new DateTimeZone($timeZone ?? Yii::$app->params['timeZone']);
    }

    private static function getDateTime($date = 'NOW', $timeZone = null): DateTime
    {
        return new DateTime($date, self::getTimeZone($timeZone));
    }

    public static function getDate($date = null, $format = 'Y-m-d', $timeZone = null)
    {
        return self::getDateTime($date, $timeZone)->format($format);
    }

    public static function getTime($date = null, $format = 'Y-m-d h:i:s', $timeZone = null)
    {
        return self::getDate($date, $format, $timeZone);
    }

    public static function getStartTime($amPm = false, $date = null)
    {
        $time = $amPm ? '00:00 AM' : '00:00:00';
        return $date ? "$date $time" : $time;
    }

    public static function getEndTime($amPm = false, $date = null)
    {
        $time = $amPm ? '11:59 PM' : '23:59:59';
        return $date ? "$date $time" : $time;
    }

    public static function getTodayStartTime($amPm = false)
    {
        $today = self::getDateTime()->format('Y-m-d');
        return self::getStartTime($amPm, $today);
    }

    public static function getTodayEndTime($amPm = false)
    {
        $today = self::getDateTime()->format('Y-m-d');
        return self::getEndTime($amPm, $today);
    }

    public static function getDateIntervalByDate($date, $intervalDays, $format = 'Y-m-d H:i:s')
    {
        $dateTime = self::getDateTime($date);
        $dateTime->sub(new DateInterval("P{$intervalDays}D"));
        return $dateTime->format($format);
    }

    public static function getDateInterval($intervalDays = 1, $format = 'Y-m-d H:i:s')
    {
        $dateTime = self::getDateTime();
        $dateTime->sub(new DateInterval("P{$intervalDays}D"));
        return $dateTime->format($format);
    }

    public static function getDateDiffOnDay($from='NOW', $to = 'NOW', $format = "%a")
    {
        $fromDate = self::getDateTime($from);
        $toDate = self::getDateTime($to);
        return $fromDate->diff($toDate)->format($format);
    }

    public static function countDown($date)
    {
        $from = self::getDateTime($date);
        $now = self::getDateTime();
        $interval = $from->diff($now);

        if ($interval->y > 0) {
            return "{$interval->y} Year(s)";
        } elseif ($interval->m > 0) {
            return "{$interval->m} Month(s)";
        } else {
            return "{$interval->d} Day(s)";
        }
    }

    public static function validateDate($dateStr, $format = 'Y-m-d', $timeZone = null)
    {
        $date = DateTime::createFromFormat($format, $dateStr, self::getTimeZone($timeZone));
        $errors = DateTime::getLastErrors();
        return $date && $errors['warning_count'] === 0 && $errors['error_count'] === 0;
    }
}
