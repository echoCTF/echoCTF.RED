<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=echoCTF',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'enableProfiling'=>true,
    //'enableParamLogging'=>true,
    // Schema cache options (for production environment)
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600*24,
    'schemaCache' => 'cache',
    'enableQueryCache' => true,
    'queryCache'=>'qcache',
    'queryCacheDuration'=>60,
//    'on afterOpen' => function($event) {
//        $event->sender->createCommand("SET time_zone='+00:00'")->execute();
//    },
];
