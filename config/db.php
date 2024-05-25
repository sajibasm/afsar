<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => getenv('DB_DSN'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'charset' => 'utf8',
    'on afterOpen' => function($event) {
        $event->sender->createCommand("SET sql_mode = ''")->execute();
    },

//    'enableSchemaCache' => true,
//    // Duration of schema cache.
//    'schemaCacheDuration' => 3600,
//    // Name of the cache component used to store schema information
//    'schemaCache' => 'cache',
//    'enableQueryCache'=>true,
//    'queryCacheDuration'=>3600,
//    'queryCache'=>'cache',

];
