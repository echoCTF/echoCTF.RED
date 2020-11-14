<?php

$params=require __DIR__.'/params.php';
$db=require __DIR__.'/db.php';
$cache_config=require __DIR__.'/cache.php';
$cookieValidationKey=require __DIR__.'/validationKey.php';
$config=[
    'id' => 'basic',
    'name'=>'echoCTF mUI',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'smartcity' => [
            'class' => 'app\modules\smartcity\Module',
        ],
        'restapi' => [
            'class' => 'app\modules\restapi\Module',
        ],
        'settings' => [
            'class' => 'app\modules\settings\Module',
        ],
        'frontend' => [
            'class' => 'app\modules\frontend\Module',
        ],
        'gameplay' => [
            'class' => 'app\modules\gameplay\Module',
        ],
        'activity' => [
            'class' => 'app\modules\activity\Module',
        ],
    ],
    'components' => [
      'sys'=> [
        'class' => 'app\components\Sysconfig',
      ],
      'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'useFileTransport' => false,
        'viewPath' => '@app/mail/layouts',
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'port' => '25',
        ],
      ],
        'session' => [
          'class' => 'yii\web\DbSession',
          'sessionTable' => 'muisess',
        ],
        'formatter' => [
            'class' => 'app\models\AppFormatter',
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'cookieValidationKey' => $cookieValidationKey,
        ],
        'cache' => $cache_config,
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
            /*'enablePrettyUrl' => true,
            'showScriptName' => false,*/
            'rules' => [
              ['class' => 'yii\rest\UrlRule', 'controller' => ['app\modules\restapi\controllers\target']],
              ['class' => 'yii\rest\UrlRule', 'controller' => ['app\modules\restapi\controllers\targetvariable']],
              ['class' => 'yii\rest\UrlRule', 'controller' => ['app\modules\restapi\controllers\finding']],
              ['class' => 'yii\rest\UrlRule', 'controller' => ['app\modules\restapi\controllers\treasure']],
              ['class' => 'yii\rest\UrlRule', 'controller' => ['app\modules\restapi\controllers\hint']],
              ['class' => 'yii\rest\UrlRule', 'controller' => ['app\modules\restapi\controllers\syncfiles']],
            ],
        ],
    ],
    'params' => $params,
];

if(YII_ENV_DEV)
{
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]='debug';
    $config['modules']['debug']=[
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '172.23.0.1'],
    ];

    $config['bootstrap'][]='gii';
    $config['modules']['gii']=[
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '172.23.0.1'],
    ];
}
return $config;
