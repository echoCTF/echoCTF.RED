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
      if($this->actionAllowedFor($target->id))
      {
        throw new UserException(\Yii::t('app','Target not allowed to spawn private instances!'));
      }

      if($target->status!=='online')
      {
        throw new UserException(\Yii::t('app','Target did not start, target is not online yet!'));
      }
      $ti=TargetInstance::findOne(Yii::$app->user->id);
      // Check if user has already a started instance
      if($ti!==null)
      {
        if($ti->target_id!=$id)
        {
          Yii::$app->session->setFlash('warning', sprintf(\Yii::t('app','Scheduling shutdown for old [%s] instance. You will receive a notification when the instance is shut.'), $ti->target->name));
          $ti->reboot=2;
          $ti->save();
        }
        else
        {
          Yii::$app->session->setFlash('info', sprintf(\Yii::t('app','You already have an instance of [%s] running.'), $ti->target->name));
        }

        return Yii::$app->controller->redirect($goback);
      }

      $ti=new TargetInstance;
      $ti->player_id=Yii::$app->user->id;
      $ti->target_id=$id;
      // pick the least used server currently
      $ti->server_id=intval(Yii::$app->db->createCommand('select id from server t1 left join target_instance t2 on t1.id=t2.server_id group by t1.id order by count(t2.server_id) limit 1')->queryScalar());
      if($ti->save()!==false)
        Yii::$app->session->setFlash('success', sprintf(\Yii::t('app','Spawning new instance for [%s]. You will receive a notification when the instance is up.'), $ti->target->name));
      else
        throw new UserException(\Yii::t('app','Failed to spawn new target instance for you.'));

    }
    catch(\Exception $e)
    {
      Yii::$app->session->setFlash('error', $e->getMessage());
    }

    return Yii::$app->controller->redirect($goback);
  }

  protected function actionAllowedFor($id)
  {
      $model=$this->findTarget($id);
      $action=sprintf('/target/%d/spawn',$model->id);
      return $model->instance_allowed && Yii::$app->DisabledRoute->disabled($action);
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
