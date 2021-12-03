<?php

$params=require __DIR__.'/params.php';
$db=require __DIR__.'/db.php';
$cache=require __DIR__.'/cache.php';
$cookieValidationKey=require __DIR__.'/validationKey.php';

$config=[
    'id' => 'pui2',
    'name'=>'echoCTF.RED Mycenae',
    'basePath' => dirname(__DIR__),
    'charset' => 'UTF-8',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@appconfig' => realpath(dirname(__FILE__)),
    ],
    'modules' => [
      'api' => [
          'class' => 'app\modules\api\Module',
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
        'DisabledRoute'=> [
          'class' => 'app\components\DisabledRoute',
        ],
        'cache' => $cache,
        'session'=>[
          'name' => 'red',
          'cookieParams'=>[
            //'sameSite'=> 'Lax',
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
//            'useFileTransport' => defined(YII_ENV_DEV),
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
                    'levels' => ['error'],
                    'except' => ['yii\web\HttpException:404'],
                ],
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
                'challenge/<id:\d+>/download' => 'challenge/default/download',
                'challenge/<id:\d+>/rate' => 'game/default/rate-solver',
                // app/modules/target/controllers/TargetController.php
                'target/<id:\d+>/rate' => 'game/default/rate-headshot',
                'targets' => 'target/default/index',
                'target/<id:\d+>' => 'target/default/view',
                'target/<id:\d+>/badge' => 'target/default/badge',
                'target/<id:\d+>/spin'=>'target/default/spin',
                'target/<id:\d+>/writeups/enable' => 'target/writeup/enable',
                'target/<id:\d+>/writeup/submit' => 'target/writeup/submit',
                'target/<id:\d+>/writeup/view' => 'target/writeup/view',
                'target/<id:\d+>/writeup/update' => 'target/writeup/update',
                //'target/<id:\d+>/vs/<profile_id:\d+>/badge'=>'target/default/versusBadge',
                //'target/<id:\d+>/rate'=>'target/default/rate',
                'claim'=>'target/default/claim',
                // app/controllers/ProfileController
                'profile/<id:\d+>' => 'profile/index',
                'profile/<id:\d+>/badge' => 'profile/badge',
                'p/<id:\d+>' => 'profile/index',
                'p/<id:\d+>/badge' => 'profile/badge',
                'profile/me'=>'profile/me',
                'profile/ovpn'=>'profile/ovpn',
                'profile/settings'=>'profile/settings',
                //'profile/notifications'=>'profile/notifications',
                //'profile/hints'=>'profile/hints',
//                'profile/robohash' => 'profile/robohash',
                // app/controllers/DashboardController.php
                'dashboard' => 'dashboard/index',
                // HELP MODULE
                'help' => 'help/default/index',
                'faq' => 'help/faq/index',
                'help/faq' => 'help/faq/index',
                'rules' => 'help/rule/index',
                'help/rules' => 'help/rule/index',
                'instructions' => 'help/instruction/index',
                'help/instructions' => 'help/instruction/index',
                'help/experience' => 'help/experience/index',
                // app/controllers/LegalController.php
                'terms_and_conditions'=>'legal/terms-and-conditions',
                'legal/terms-and-conditions'=>'legal/terms-and-conditions',
                'privacy_policy'=>'legal/privacy-policy',
                'legal/privacy-policy'=>'legal/privacy-policy',
                'site/captcha'=>'site/captcha',
                'target/<id:\d+>/vs/<profile_id:\d+>'=>'target/default/versus',
                'target/<id:\d+>/versus/<profile_id:\d+>'=>'target/default/versus',
                'tutorials' => 'tutorial/default/index',
                'tutorial/<id:\d+>' => 'tutorial/default/view',
                'leaderboards' => 'game/leaderboards/index',
                // Team Module rules
                'team' => 'team/default/index',
                'team/create' => 'team/default/create',
                'team/update' => 'team/default/update',
                'team/join/<token>' => 'team/default/join',
                'team/invite/<token>' => 'team/default/invite',
                'team/approve/<id:\d+>' => 'team/default/approve',
                'team/reject/<id:\d+>' => 'team/default/reject',
                // Network Module
                'networks' => 'network/default/index',
                'network/<id:\d+>' => 'network/default/view',
                // API Module
                'api/headshots' => 'api/headshot/index',
                //['class' => 'yii\rest\UrlRule', 'controller' => 'api/headshot'],
            ],
        ],
    ],
    'params' => $params,
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
