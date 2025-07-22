<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class LoginAsset extends AssetBundle
{
    public $sourcePath = '@app/library/login';

    public $css = [
        'css/login.css',
    ];

    public $js = [
//        'js/login.js',
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
            'js/*',
        ],
    ];
}
