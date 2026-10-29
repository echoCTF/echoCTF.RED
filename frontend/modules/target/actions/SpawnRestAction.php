<?php

namespace app\modules\target\actions;

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

  public function run($id, $team = false)
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $goback = Url::previous();
    if ($goback === '/')
      $goback = ['/target/default/view', 'id' => $id];
    try {
      [$status, $ti] = TargetInstance::spawnFor(Yii::$app->user->identity, (int) $id, $team !== false);
      if ($status === 'old_shutdown')
        Yii::$app->session->setFlash('warning', sprintf(\Yii::t('app', 'Scheduling shutdown for old [%s] instance. You will receive a notification when the instance is shut.'), $ti->target->name));
      elseif ($status === 'restarted')
        Yii::$app->session->setFlash('success', sprintf(\Yii::t('app', 'Target instance [%s] scheduled for restart. You will receive a notification when the operation is completed.'), $ti->target->name));
      else
        Yii::$app->session->setFlash('success', sprintf(\Yii::t('app', 'Spawning new instance for [%s]. You will receive a notification when the instance is up.'), $ti->target->name));
    } catch (\Throwable $e) {
      Yii::$app->session->setFlash('error', $e->getMessage());
    }

    return Yii::$app->controller->redirect($goback);
  }
}
