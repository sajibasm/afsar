<?php

namespace app\components;

use app\models\AppSettings;
use Yii;
use yii\helpers\Json;

class SystemSettings
{
    private static array $cache = [];

    public static function getAttribute(?string $key = null)
    {
        if (empty($key)) {
            return AppSettings::find()->all();
        }

        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $model = AppSettings::find()->where(['app_options' => $key])->one();
        if (!$model) {
            return self::$cache[$key] = null;
        }

        $value = $model->app_values;
        $type = strtolower($model->type);

        $result = null;

        switch ($type) {
            case 'bool':
                $result = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;

            case 'int':
                $result = (int) $value;
                break;

            case 'float':
                $result = (float) $value;
                break;

            case 'json':
                $decoded = json_decode($value, true);
                $result = (json_last_error() === JSON_ERROR_NONE) ? $decoded : null;
                break;

            case 'list':
                $result = array_map('trim', explode(',', $value));
                break;

            case 'file':
                $result = !empty($value) ? Yii::getAlias('@web/uploads/' . ltrim($value, '/')) : null;
                break;

            case 'url':
                $result = filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
                break;

            case 'email':
                $result = filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null;
                break;

            case 'date':
                $timestamp = strtotime($value);
                $result = $timestamp ? date('Y-m-d', $timestamp) : null;
                break;

            case 'datetime':
                $timestamp = strtotime($value);
                $result = $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
                break;

            case 'color':
                $result = preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value) ? $value : null;
                break;

            case 'encrypted':
                $result = self::decrypt($value);
                break;

            case 'richtext':
                $result = (string) $value;
                break;

            case 'phone':
                $result = preg_replace('/[^0-9+]/', '', $value);
                break;

            case 'currency':
                $result = self::parseCurrency($value);
                break;

            case 'object':
                $decoded = json_decode($value);
                $result = (json_last_error() === JSON_ERROR_NONE) ? $decoded : null;
                break;

            case 'raw':
            default:
                $result = is_numeric($value) ? $value + 0 : trim((string) $value);
                break;
        }

        return self::$cache[$key] = $result;
    }

    protected static function decrypt($value): ?string
    {
        try {
            return Yii::$app->security->decryptByPassword(base64_decode($value), 'your-secret-key');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected static function parseCurrency($value): array
    {
        $parts = explode(' ', trim($value));
        return [
            'amount' => isset($parts[0]) && is_numeric($parts[0]) ? (float) $parts[0] : 0,
            'currency' => isset($parts[1]) ? strtoupper($parts[1]) : 'BDT',
        ];
    }

    protected static function getBoolSetting(string $key): bool
    {
        return self::getAttribute($key) === true;
    }

    // Branding & Contact
    public static function Company(): ?string { return self::getAttribute('NAME'); }

    public static function CompanyShortName()
    {
        $words = preg_split('/\s+/', trim(self::Company()));
        $initials = array_map(fn($word) => strtoupper($word[0]), $words);
        return implode('', $initials);
    }

    public static function CompanyAddress1(): ?string { return self::getAttribute('ADDRESS1'); }
    public static function CompanyAddress2(): ?string { return self::getAttribute('ADDRESS2'); }
    public static function CompanyCity(): ?string { return self::getAttribute('CITY'); }
    public static function CompanyPostalCode(): ?string { return self::getAttribute('POSTAL_CODE'); }
    public static function CompanyPhoneNumber(): ?string { return self::getAttribute('PHONE_NUMBER'); }
    public static function CompanyContactNumber(): ?string { return self::getAttribute('CONTACT_NUMBER'); }
    public static function CompanyLogo(): ?string { return self::getAttribute('LOGO'); }
    public static function CompanyWaterMark(): ?string { return self::getAttribute('LOGO_WATER_MARK'); }
    public static function CompanyEmail(): ?string { return self::getAttribute('CONTACT_EMAIL'); }
    public static function CompanyDomain(): ?string { return self::getAttribute('WEBSITE'); }
    public static function DevelopBy(): ?string { return self::getAttribute('DEVELOPED_BY'); }

    // Appearance
    public static function getAppColor(): ?string { return self::getAttribute('COLOR'); }
    public static function themeColor(): string { return self::getAttribute('THEME-COLOR') ?: 'skin-blue'; }

    // Date & Time Formats
    public static function calenderDateFormat(): ?string { return self::getAttribute('CALENDER_DATE_FORMAT'); }
    public static function calenderEndDateFormat(): ?string { return self::getAttribute('CALENDER_END_DATE_FORMAT'); }
    public static function DateFormat(): ?string { return self::getAttribute('DATE_FORMAT'); }
    public static function TimeFormat(): ?string { return self::getAttribute('TIME_FORMAT'); }
    public static function dateTimeFormat(): ?string { return self::getAttribute('REPORT_DATE_TIME_FORMAT'); }

    // Currency & Pagination
    public static function AppCurrency(): ?string { return self::getAttribute('CURRENCY'); }
    public static function PerPageRecords(): ?int { return (int) self::getAttribute('PER_PAGE_RECORDS'); }
    public static function VatPercent(): float { return (float) self::getAttribute('VAT_PERCENTAGE'); }
    public static function AitPercent(): float { return (float) self::getAttribute('AIT_PERCENTAGE'); }

    // Email & Auth
    public static function AppEmail(): ?string { return self::getAttribute('EMAIL'); }

    // Invoice Settings
    public static function invoiceTrackingNotificationSMS(): bool { return self::getBoolSetting('INVOICE_TRACKING_NOTIFICATION_SMS'); }
    public static function invoiceTrackingNotificationEmail(): bool { return self::getBoolSetting('INVOICE_TRACKING_NOTIFICATION_EMAIL'); }
    public static function invoiceUpdateNotificationEmail(): bool { return self::getBoolSetting('INVOICE_CREATE_NOTIFICATION_EMAIL'); }
    public static function invoiceFooterMassage(): ?string { return self::getAttribute('INVOICE_FOOTER_MESSAGE'); }

    // Customer Notifications
    public static function customerDueReceivedSMS(): bool { return self::getBoolSetting('CUSTOMER_DUE_RECEIVED_SMS'); }

    // Outlet Management
    public static function Store($id = null, bool $self = false)
    {
        $data = Json::decode(self::getAttribute('SHOWROOM_LIST'), true);
        if (!is_array($data)) return [];

        $list = [];

        foreach ($data as $outlet) {
            if ($id !== null && isset($outlet['id']) && $outlet['id'] == $id) {
                return $outlet;
            }

            if ($self) {
                return $outlet;
            }

            if (empty($outlet['self'])) {
                $list[] = [
                    'id' => $outlet['id'] ?? null,
                    'name' => $outlet['name'] ?? '',
                ];
            }
        }

        return $list;
    }

    public static function getOutletById($id)
    {
        return self::Store($id);
    }

    public static function watermark(): ?string
    {
        return self::CompanyWaterMark();
    }
}
