<?php
// app/assets/SalesAsset.php
namespace app\assets;
use yii\web\AssetBundle;

class CustomerPaymentAssets extends AssetBundle
{
    public $sourcePath = '@app/library/client/js/'; // ✅ Protected directory (not publicly accessible)
    public $depends = [ \yii\web\JqueryAsset::class ];
}
