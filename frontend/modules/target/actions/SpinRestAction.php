<?php

namespace app\modules\target\actions;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\Target;
use app\modules\network\models\NetworkPlayer;
use \yii\web\NotFoundHttpException;

class SpinRestAction extends \yii\rest\ViewAction
{
  public $modelClass = "\app\models\PlayerSpin";
  public $serializer = 'yii\rest\Serializer';

  public function run($id)
  {

    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    try {
      $target = \app\modules\target\models\SpinQueue::queue(Yii::$app->user->identity, (int) $id);
      $msg = \Yii::t('app', "Target [%s] queued for reboot. You will receive a notification when the operation is completed.");
      if ($target->ondemand && $target->ondemand->state === -1)
        $msg = \Yii::t('app', "Target [%s] queued to power-up. You will receive a notification when the operation is completed.");
      Yii::$app->session->setFlash('success', sprintf($msg, $target->name));
    } catch (\Throwable $e) {
      Yii::$app->session->setFlash('error', $e->getMessage());
    }
    return $this->redirectTo();
  }

  protected function redirectTo()
  {
    if (Yii::$app->request->referrer) {
      return Yii::$app->getResponse()->redirect(Yii::$app->request->referrer);
    }
    return Yii::$app->getResponse()->redirect(['/target/default/index']);
  }
}
