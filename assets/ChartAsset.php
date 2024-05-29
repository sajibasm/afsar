<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ChartAsset extends AssetBundle
{
    public $basePath = '@app';
    public $baseUrl = '@web/assets';
    public $css = [
        'lib/css/site.css',
        'lib/css/custom.css',
    ];

    public $cssOptions = [
    ];

    public $js = [
        'lib/js/init.js',
        'lib/js/modalAjax.js',
        'lib/amcharts4/core.js',
        'lib/amcharts4/charts.js',
        'lib/amcharts4/themes/animated.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
