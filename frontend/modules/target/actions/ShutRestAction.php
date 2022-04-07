<?php
namespace app\modules\target\actions;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\TargetInstance;
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
      $ti=TargetInstance::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$id]);
      // Check if the instance exists
      if($ti!==null && $ti->reboot!==2)
      {
        $ti->reboot=2;
        $ti->save();
        Yii::$app->session->setFlash('success', sprintf('Scheduling shutdown for instance [%s]. You will receive a notification when the instance is shut.', $ti->target->name));
      }
      else
      {
        Yii::$app->session->setFlash('warning', sprintf('You do not have an instance of [%s] running.', $ti->target->name));
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
}
