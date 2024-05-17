<?php

$params=require __DIR__.'/params.php';
$db=require __DIR__.'/db.php';
$cache_config=require __DIR__.'/cache.php';
$cookieValidationKey=require __DIR__.'/validationKey.php';
$config=[
    'id' => 'basic',
    'name'=>'echoCTF mUI',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','sales'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'sales' => [
            'class' => 'app\modules\sales\Module',
        ],
        'content' => [
            'class' => 'app\modules\content\Module',
        ],
        'infrastructure' => [
            'class' => 'app\modules\infrastructure\Module',
        ],
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
      'i18n' => [
        'translations' => [
          'yii*' => [
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
      'mailer' => [
        'class' => 'app\components\Mailer',
        'transport' => [
            'dsn'=>'native://default',
        ],
      ],
      'session' => [
        'class' => 'yii\web\DbSession',
        'timeout'=>3600 * 1,
        'sessionTable' => 'muisess',
      ],
      'formatter' => [
          'class' => 'app\models\AppFormatter',
          'nullDisplay' => '<span class="not-set small">(empty)</span>',
      ],
      'request' => [
          'parsers' => [
              'application/json' => 'yii\web\JsonParser',
          ],
          'cookieValidationKey' => $cookieValidationKey,
          'enableCsrfCookie' => false,
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
                  'categories' => ['yii\swiftmailer\Logger::add'],
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
    'container' => [
        'definitions' => [
            \yii\widgets\LinkPager::class => \yii\bootstrap5\LinkPager::class,
            'yii\grid\ActionColumn'=> [
               'contentOptions' => ['style' => ['white-space' => 'nowrap']],
            ]
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
