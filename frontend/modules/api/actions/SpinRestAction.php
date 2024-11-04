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
      if (\Yii::$app->cache->memcache->get("api_target_spin:" . \Yii::$app->user->id) !== false) {
        \Yii::$app->response->statusCode = 429;
        return ["message"=>\Yii::t('app',"Rate-limited, wait a few seconds and try again."),"code"=>0,"status"=>\Yii::$app->response->statusCode];
      }
      \Yii::$app->cache->memcache->set("api_target_spin:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_target_spin_timeout) + 1);

      $target = $this->findModelProgress($id);
      $module = \Yii::$app->getModule('target');

      $module->checkNetwork($target);

      if (Yii::$app->user->identity->instance !== NULL && Yii::$app->user->identity->instance->target_id === $target->id) {
        Yii::$app->user->identity->instance->updateAttributes(['reboot' => 1]);
        \Yii::$app->response->statusCode = 201;
        return ["message"=>\Yii::t('app',"Instance scheduled for reboot"),"code"=>0,"status"=>\Yii::$app->response->statusCode];;
      }

      $this->checkSpinable($target);

      $playerSpin = Yii::$app->user->identity->profile->spins;
      $SQ = new \app\modules\target\models\SpinQueue;
      $SQ->player_id = (int) \Yii::$app->user->id;
      $SQ->target_id = $target->id;
      $playerSpin->counter = intval($playerSpin->counter) + 1;
      $playerSpin->total = intval($playerSpin->total) + 1;
      if ($SQ->save() !== false && $playerSpin->save() !== false) {
        \Yii::$app->response->statusCode = 201;
        \Yii::$app->response->data=["message"=>\Yii::t('app',"Queued target for spin"),"code"=>0,"status"=>\Yii::$app->response->statusCode];
      } else
        throw new NotFoundHttpException(\Yii::t('app', 'Failed to queue target for restart.'));
    } catch (\Exception $e) {
      \Yii::$app->response->statusCode = 422;
      \Yii::$app->response->data=["message"=>$e->getMessage(),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }
  }

  protected function findModelProgress($id)
  {
    if (($model = Target::find()->player_progress(\Yii::$app->user->id)->where(['t.id' => $id])->one()) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(\Yii::t('app', 'The requested target does not exist.'));
  }

  protected function checkSpinable($target)
  {
    if ($target->spinable !== true)
      throw new NotFoundHttpException(\Yii::t('app', 'Not allowed to spin target. Target cannot not be spined.'));
  }
}
