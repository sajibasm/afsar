<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
try {
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die('The .env file is missing or invalid.');
}

// Debug: Check loaded environment variables
$dotenv->required(['DB_DSN', 'DB_USERNAME', 'DB_PASSWORD'])->notEmpty();
$dotenv->required(['YII_DEBUG', 'YII_ENV'])->allowedValues(['true', 'false', 'dev', 'prod']);
// Explicitly set environment variables using putenv()
foreach ($_ENV as $key => $value) {
    putenv("$key=$value");
}

$config = require __DIR__ . '/../config/web.php';
(new yii\web\Application($config))->run();
