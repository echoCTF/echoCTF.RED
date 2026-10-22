<?php

namespace app\modules\api\actions;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\Target;
use app\modules\network\models\NetworkPlayer;
use \yii\web\NotFoundHttpException;

class SpinRestAction extends \yii\rest\ViewAction
{
  public $modelClass = "\app\models\PlayerSpin";
  public $serializer = [
    'class' => 'yii\rest\Serializer',
    'collectionEnvelope' => 'items',
  ];

  public function run($id)
  {

    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    \Yii::$app->response->statusCode = 200;
    try {
      if (!\Yii::$app->cache->memcache->add("api_target_spin:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_target_spin_timeout) + 1)) {
        \Yii::$app->response->statusCode = 429;
        return ["message" => \Yii::t('app', "Rate-limited, wait a few seconds and try again."), "code" => 0, "status" => \Yii::$app->response->statusCode];
      }

      \app\modules\target\models\SpinQueue::queue(Yii::$app->user->identity, (int) $id);
      \Yii::$app->response->statusCode = 201;
      \Yii::$app->response->data = ["message" => \Yii::t('app', "Queued target for spin"), "code" => 0, "status" => \Yii::$app->response->statusCode];
    } catch (\Exception $e) {
      \Yii::$app->response->statusCode = 422;
      \Yii::$app->response->data = ["message" => $e->getMessage(), "code" => 0, "status" => \Yii::$app->response->statusCode];
    }
  }
}
