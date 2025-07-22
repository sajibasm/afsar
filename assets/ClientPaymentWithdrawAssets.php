<?php
// app/assets/SalesAsset.php
namespace app\assets;
use yii\web\AssetBundle;

class ClientPaymentWithdrawAssets extends AssetBundle
{
    public $sourcePath = '@app/library/client-payment-withdraw'; // Parent of both js/ and css/

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
