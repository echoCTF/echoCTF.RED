<?php

namespace app\modules\api\actions;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\TargetInstance;
use app\modules\target\models\Target;
use yii\web\NotFoundHttpException;
use yii\base\UserException;
use yii\helpers\Url;

class SpawnRestAction extends \yii\rest\ViewAction
{
  public $modelClass = '\app\modules\target\models\TargetInstance';
  public $serializer = 'yii\rest\Serializer';

  public function run($id)
  {

    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    \Yii::$app->response->statusCode = 200;

    try {
      if (!\Yii::$app->cache->memcache->add("api_target_spawn:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_target_spawn_timeout) + 1)) {
        \Yii::$app->response->statusCode = 429;
        return ["message" => \Yii::t('app', "Rate-limited, wait a few seconds and try again."), "code" => 0, "status" => \Yii::$app->response->statusCode];
      }

      [$status] = TargetInstance::spawnFor(Yii::$app->user->identity, (int) $id);
      [$code, $message] = [
        'old_shutdown' => [201, \Yii::t('app', "Scheduled existing instance to shutdown")],
        'restarted' => [200, \Yii::t('app', "Scheduled instance restart.")],
        'spawned' => [201, \Yii::t('app', "Scheduled spawn of private target instance.")],
      ][$status];
      \Yii::$app->response->statusCode = $code;
      \Yii::$app->response->data = ["message" => $message, "code" => 0, "status" => $code];
    } catch (\Throwable $e) {
      \Yii::$app->response->statusCode = $e instanceof \yii\web\HttpException ? $e->statusCode : 422;
      \Yii::$app->response->data = ["message" => $e->getMessage(), "code" => 0, "status" => \Yii::$app->response->statusCode];
    }
  }
}
