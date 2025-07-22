<?php
// app/assets/SalesAsset.php
namespace app\assets;
use yii\web\AssetBundle;

class ClientPaymentAsset extends AssetBundle
{
    public $sourcePath = '@app/library/client-payment'; // Parent of both js/ and css/

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
