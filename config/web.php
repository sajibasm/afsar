<?php

use yii\helpers\VarDumper;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$allowActions = require __DIR__ . '/allowed_url.php';


$config = [
    'id' => 'Axial360',
    'name' => 'Axial360',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue', 'admin'],
    'aliases' => [
        '@mdm/admin' => '@app/extensions/yii2-admin', // adjust this path to your actual extracted directory
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],

    'modules' => [

        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'left-menu', // You can also use 'right-menu' or your custom layout
            'mainLayout' => '@app/views/layouts/main.php',

            'menus' => [
                'assignment' => [
                    'label' => 'Grant Access', // change label
                    'icon' => 'fa fa-user-shield' // ✅ Font Awesome class
                ],
                //'route' => true, // disable menu
            ],

            'controllerMap' => [
                'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    'userClassName' => 'app\models\User',
                    'idField' => 'user_id',
                    'usernameField' => 'username',
                    'extraColumns' => [
                        [
                            'attribute' => 'full_name',
                            'label' => 'Full Name',
                            'value' => function($model, $key, $index, $column) {
                                return $model->first_name.' '.$model->last_name;
                            },
                        ],
                    ],
                    'searchClass' => 'app\models\UserSearch'
                ],
            ],
        ],

        'gridview' => [
            'class' => '\kartik\grid\Module',
            //'bsVersion' => '5.x', // or '3.x'
            'downloadAction' => 'gridview/export/download',
            // 'i18n' => [],
            'exportEncryptSalt' => 'tG85vd1',
        ],
    ],

    'components' => [

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],

        'user' => [
            'identityClass' => 'mdm\admin\models\User', // or another fully-qualified class path
            'loginUrl' => ['admin/user/login'],
        ],

        'userLoginLogger' => [
            'class' => 'app\components\UserLoginLogger',
        ],

        'view' => [
            'theme' => [
                'basePath' => '@app/themes/adminlte',
                'baseUrl' => '@web/themes/adminlte',
                'pathMap' => [
                    '@app/views' => '@app/themes/adminlte',
                ]
            ]
        ],

        'recaptchaV3' => [
            'class' => 'Baha2Odeh\RecaptchaV3\RecaptchaV3',
            'site_key' => getenv('GOOGLE_CAPTCHA_SITE_KEY'),
            'secret_key' => getenv('GOOGLE_CAPTCHA_SECRET_KEY'),
            'verify_ssl' => false, // default is true
        ],

        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'mNBNb3SDKL5Iqbi__fukexv7zR8sknJx',
            'enableCookieValidation' => true,
            'enableCsrfValidation' => false,
        ],

        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'database' => getenv('REDIS_DATABASE'),
        ],

        'session' => [
            'class' => 'yii\redis\Session',
        ],

        'cache' => [
//            'class' => 'yii\redis\Cache',
            'class' => 'yii\caching\FileCache',
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            //'timeZone' => 'Asia/Dhaka',
            'dateFormat' => 'php:d-m-Y',
            'datetimeFormat' => 'php:d-m-Y h:i A',
            'timeFormat' => 'php:h:i A',

            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
            'currencyCode' => null,
//            'numberFormatterSymbols'=>[\NumberFormatter::CURRENCY_SYMBOL => null],
//            'numberFormatterOptions' => [
//                \NumberFormatter::MIN_FRACTION_DIGITS => 0,
//                \NumberFormatter::MAX_FRACTION_DIGITS => 0,
//            ]
        ],

        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => false, // Set this to false to send real emails
            'transport' => [
                'dsn' => 'smtp://'.getenv('SMTP_USER_NAME').':'.getenv('SMTP_PASSWORD').'@'.getenv('SMTP_HOST').':'.getenv('SMTP_PORT').''
            ],
        ],

        'assetManager' => [
            'bundles' => [
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue'
                ],
            ],
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // your rules go here
            ],
        ],

        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => $db, // DB connection component or its config
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex used to sync queries
        ],
    ],

    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => $allowActions
    ],

    'params' => $params,
];

if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'] = ['debug', 'gii'];
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    if (!function_exists('dd')) {
        function dd($variable, $depth = 10, $highlight = true)
        {
            VarDumper::dump($variable, $depth, $highlight); // depth, highlight
            die();
        }
    }
}

return $config;
