<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\View;

use yii\base\Exception;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/lib/';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap',
        'css/site.css',
        'css/custom.css',];

    public $cssOptions = [];

    public $js = [
        //'needim/noty/lib/noty.js',

        'js/init.js',
        'js/modalAjax.js',
        'js/core/html5shiv.min.js',
        'js/core/respond.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $publishOptions = [
        'only' => [
            'fonts/*',
            'css/*',
        ]
    ];

}
