<?php
namespace app\assets;
use yii\web\AssetBundle;
class CdnAsset extends AssetBundle
{
    public $css = [
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',
        'https://fonts.googleapis.com/icon?family=Material+Icons',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
    ];

    public $js = [
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD]; // Or POS_END based on your needs
}
