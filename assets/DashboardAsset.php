<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class DashboardAsset extends AssetBundle
{
    public $sourcePath = '@app/library/dashboard'; // ✅ One unified sourcePath

    public $css = [
        'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css',
        'css/chart.css',
    ];

    public $js = [
        'js/amcharts/4.8.9/core.js',
        'js/amcharts/4.8.9/charts.js',
        'js/amcharts/4.8.9/themes/dataviz.js',
        'js/amcharts/4.8.9/themes/material.js',
        'js/amcharts/4.8.9/themes/animated.js',
    ];

    public $jsOptions = [
        'position' => View::POS_END,
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}

