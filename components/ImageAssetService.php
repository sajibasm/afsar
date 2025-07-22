<?php
namespace app\components;

use Yii;
use yii\helpers\Url;

class ImageAssetService
{
    protected static $basePath;
    protected static $baseUrl;

    protected static function publishAssets()
    {
        if (!self::$basePath || !self::$baseUrl) {
            $folder = Yii::getAlias('@app/library/assets/images');
            list($path, $url) = Yii::$app->assetManager->publish($folder); // ✅ Correct order

            self::$basePath = $path;
            self::$baseUrl = $url;
        }
    }

    protected static function getImageUrl($filename, $asBase64 = false)
    {
        self::publishAssets();

        $fullPath = self::$basePath . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($fullPath)) {
            return null; // Or use a fallback
        }

        if ($asBase64) {
            $type = pathinfo($fullPath, PATHINFO_EXTENSION);
            $data = file_get_contents($fullPath);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // Default: absolute URL
        return Url::to(self::$baseUrl . '/' . $filename, true);
    }


    // Add your image methods here
    public static function getLogo($asBase64 = false)
    {
        return self::getImageUrl('logo.png', $asBase64);
    }

    public static function getPerson($asBase64 = false)
    {
        return self::getImageUrl('person.png', $asBase64);
    }

    public static function getUser($asBase64 = false)
    {
        return self::getImageUrl('user.png', $asBase64);
    }

    public static function getWatermark($asBase64 = false)
    {
        return self::getImageUrl('watermark.png', $asBase64);
    }

    public static function getLoginAvatar($asBase64 = false)
    {
        return self::getImageUrl('login-avatar.png', $asBase64);
    }

    public static function getLoginBg($asBase64 = false)
    {
        return self::getImageUrl('login-bg.jpg', $asBase64);
    }

    public static function getAccount($asBase64 = false)
    {
        return self::getImageUrl('account.png', $asBase64);
    }

    public static function getAxialLogo($asBase64 = false)
    {
        return self::getImageUrl('axial-logo.png', $asBase64);
    }

    public static function getStore($asBase64 = false)
    {
        return self::getImageUrl('store.png', $asBase64);
    }
}
