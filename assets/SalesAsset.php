<?php
// app/assets/SalesAsset.php
namespace app\assets;
use yii\web\AssetBundle;

class SalesAsset extends AssetBundle
{
    public $sourcePath = '@app/library/sales'; // ✅ Protected directory (not publicly accessible)
    public $depends = [ \yii\web\JqueryAsset::class ];
}
