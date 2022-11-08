<?php

$params=require __DIR__.'/params.php';
$db=require __DIR__.'/db.php';

$config=[
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','sales'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
      'i18n' => [
        'translations' => [
          'yii' => [
            'class' => 'yii\i18n\PhpMessageSource',
          ],
          'app*' => [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/messages',
            'sourceLanguage' => 'en-US',
            'fileMap' => [
              'app' => 'app.php',
              'app/error' => 'error.php',
            ],
          ],
        ],
      ],
      'sales' => [
          'class' => 'app\modules\sales\Module',
      ],
      'sys'=> [
        'class' => 'app\components\Sysconfig',
      ],
      'mailer' => [
        'class' => 'app\components\Mailer',
        'transport' => [
          'dsn'=>'native://default',
        ],
      ],
      'cache' => [
          'class' => 'yii\caching\FileCache',
      ],
      'log' => [
          'targets' => [
              [
                  'class' => 'yii\log\FileTarget',
                  'levels' => ['error', 'warning'],
              ],
          ],
      ],
      'db' => $db,
    ],
    'params' => $params,
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationTable' => 'migration',
            'migrationPath' => '@app/migrations',
        ],
        'migrate-red' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationTable' => 'migration_red',
            'migrationPath' => '@app/migrations-red',
        ],
        'init_data' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationTable' => 'init_data',
            'migrationPath' => '@app/migrations-init',
        ],
      ],
];

if(YII_ENV_DEV)
{
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]='gii';
    $config['modules']['gii']=[
        'class' => 'yii\gii\Module',
    ];
}

return $config;
