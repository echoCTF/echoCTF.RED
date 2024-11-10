<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;

class ProfileController extends \yii\rest\ActiveController
{
  public $modelClass = 'app\models\Profile';
  public $serializer = [
    'class' => 'yii\rest\Serializer',
    'collectionEnvelope' => 'items',
  ];
  public function behaviors()
  {
    \Yii::$app->user->enableSession = false;
    \Yii::$app->user->loginUrl = null;

    return ArrayHelper::merge(parent::behaviors(), [
      'authenticator' => [
        'authMethods' => [
          HttpBearerAuth::class,
        ],
      ],
      'content' => [
        'class' => yii\filters\ContentNegotiator::class,
        'formats' => [
          'application/json' => \yii\web\Response::FORMAT_JSON,
        ],
      ],
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [ //api_bearer_disable
            'allow' => false,
            'matchCallback' => function () {
              return \Yii::$app->sys->api_bearer_enable !== true;
            }
          ],
          [
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
    ]);
  }

  public function actions()
  {
    $actions = parent::actions();
    // disable the "delete", "create", "view","update" actions
    unset($actions['delete'], $actions['create'], $actions['update'], $actions['index'], $actions['view']);

    return $actions;
  }

  public function actionMe()
  {
    $profile = array_merge(['id' => null, 'username' => Yii::$app->user->identity->username, 'bio' => null, 'vip' => null, 'admin' => null, 'onVPN' => null, 'vpn_ip' => null], Yii::$app->user->identity->profile->attributes);
    unset(
      $profile['gdpr'],
      $profile['htb'],
      $profile['terms_and_conditions'],
      $profile['mail_optin'],
      $profile['updated_at'],
      $profile['approved_avatar'],
      $profile['echoctf'],
      $profile['player_id'],
      $profile['created_at'],
    );
    $profile['created_at'] = Yii::$app->user->identity->created;
    $profile['vip'] = Yii::$app->user->identity->isVip;
    $profile['admin'] = Yii::$app->user->identity->isAdmin;
    $profile['onVPN'] = Yii::$app->user->identity->onVPN;
    $profile['vpn_ip'] = Yii::$app->user->identity->vpnIP;
    return $profile;
  }
}
