<?php
namespace app\modules\target\actions;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\Target;
use app\modules\network\models\NetworkPlayer;
use \yii\web\NotFoundHttpException;
class SpinRestAction extends \yii\rest\ViewAction
{
  public $modelClass="\app\models\PlayerSpin";
  public $serializer='yii\rest\Serializer';

  public function run($id)
  {

    \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
    try
    {
      $target=$this->findModelProgress($id);
      $module = \app\modules\target\Module::getInstance();

      $module->checkNetwork($target);

      if(Yii::$app->user->identity->instance !== NULL && Yii::$app->user->identity->instance->target_id===$target->id)
      {
        Yii::$app->user->identity->instance->updateAttributes(['reboot'=>1]);
        Yii::$app->session->setFlash('success', sprintf(\Yii::t('app','Target instance [%s] scheduled for restart. You will receive a notification when the operation is completed.'), $target->name));
        return $this->redirectTo();
      }

      $this->checkSpinable($target);
      $msg=\Yii::t('app',"Target [%s] queued for restart. You will receive a notification when the operation is completed.");
      if($target->ondemand && $target->ondemand->state===-1)
        $msg=\Yii::t('app',"Target [%s] queued to power-up. You will receive a notification when the operation is completed.");

      $playerSpin=Yii::$app->user->identity->profile->spins;
      $SQ=new \app\modules\target\models\SpinQueue;
      $SQ->player_id=(int) \Yii::$app->user->id;
      $SQ->target_id=$target->id;
      $playerSpin->counter=intval($playerSpin->counter) + 1;
      $playerSpin->total=intval($playerSpin->total) + 1;
      if($SQ->save() !== false && $playerSpin->save() !== false)
        Yii::$app->session->setFlash('success', sprintf($msg, $target->name));
      else
        throw new NotFoundHttpException(\Yii::t('app','Failed to queue target for restart.'));

    }
    catch(\Exception $e)
    {
      Yii::$app->session->setFlash('error', $e->getMessage());
    }
    return $this->redirectTo();

  }

  protected function redirectTo()
  {
    if(Yii::$app->request->referrer)
    {
      return Yii::$app->getResponse()->redirect(Yii::$app->request->referrer);
    }
    return Yii::$app->getResponse()->redirect(['/target/default/index']);
  }

  protected function findModelProgress($id)
  {
    if(($model=Target::find()->player_progress(\Yii::$app->user->id)->where(['t.id'=>$id])->one()) !== null)
    {
        return $model;
    }
    throw new NotFoundHttpException(\Yii::t('app','The requested target does not exist.'));
  }

  protected function checkSpinable($target)
  {
    if($target->spinable !== true)
      throw new NotFoundHttpException(\Yii::t('app','Not allowed to spin target. Target cannot not be spined.'));
  }

}
