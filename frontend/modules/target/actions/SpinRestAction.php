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

      $this->checkSpinable($target);
      $msg=\Yii::t('app',"Target [%s] queued for reboot. You will receive a notification when the operation is completed.");
      if($target->ondemand && $target->ondemand->state===-1)
        $msg=\Yii::t('app',"Target [%s] queued to power-up. You will receive a notification when the operation is completed.");

      $playerSpin=Yii::$app->user->identity->profile->spins;
      $SQ=new \app\modules\target\models\SpinQueue;
      $SQ->player_id=(int) \Yii::$app->user->id;
      $SQ->target_id=$target->id;
      $playerSpin->counter=intval($playerSpin->counter) + 1;
      $playerSpin->total=intval($playerSpin->total) + 1;
      if($SQ->save() !== false && $playerSpin->save() !== false)
      {
        Yii::$app->session->setFlash('success', sprintf($msg, $target->name));
        if(\Yii::$app->sys->spins_per_day!==false && intval($playerSpin->counter)>=intval(\Yii::$app->sys->spins_per_day))
        {
          Yii::$app->user->identity->notify("swal:info",\Yii::t('app',"Max spins reached"),\Yii::t('app','You have reached the maximum number of spins for the day. Feel free to reach out to our support to reset your counter!'));
        }
      }
      else
        throw new NotFoundHttpException(\Yii::t('app','Failed to queue target for reboot.'));

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
      throw new NotFoundHttpException(\Yii::t('app','Not allowed to spin target. Target cannot be spun.'));
  }

}
