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
    $target=Target::find()->where(['t.id'=>$id])->player_progress(\Yii::$app->user->id)->one();
    try
    {
      if($target === null)
        throw new NotFoundHttpException('The requested page does not exist.');
      if($target->network !== null && NetworkPlayer::findOne($target->network->id,\Yii::$app->user->id) === null)
        throw new NotFoundHttpException('Not allowed to spin target. You dont have access to this network.');
      if($target->spinable !== true)
        throw new NotFoundHttpException('Not allowed to spin target. Target not spinable.');
      $playerSpin=Yii::$app->user->identity->profile->spins;
      $SQ=new \app\modules\target\models\SpinQueue;
      $SQ->player_id=(int) \Yii::$app->user->id;
      $SQ->target_id=$target->id;
      $playerSpin->counter=intval($playerSpin->counter) + 1;
      $playerSpin->total=intval($playerSpin->total) + 1;
      if($SQ->save() !== false && $playerSpin->save() !== false)
        Yii::$app->session->setFlash('success', sprintf('Target [%s] queued for restart. You will receive a notification when the operation is completed.', $target->fqdn));
      else
        throw new NotFoundHttpException('Failed to queue target for restart.');

    }
    catch(\Exception $e)
    {
      Yii::$app->session->setFlash('error', $e->getMessage());
    }

    if(Yii::$app->request->referrer)
    {
      return Yii::$app->getResponse()->redirect(Yii::$app->request->referrer);
    }
    else
    {
      return Yii::$app->getResponse()->redirect(['/dashboard/index']);
    }

  }

}
