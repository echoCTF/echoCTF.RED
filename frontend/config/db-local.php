<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=echoCTF',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'enableProfiling'=>true,
    'enableProfiling'=>true,
    // Schema cache options (for production environment)
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
//    'on afterOpen' => function($event) {
//        $event->sender->createCommand("SET time_zone='+00:00'")->execute();
//    },
];
