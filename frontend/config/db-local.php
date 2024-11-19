<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=echoCTF',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    //'enableProfiling'=>false,
    //'enableParamLogging'=>true,
    // Schema cache options (for production environment)
    'enableSchemaCache' => true,
    'enableQueryCache' => true,
    'schemaCacheDuration' => 0,
    'queryCacheDuration'=>60,
    // You can have multiple caches defined for queries
    //'queryCache'=>'qcache',
    //'schemaCache' => 'cache',
//    'on afterOpen' => function($event) {
//        $event->sender->createCommand("SET time_zone='+00:00'")->execute();
//    },
];
