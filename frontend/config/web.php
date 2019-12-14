<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'pui2',
    'name'=>'echoCTF.RED Mycenae',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
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
          'class'=>'app\components\echoCTFAssetManager',
          'nullPublish'=>false,
          'bundles' => [
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
                  //'sidebarBackgroundImage' => ''
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
                    '@app/modules' => '@app/themes/material/modules', // <-- !!!
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'D_m3LWxC7wb5HbELOx4IP4QrnMYCN_lN',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'sys'=> [
          'class' => 'app\components\Sysconfig',
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 60,
                    'persistent'=>true,
                    //'useMemcached'=>true,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\Player',
            'enableAutoLogin' => true,
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
                '' => 'site/index',
                'about' => 'site/about',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'challenges' => 'challenge/default/index',
                'challenge/<id:\d+>' => 'challenge/default/view',
                'target/<id:\d+>' => 'target/default/index',
                'profile/<id:\d+>' => 'profile/default/index',
                'dashboard' => 'dashboard/index',
                'help/faq' => 'help/faq/index',
                'faq' => 'help/faq/index',
                'help/rules' => 'help/rule/index',
                'rules' => 'help/rule/index',
                'help/instructions' => 'help/instruction/index',
                'instructions' => 'help/instruction/index',
                'claim'=>'target/default/claim',
                'profile/<id:\d+>'=>'profile/index',
                'profile/me'=>'profile/me',
                'profile/ovpn'=>'profile/ovpn',
                'profile/settings'=>'profile/settings',
                'profile/notifications'=>'profile/notifications',
                'profile/hints'=>'profile/hints',
                'register'=>'site/register',
                'site/request-password-reset'=>'site/request-password-reset',
                'site/reset-password' => 'site/reset-password',
                'site/resend-verification-email'=>'site/resend-verification-email',
                'site/verify-email'=>'site/verify-email',
                'changelog' => 'site/changelog'
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
//
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
  //      'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
