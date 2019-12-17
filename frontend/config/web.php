<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$cache = require __DIR__ . '/memcached.php';

$config = [
    'id' => 'pui2',
    'name'=>'echoCTF.RED Mycenae',
    'basePath' => dirname(__DIR__),
    'charset' => 'UTF-8',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
      'game' => [
          'class' => 'app\modules\game\Module',
      ],
      'challenge' => [
          'class' => 'app\modules\challenge\Module',
      ],
      'help' => [
            'class' => 'app\modules\help\Module',
        ],
      'target' => [
            'class' => 'app\modules\target\Module',
      ],
    ],
    'components' => [
      'assetManager' => [
          //'class'=>'app\components\echoCTFAssetManager',
          //'nullPublish'=>false,
          'bundles' => [
              'yii\bootstrap4\BootstrapAsset' => [
                'sourcePath' => null,
                'css' => [    ],
              ],
              'yii\validators\ValidationAsset' => [
                'sourcePath' => null,
                'js' => [
                    'js/yii.validation.js',
                ],

              ],
              'yii\widgets\ActiveFormAsset'=>[
                'sourcePath' => null,
                'js' => [
                    'js/yii.activeForm.js',
                ],
              ],
              'yii\grid\GridViewAsset'=>[
                'sourcePath' => null,
                'js' => [
                    'js/yii.gridView.js',
                ],
              ],
              'yii\web\YiiAsset' => [
                'sourcePath' => null,
                'js' => [
                    'js/yii.js',
                ],
              ],
              'yii\widgets\PjaxAsset'=>[
                'sourcePath' => null,
                'js' => [
                    'js/jquery.pjax.js',
                ],
              ],
              'yii\web\JqueryAsset' => [
                  'sourcePath' => null,
                  'js' => [
                      'js/jquery.js',
                  ],
              ],
              'yii\bootstrap\BootstrapPluginAsset' => [
                  'js'=>[]
              ],
              'yii\bootstrap\BootstrapAsset' => [
                  'css' => [],
              ],
              'app\assets\MaterialAsset' => [
                  'siteTitle' => '',
                  'logoMini' => '/images/logo-red-small.png',
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
            'cookieValidationKey' => 'D_m3LWxC7wb5HbELOx4IP4QrnMYCN_lN',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'sys'=> [
          'class' => 'app\components\Sysconfig',
        ],
        'cache' => $cache,
        'session'=>[
          'name' => 'red',
        ],
        'user' => [
            'identityClass' => 'app\models\Player',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-red', 'httpOnly' => true],
//            'autoUpdateFlash' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
//            'viewPath' => '@app/mail/layouts',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp-relay.gmail.com',
//                'username' => 'username',
//                'password' => 'password',
//                'port' => '25',
//                'encryption' => 'none',
//                'streamOptions' => [
//                  'ssl' => [
//                      'verify_peer' => false,
//                      'verify_peer_name' => false,
//                  ],
//                ],
            ],
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
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                // app/controllers/SiteController.php
                '' => 'site/index',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'register'=>'site/register',
                'request-password-reset'=>'site/request-password-reset',
                'reset-password' => 'site/reset-password',
                'resend-verification-email'=>'site/resend-verification-email',
                'verify-email'=>'site/verify-email',
                'changelog' => 'site/changelog',
                // app/modules/challenge/controllers/ChallengeController.php
                'challenges' => 'challenge/default/index',
                'challenge/<id:\d+>' => 'challenge/default/view',
                // app/modules/target/controllers/TargetController.php
                'target/<id:\d+>' => 'target/default/index',
                'target/<id:\d+>/spin'=>'target/default/spin',
                'claim'=>'target/default/claim',
                // app/controllers/ProfileController
                'profile/<id:\d+>' => 'profile/index',
                'profile/me'=>'profile/me',
                'profile/ovpn'=>'profile/ovpn',
                'profile/settings'=>'profile/settings',
                'profile/notifications'=>'profile/notifications',
                'profile/hints'=>'profile/hints',
                // app/controllers/DashboardController.php
                'dashboard' => 'dashboard/index',
                // HELP MODULE
                'help/faq' => 'help/faq/index',
                'faq' => 'help/faq/index',
                'help/rules' => 'help/rule/index',
                'rules' => 'help/rule/index',
                'help/instructions' => 'help/instruction/index',
                'instructions' => 'help/instruction/index',
                // app/controllers/LegalController.php
                'legal/terms-and-conditions'=>'legal/terms-and-conditions',
                'legal/privacy-policy'=>'legal/privacy-policy',
                //['class' => 'yii\rest\UrlRule', 'controller' => 'profile','only'=>['notifications']],
//                ['class' => 'yii\rest\UrlRule', 'controller' => 'rule'],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
//    $config['bootstrap'][] = 'debug';
//    $config['modules']['debug'] = [
//        'class' => 'yii\debug\Module',
//    // uncomment the following to add your IP if you are not connecting from localhost.
//        'allowedIPs' => ['127.0.0.1', '::1'],
//    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
  //      'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
