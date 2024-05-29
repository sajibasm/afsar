<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
Yii::setAlias('@modules', dirname(dirname(__FILE__)) . '/modules/');



$config = [
    'id' => 'ASL-Inventory',
    'name' => 'Axial Inventory',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue', 'admin'],
    'aliases' => [
        //'@mdm/admin' => '@app/extensions/yii2-admin', // adjust this path to your actual extracted directory
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],


    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'debug/*',
            'admin/*',

            'site/login',
            'site/chart',
            'site/daily-summery',
            'site/index',
            'site/sales-growth',
            'site/analytics',
            'site/permission',

//            'sales/index',
//            'sales/outlet',
//            'sales/get-brand-list-by-item',
//            'sales/get-size-list-by-brand',
//            'sales/get-product-price',
//            'sales/check-available-product',
//            'sales/customer-details',
//            'sales/draft-update',
//            'sales/invoice-item-update-restore',
//            'sales/invoice-item-update-delete',
//            'sales/invoice-item-delete',
//            'sales/cancel-sales-invoice',
//            'sales/cancel-update-invoice',
//            'sales/delete-invoice',
//            'sales/remove-invoice',
//            'sales/restore',
//
//            'product-stock/outlet',
//            'product-stock/received-view',
//            'product-stock/received-approved',
//            'product-stock/restore-approved',
//            'product-stock/get-item-by-brand',
//            'product-stock/get-brand-list-by-item',
//            'product-stock/get-size-list-by-brand',
//            'product-stock/get-product-price',
//            'product-stock/existing-price',
//            'product-stock/stock-delete-all',
//            'product-stock/discard',
//            'product-stock/stock-delete-all',
//
//            'product-stock-movement/get-item-by-brand',
//            'product-stock-movement/get-brand-list-by-item',
//            'product-stock-movement/get-size-list-by-brand',
//            'product-stock-movement/product-details-by-size-id',
//            'product-stock-movement/get-product-price'

        ]
    ],

    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
            'identityClass' => 'app\models\User', // Your User model class
            'loginUrl' => ['site/login'],
            //'loginUrl' => ['admin/user/login'],
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

        'recaptcha' => [
            'class' => 'richweber\recaptcha\ReCaptcha',
            'siteKey' => getenv('GOOGLE_CAPTCHA_SITE_KEY'),
            'secretKey' => getenv('GOOGLE_CAPTCHA_SECRET_KEY'),
            'errorMessage' => 'Are you robot?',
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
            'class' => 'yii\redis\Cache',
            //'class' => 'yii\caching\FileCache',
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
            //'numberFormatterSymbols'=>[\NumberFormatter::CURRENCY_SYMBOL => null],
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

    'modules' => [

        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'left-menu', // You can also use 'right-menu' or your custom layout
            //'mainLayout' => '@app/views/layouts/main.php',

            'menus' => [
                'assignment' => [
                    'label' => 'Grant Access' // change label
                ],
                //'route' => true, // disable menu
            ],

            'controllerMap' => [
                'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    /* 'userClassName' => 'app\models\User', */
                    'idField' => 'user_id',
                    'usernameField' => 'username',
                    //'fullnameField' => 'profile.full_name',
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

    function dd($var, $flag = true)
    {
        echo '<pre>';
        if ($flag) {
            print_r($var);
        } else {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

return $config;
