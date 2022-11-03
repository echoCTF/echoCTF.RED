<?php
namespace app\modules\target\actions;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\TargetInstance;
use app\modules\target\models\Target;
use yii\web\NotFoundHttpException;
use yii\base\UserException;
use yii\helpers\Url;
class ShutRestAction extends \yii\rest\ViewAction
{
  public $modelClass='\app\modules\target\models\TargetInstance';
  public $serializer='yii\rest\Serializer';

  public function run($id)
  {
    \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
    try
    {
      $target=$this->findTarget($id);
      if($target->status!=='online')
      {
        throw new UserException(\Yii::t('app',"Target can't be shutdown, target is not online yet!"));
      }
      $ti=TargetInstance::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$id]);
      // Check if the instance exists
      if($ti!==null && $ti->reboot!==2)
      {
        $ti->reboot=2;
        $ti->save();
        Yii::$app->session->setFlash('success', sprintf(\Yii::t('app','Scheduling shutdown for instance [%s]. You will receive a notification when the instance is shut.'), $ti->target->name));
      }
      else
      {
        Yii::$app->session->setFlash('warning', sprintf(\Yii::t('app','You do not have an instance of [%s] running.'), $target->name));
      }

    }
    catch(\Exception $e)
    {
      Yii::$app->session->setFlash('error', $e->getMessage());
    }

    $goback=Url::previous();
    if($goback==='/')
      $goback=['/target/default/view','id'=>$id];

    return Yii::$app->controller->redirect($goback);
  }

  private function findTarget($id)
  {
    if(($model=Target::findOne($id))!==NULL)
    {
      return $model;
    }

    throw new NotFoundHttpException(\Yii::t('app','The requested target does not exist.'));

  }

}
