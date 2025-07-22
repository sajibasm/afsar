<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/library/init'; // ✅ Root-relative and includes both js/ and css/

    public $css = [
        'css/site.css',
        'css/custom.css',
    ];

    public $js = [
        'js/init.js',
        'js/html5shiv.min.js',
        'js/respond.min.js',
    ];

    public $jsOptions = [
        'position' => View::POS_END,
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $publishOptions = [
        'only' => [
            'css/*',
            'js/*', // ✅ Make sure this matches actual js/ structure
        ],
    ];
}
