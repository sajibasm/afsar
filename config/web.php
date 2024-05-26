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
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],

    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or 'yii\rbac\PhpManager'
        ],

        'user' => [
            'identityClass' => 'app\models\User', // Your User model class
            'loginUrl' => ['site/login'],
        ],
        'as access' => [
            'class' => 'mdm\admin\components\AccessControl',
            'allowActions' => [
                // The actions listed here will be allowed to everyone including guests.
                // So, 'site/*' should not appear here in a real application.
                'site/*',
                'admin/*',
            ],
        ],

//        'asm' => [
//            'class' => 'app\modules\asm\components\ASM',
//        ],

//        'user' => [
//            'identityClass' => 'app\models\User',
//            'enableAutoLogin' => true,
//        ],

        'view' => [
            'theme' => [
                'basePath' => '@app/themes/adminlte',
                'baseUrl' => '@web/themes/adminlte',
                'pathMap' => [
                    '@app/views' => '@app/themes/adminlte',
                ]
            ]
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


        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

//        'mailer' => [
//            'class' => 'yii\swiftmailer\Mailer',
//            // send all mails to a file by default. You have to set
//            // 'useFileTransport' to false and configure a transport
//            // for the mailer to send real emails.
//            'useFileTransport' => true,
//        ],


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

        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            //'viewPath' => '@backend/mail',
            'useFileTransport' => false,//set this property to false to send mails to real email addresses
            //comment the following array to send mail using php's mail function
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => getenv('SMTP_HOST'),
                'username' => getenv('SMTP_USER_NAME'),
                'password' => getenv('SMTP_PASSWORD'),
                'port' => getenv('SMTP_PORT'),
                'encryption' => getenv('SMTP_ENCRYPTION'),
            ]
        ],

        'assetManager' => [
            'bundles' => [
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
        ],

//        'asm' => [
//            'class' => 'app\modules\asm\Module',
//            'defaultRoute' => 'modules',
//            'redis' => true,
//        ],

        'gridview' => [
            'class' => '\kartik\grid\Module',
            //'bsVersion' => '5.x', // or '3.x'
            'downloadAction' => 'gridview/export/download',
            // 'i18n' => [],
            'exportEncryptSalt' => 'tG85vd1',
        ],

        'dynagrid' => [
            'class' => '\kartik\dynagrid\Module',
            // other module settings
        ],
//        'admin' => [
//            'class' => 'app\modules\admin\Module',
//            //'layout'=>'@app/themes/adminlte/layouts/main'
//        ]
    ],

//    'as access' => [
//        'class' => 'mdm\admin\components\AccessControl',
//        'allowActions' => $allowedRoutes,
//    ],

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
