<?php
$params=require __DIR__.'/params.php';
$db=require __DIR__.'/db.php';
$cache=require __DIR__.'/cache.php';
$cookieValidationKey=require __DIR__.'/validationKey.php';

$config=[
    'id' => 'pui2',
    //'language' => 'el-GR',
    'sourceLanguage' => 'en-US',
    'name'=>'echoCTF.RED Mycenae',
    'basePath' => dirname(__DIR__),
    'charset' => 'UTF-8',
    'bootstrap' => [
      'log',
      'app\extensions\MemcacheUrlManagerBootstrap',
      'subscription'
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@appconfig' => realpath(dirname(__FILE__)),
    ],
    'modules' => [
      'api' => [
          'class' => 'app\modules\api\Module',
      ],
      'subscription' => [
          'class' => 'app\modules\subscription\Module',
      ],
      'game' => [
          'class' => 'app\modules\game\Module',
      ],
      'challenge' => [
          'class' => 'app\modules\challenge\Module',
      ],
      'tutorial' => [
          'class' => 'app\modules\tutorial\Module',
      ],
      'help' => [
            'class' => 'app\modules\help\Module',
        ],
      'target' => [
            'class' => 'app\modules\target\Module',
      ],
      'network' => [
            'class' => 'app\modules\network\Module',
      ],
      'team' => [
          'class' => 'app\modules\team\Module',
      ],
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
      'assetManager' => [
          'bundles' => [
              'yii\captcha\CaptchaAsset' => [
                'sourcePath' => null,
                'js' => ['js/yii.captcha.min.js', ],
              ],
              'yii\bootstrap4\BootstrapAsset' => [
                'sourcePath' => null,
                'css' => [],
              ],
              'yii\validators\ValidationAsset' => [
                'sourcePath' => null,
                'js' => [
                    'js/yii.validation.min.js',
                ],

              ],
              'yii\widgets\ActiveFormAsset'=>[
                'sourcePath' => null,
                'js' => [
                    'js/yii.activeForm.min.js',
                ],
              ],
              'yii\grid\GridViewAsset'=>[
                'sourcePath' => null,
                'js' => [
                    'js/yii.gridView.min.js',
                ],
              ],
              'yii\web\YiiAsset' => [
                'sourcePath' => null,
                'js' => [
                    'js/yii.min.js',
                ],
              ],
              'yii\widgets\PjaxAsset'=>[
                'sourcePath' => null,
                'js' => [
                    'js/jquery.pjax.min.js',
                ],
              ],
              'yii\web\JqueryAsset' => [
                  'sourcePath' => null,
                  'js' => [
                      'js/jquery.min.js',
                  ],
              ],
              'yii\bootstrap\BootstrapPluginAsset' => [
                'sourcePath' => null,
                  'js'=>[]
              ],
              'yii\bootstrap\BootstrapAsset' => [
                  'sourcePath' => null,
                  'css' => [],
              ],
              'app\assets\MaterialAsset' => [
                  'siteTitle' => '',
                  'logoMini' => '/images/logo-small.png',
                  'sidebarColor' => 'echoctf',
                  'sidebarBackgroundColor' => 'black',
              ],
          ],
        ],
        'view' => [
            'class' => 'app\components\echoCTFView',
            'theme' => [
                'basePath' => '@app/themes/material',
                'baseUrl' => '@web/themes/material',
                'pathMap' => [
                    '@app/views' => '@app/themes/material',
                    '@app/modules' => '@app/themes/material/modules',
                ],
            ],
        ],
        'request' => [
            'csrfParam' => '_csrf-red',
// Hard code the domain to avoid parsing HTTP_HOST
//            'hostInfo'=>'https://echoctf.red',
            'enableCsrfValidation' => true,
            'enableCsrfCookie'=>false,
            'csrfCookie'=>['httpOnly'=>true],
            'cookieValidationKey' => $cookieValidationKey,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'sys'=> [
          'class' => 'app\components\Sysconfig',
        ],
        'counters'=> [
          'class' => 'app\components\Counters',
        ],
        'DisabledRoute'=> [
          'class' => 'app\components\DisabledRoute',
        ],
        'cache' => $cache,
        'session'=>[
          'name' => 'red',
          'timeout'=>3600 * 12,
          'cookieParams'=>[
            'sameSite'=> 'Strict',
            'httpOnly'=>true
          ],
        ],
        'user' => [
            //'class' => '\app\components\User',
            'identityClass' => '\app\models\Player',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-red', 'httpOnly' => true, /*'sameSite'=>'Lax'*/],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'app\components\Mailer',
            'transport' => [
                'dsn'=>'native://default',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'except' => ['yii\web\HttpException:404'],
                ],
//                [
//                  'class' => 'yii\log\FileTarget',
//                  'logFile' => '@runtime/logs/profile.log',
//                  'logVars' => [],
//                  'levels' => ['profile'],
//                  'categories' => ['yii\db\Command::query'],
//                  'prefix' => function($message) {
//                      return '';
//                  }
//                ]
            ],

        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true, // only rule urls are valid
            'showScriptName' => false,
            'rules' => [
                // app/controllers/SiteController.php
                '' => 'site/index',
                '/' => 'site/index',
            ],
        ],
    ],
    'params' => $params,
    'on beforeRequest' => function ($event) {
      if(\Yii::$app->sys->force_https_urls!==false){
        $_SERVER['HTTPS']='on';
      }
      if (\Yii::$app->sys->maintenance === true) {
          if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin ) {
              Yii::$app->catchAll = [ 'site/maintenance' ];
          }
      }
    },
    'on afterRequest' => function() {
      try {
        if (!Yii::$app->user->isGuest) {
          \Yii::$app->cache->memcache->set("last_seen:".\Yii::$app->user->id, time());
          \Yii::$app->cache->memcache->set("online:".\Yii::$app->user->id, time(), intval(\Yii::$app->sys->online_timeout));
          \Yii::$app->cache->memcache->set("player_session:".\Yii::$app->user->id, \Yii::$app->session->id, intval(\Yii::$app->sys->online_timeout));
          \Yii::$app->cache->memcache->set("player_frontend_ip:".\Yii::$app->user->id, \Yii::$app->request->remoteIP);
          return;
        }
      } catch (\Exception $e) { }
    },
];

/*
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
//        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
//        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}
*/
return $config;
