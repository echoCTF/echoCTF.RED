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
  public $modelClass='\app\modules\target\models\TargetInstance';
  public $serializer='yii\rest\Serializer';

  public function run($id)
  {

    \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
    $goback=Url::previous();
    if($goback==='/')
      $goback=['/target/default/view','id'=>$id];

    try
    {
      $target=$this->findTarget($id);
      if($target->status!=='online')
      {
        throw new UserException('Target did not start, target is not online yet!');
      }
      $ti=TargetInstance::findOne(Yii::$app->user->id);
      // Check if user has already a started instance
      if($ti!==null)
      {
        if($ti->target_id!=$id)
        {
          Yii::$app->session->setFlash('warning', sprintf('Scheduling shutdown for old [%s] instance. You will receive a notification when the instance is shut.', $ti->target->name));
          $ti->reboot=2;
          $ti->save();
        }
        else
        {
          Yii::$app->session->setFlash('info', sprintf('You already have an instance of [%s] running.', $ti->target->name));
        }

        return Yii::$app->controller->redirect($goback);
      }

      $ti=new TargetInstance;
      $ti->player_id=Yii::$app->user->id;
      $ti->target_id=$id;
      // pick a random server to use
      $ti->server_id=intval(Yii::$app->db->createCommand('SELECT id FROM server ORDER BY RAND() LIMIT 1')->queryScalar());
      if($ti->save()!==false)
        Yii::$app->session->setFlash('success', sprintf('Spawning new instance for [%s]. You will receive a notification when the instance is up.', $ti->target->name));
      else
        throw new UserException('Failed to spawn new target instance for you.');

    }
    catch(\Exception $e)
    {
      Yii::$app->session->setFlash('error', $e->getMessage());
    }

    return Yii::$app->controller->redirect($goback);
  }

  private function findTarget($id)
  {
    if(($model=Target::findOne($id))!==NULL)
    {
      return $model;
    }

    throw new NotFoundHttpException('The requested target does not exist.');

  }
}
