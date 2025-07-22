<?php
// app/assets/AlertAsset.php
namespace app\assets;

use yii\web\AssetBundle;

class AlertAsset extends AssetBundle
{
    public $sourcePath = '@app/library/alert/'; // ✅ Protected directory (not publicly accessible)
    public $js = ['confirm-buttons.js'];
    public $depends = [\yii\web\JqueryAsset::class];
}
