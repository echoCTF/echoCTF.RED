<?php

$params=require __DIR__.'/params.php';
$db=require __DIR__.'/db.php';
$cache=require __DIR__.'/cache.php';

$config=[
    'id' => 'basic-console',
//    'language' => 'el-GR',
    'sourceLanguage' => 'en-US',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
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
        'sys'=> [
          'class' => 'app\components\Sysconfig',
        ],
        'cache' => $cache,
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
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
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
