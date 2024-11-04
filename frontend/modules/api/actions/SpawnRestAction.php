<?php
namespace app\modules\api\actions;

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
    \Yii::$app->response->statusCode = 200;

    try
    {
      \Yii::$app->response->statusCode = 201;
      $target=$this->findTarget($id);
      if (\Yii::$app->cache->memcache->get("api_target_spawn:" . \Yii::$app->user->id) !== false) {
        \Yii::$app->response->statusCode = 429;
        return ["message"=>\Yii::t('app',"Rate-limited, wait a few seconds and try again."),"code"=>0,"status"=>\Yii::$app->response->statusCode];
      }
      \Yii::$app->cache->memcache->set("api_target_spawn:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_target_spawn_timeout) + 1);

      if($this->actionAllowedFor($target->id))
      {
        \Yii::$app->response->statusCode = 403;
        throw new UserException(\Yii::t('app','Target not allowed to spawn private instances!'));
      }
      if(!$this->actionAllowedBy())
      {
        \Yii::$app->response->statusCode = 403;
        throw new UserException(\Yii::t('app','Player not allowed to spawn private instances!'));
      }

      if($target->status!=='online')
      {
        \Yii::$app->response->statusCode = 422;
        throw new UserException(\Yii::t('app','Target did not start, target is not online yet!'));
      }

      $ti=TargetInstance::findOne(Yii::$app->user->id);
      // Check if user has already a started instance
      if($ti!==null)
      {
        if($ti->target_id!=$id)
        {
          \Yii::$app->response->statusCode = 201;
          $ti->reboot=2;
          $ti->save();
          \Yii::$app->response->data=["message"=>\Yii::t('app',"Scheduled existing instance to shutdown"),"code"=>0,"status"=>\Yii::$app->response->statusCode];
        }
        else
        {
          \Yii::$app->response->statusCode = 200;
          \Yii::$app->response->data=["message"=>\Yii::t('app',"Instance already exists for this target."),"code"=>0,"status"=>\Yii::$app->response->statusCode];
        }

        return \Yii::$app->response->data;
      }

      $ti=new TargetInstance;
      $ti->player_id=Yii::$app->user->id;
      $ti->target_id=$id;
      // pick the least used server currently
      $ti->server_id=intval(Yii::$app->db->createCommand('select id from server t1 left join target_instance t2 on t1.id=t2.server_id group by t1.id order by count(t2.server_id) limit 1')->queryScalar());
      if(!$ti->save())
      {
        \Yii::$app->response->statusCode = 422;
        throw new UserException(\Yii::t('app','Failed to spawn new target instance for you.'));
      }
      \Yii::$app->response->data=["message"=>\Yii::t('app',"Scheduled spawn of private target instance."),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }
    catch(\Exception $e)
    {
      \Yii::$app->response->data=["message"=>$e->getMessage(),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }

    //return [];
  }

  protected function actionAllowedFor($id)
  {
      $model=$this->findTarget($id);
      $action=sprintf('/target/%d/spawn',$model->id);
      return $model->instance_allowed && Yii::$app->DisabledRoute->disabled($action);
  }

  protected function actionAllowedBy()
  {
    return (Yii::$app->user->identity->isVip===true || Yii::$app->sys->all_players_vip===true);
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
