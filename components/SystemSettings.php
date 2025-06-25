<?php

namespace app\components;

use app\models\AppSettings;
use Yii;
use yii\helpers\Json;

class SystemSettings
{
    public const BOOL_TYPE = AppSettings::BOOl_TYPE;

    public static function getAttribute(string $key = null)
    {
        if (empty($key)) {
            return AppSettings::find()->all();
        }

        $model = AppSettings::find()->where(['app_options' => $key])->one();
        if (!$model) {
            return null;
        }

        if ($model->type === self::BOOL_TYPE) {
            return $model->app_values === 'true';
        }

        return trim((string) $model->app_values);
    }

    public static function getStoreName(): ?string
    {
        return self::getAttribute('NAME');
    }

    public static function calenderDateFormat(): ?string
    {
        return self::getAttribute('CALENDER_DATE_FORMAT');
    }

    public static function calenderEndDateFormat(): ?string
    {
        return self::getAttribute('CALENDER_END_DATE_FORMAT');
    }

    public static function invoiceSalesAutoPrint(): bool
    {
        return self::getAttribute('INVOICE_SALES_AUTO_PRINT') === true;
    }

    public static function invoiceExpenseAutoPrint(): bool
    {
        return self::getAttribute('INVOICE_EXPENSE_AUTO_PRINT') === true;
    }

    public static function invoiceAutoPrintWindow(): bool
    {
        return self::invoiceSalesAutoPrint();
    }

    public static function getAddress1(): ?string
    {
        return self::getAttribute('ADDRESS1');
    }

    public static function getAddress2(): ?string
    {
        return self::getAttribute('ADDRESS2');
    }

    public static function getLogo(): ?string
    {
        return self::getAttribute('LOGO');
    }

    public static function getContactNumber(): ?string
    {
        return self::getAttribute('CONTACT_NUMBER');
    }

    public static function getAppColor(): ?string
    {
        return self::getAttribute('COLOR');
    }

    public static function getTimeZone(): ?string
    {
        return self::getAttribute('TIME_ZONE');
    }

    public static function getDateFormat(): ?string
    {
        return self::getAttribute('DATE_FORMAT');
    }

    public static function getTimeFormat(): ?string
    {
        return self::getAttribute('TIME_FORMAT');
    }

    public static function getAppCurrency(): ?string
    {
        return self::getAttribute('CURRENCY');
    }

    public static function getPerPageRecords(): ?int
    {
        return (int) self::getAttribute('PER_PAGE_RECORDS');
    }

    public static function getAppEmail(): ?string
    {
        return self::getAttribute('EMAIL');
    }

    public static function getStoreWaterMark(): ?string
    {
        return self::getAttribute('LOGO_WATER_MARK');
    }

    public static function getAuthTimeOut(): ?int
    {
        return (int) self::getAttribute('TIME_OUT');
    }

    public static function invoiceSMS(): bool
    {
        return self::getAttribute('INVOICE_SMS') === true;
    }

    public static function invoiceEmail(): bool
    {
        return self::getAttribute('INVOICE_EMAIL') === true;
    }

    public static function invoiceTrackingNotificationSMS(): bool
    {
        return self::getAttribute('INVOICE_TRACKING_NOTIFICATION_SMS') === true;
    }

    public static function customerDueReceivedSMS(): bool
    {
        return self::getAttribute('CUSTOMER_DUE_RECEIVED_SMS') === true;
    }

    public static function invoiceTrackingNotificationEmail(): bool
    {
        return self::getAttribute('INVOICE_TRACKING_NOTIFICATION_EMAIL') === true;
    }

    public static function invoiceUpdateNotificationEmail(): bool
    {
        return self::getAttribute('INVOICE_CREATE_NOTIFICATION_EMAIL') === true;
    }

    public static function dateTimeFormat(): ?string
    {
        return self::getAttribute('REPORT_DATE_TIME_FORMAT');
    }

    public static function watermark(): ?string
    {
        return self::getStoreWaterMark();
    }

    public static function invoiceFooterMassage(): ?string
    {
        return self::getAttribute('INVOICE_FOOTER_MESSAGE');
    }

    public static function themeColor(): string
    {
        return self::getAttribute('THEME-COLOR') ?: 'skin-blue';
    }

    public static function getOutlet($id = null, bool $self = false)
    {
        $data = Json::decode(self::getAttribute('SHOWROOM_LIST'), true);
        if (!is_array($data)) return [];

        $list = [];

        foreach ($data as $outlet) {
            if ($id !== null && $outlet['id'] == $id) {
                return $outlet;
            }

            if ($self) {
                return $outlet;
            }

            if (empty($outlet['self'])) {
                $list[] = [
                    'id' => $outlet['id'],
                    'name' => $outlet['name']
                ];
            }
        }

        return $list;
    }

    public static function getOutletById($id)
    {
        return self::getOutlet($id);
    }

    public static function getAccessToken(): ?string
    {
        return self::getAttribute('ACCESS-TOKEN');
    }
}
