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

    public static function getDate($date = null , $format = 'Y-m-d', $timeZone='Asia/Dhaka')
    {
        $zone = $timeZone?$timeZone:Yii::$app->params['timeZone'];
        $newDate = !empty($date) ? new DateTime($date, new DateTimeZone($zone)) : new DateTime('NOW', new DateTimeZone($zone));
        return $newDate->format($format) ;
    }

    public static function getTime($date = null , $format = 'Y-m-d H:i:s', $timeZone='Asia/Dhaka')
    {
        return self::getDate($date, $format, $timeZone);
    }

    public static function getStartTime($AM_PM = false, $date=null)
    {
        $time = $AM_PM?'00:00 AM':'00:00:00';
        return !empty($date) ? $date . ' ' . $time : $time;
    }

    public static function getEndTime($AM_PM = false, $date=null)
    {
        $time = $AM_PM?'11:59 PM':'23:59:59';
        return !empty($date) ? $date . ' ' . $time : $time;
    }

    public static function getTodayStartTime($AM_PM = false)
    {
        $date = new DateTime('NOW', new DateTimeZone(Yii::$app->params['timeZone']));
        return $date->format('Y-m-d').' '.self::getStartTime();
    }

    public static function getTodayEndTime()
    {
        $date = new DateTime('NOW', new DateTimeZone(Yii::$app->params['timeZone']));
        return $date->format('Y-m-d').' '.self::getEndTime() ;
    }

    public static function getDateIntervalByDate($date, $interval, $format = 'Y-m-d H:i:s')
    {
        $date = new DateTime($date, new DateTimeZone(Yii::$app->params['timeZone']));
        $date->sub(new DateInterval('P'.$interval.'D'));
        return $date->format($format) ;
    }


    public static function getDateInterval($interval = 1, $format = 'Y-m-d H:i:s')
    {
        $dateInterval ='P'.$interval.'D';
        $date = new DateTime('NOW', new DateTimeZone(Yii::$app->params['timeZone']));
        $date->sub(new DateInterval($dateInterval));
        return $date->format($format) ;
    }

    public static function getDateDiffOnDay($from, $to, $format="%a")
    {
        $datetime1 = new DateTime($from, new DateTimeZone(Yii::$app->params['timeZone']));
        $datetime2 = new DateTime('NOW', new DateTimeZone(Yii::$app->params['timeZone']));
        $interval = $datetime1->diff($datetime2);
        return $interval->format($format);
    }

    public static function countDown($date)
    {

        $countDown = "";

        $datetime1 = new DateTime($date, new DateTimeZone(Yii::$app->params['timeZone']));
        $datetime2 = new DateTime('NOW', new DateTimeZone(Yii::$app->params['timeZone']));
        $interval = $datetime1->diff($datetime2);

        if($interval->d>0){
            $countDown.="{$interval->d}, Days";
        }elseif ($interval->m>0){
            $countDown.="{$interval->m}, Months";
        }elseif ($interval->y>0){
            $countDown.="{$interval->y}, Year";
        }else{
            $countDown.="{$interval->d}, Days";
        }

        return $countDown;
    }


    public static function validateDate( $str_dt, $str_dateformat='Y-m-d', $str_timezone='Asia/Dhaka')
    {
        $date = DateTime::createFromFormat( $str_dateformat, $str_dt, new DateTimeZone( $str_timezone ) );
        return $date && DateTime::getLastErrors()['warning_count'] == 0 && DateTime::getLastErrors()['error_count'] == 0;
    }

}