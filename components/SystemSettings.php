<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;



use app\models\AppSettings;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

class SystemSettings
{
    public static function getAttribute($value)
    {

        if(empty($value) || $value == null){
            return  AppSettings::find()->all();
        }else{
            $model = AppSettings::find()->where(['app_options' => $value])->One();
            if($model->type==AppSettings::BOOl_TYPE){
                if($model->app_values=='false'){
                    return false;
                }else{
                    return true;
                }
            }else{
                return (string) trim($model->app_values);
            }
        }
    }

    public static function getStoreName()
    {
        return self::getAttribute('NAME');
    }

    public static function calenderDateFormat()
    {
        return self::getAttribute('CALENDER_DATE_FORMAT');
    }

    public static function calenderEndDateFormat()
    {
        return self::getAttribute('CALENDER_END_DATE_FORMAT');
    }

    public static function invoiceSalesAutoPrint()
    {
        return self::getAttribute('INVOICE_SALES_AUTO_PRINT')=='true'?true:false;
    }

    public static function invoiceExpenseAutoPrint()
    {
        return self::getAttribute('INVOICE_EXPENSE_AUTO_PRINT')=='true'?true:false;
    }

    public static function invoiceAutoPrintWindow()
    {
        return self::getAttribute('INVOICE_SALES_AUTO_PRINT')=='true'?true:false;
    }

    public static function getAddress1()
    {
        return self::getAttribute('ADDRESS1');
    }

    public static function getAddress2()
    {
        return self::getAttribute('ADDRESS2');
    }

    public static function getLogo()
    {
        return self::getAttribute('LOGO');
    }

    public static function getContactNumber()
    {
        return self::getAttribute('CONTACT_NUMBER');
    }

    public static function getAppColor()
    {
        return self::getAttribute('COLOR');
    }

    public static function getTimeZone()
    {
        return self::getAttribute('TIME_ZONE');
    }


    public static function getDateFormat()
    {
        return self::getAttribute('DATE_FORMAT');
    }

    public static function getTimeFormat()
    {
        return self::getAttribute('TIME_FORMAT');
    }

    public static function getAppCurrency()
    {
        return self::getAttribute('CURRENCY');
    }

    public static function getPerPageRecords()
    {
        return self::getAttribute('PER_PAGE_RECORDS');
    }

    public static function getAppEmail()
    {
        return self::getAttribute('EMAIL');
    }

    public static function getStoreWaterMark()
    {
        return self::getAttribute('LOGO_WATER_MARK');
    }

    public static function getAuthTimeOut()
    {
        return self::getAttribute('TIME_OUT');
    }

    public static function invoiceSMS()
    {
        return self::getAttribute('INVOICE_SMS');
    }

    public static function invoiceEmail()
    {
        return self::getAttribute('INVOICE_EMAIL');
    }

    public static function invoiceTrackingNotificationSMS()
    {
        return self::getAttribute('INVOICE_TRACKING_NOTIFICATION_SMS');
    }

    public static function customerDueReceivedSMS()
    {
        return self::getAttribute('CUSTOMER_DUE_RECEIVED_SMS');
    }


    public static function invoiceTrackingNotificationEmail()
    {
        return self::getAttribute('INVOICE_TRACKING_NOTIFICATION_EMAIL');
    }


    public static function invoiceUpdateNotificationEmail()
    {
        return self::getAttribute('INVOICE_CREATE_NOTIFICATION_EMAIL');
    }

    public static function dateTimeFormat()
    {
        return self::getAttribute('REPORT_DATE_TIME_FORMAT');
    }

    public static function watermark()
    {
        return self::getAttribute('LOGO_WATER_MARK');
    }

    public static function invoiceFooterMassage()
    {
        return self::getAttribute('INVOICE_FOOTER_MESSAGE');
    }

    public static function themeColor()
    {
        return self::getAttribute('THEME-COLOR')?self::getAttribute('THEME-COLOR'):'skin-blue';
    }

    public static function getOutlet($id=null, $self=false)
    {
        $list  = [];
        $data = Json::decode(self::getAttribute('SHOWROOM_LIST'));
        foreach ($data as $outlet){
            if($outlet['id']==$id){
                return $outlet;
            }elseif ($self){
                return $outlet;
            }else{
                if($outlet['self']==false){
                    $list[] = ['id'=>$outlet['id'], 'name'=>$outlet['name']];
                }
            }
        }

        return $list;
    }

    public static function getOutletById($id)
    {
       return self::getOutlet($id);
    }

    public static function getAccessToken()
    {
        return self::getAttribute('ACCESS-TOKEN');
    }

}