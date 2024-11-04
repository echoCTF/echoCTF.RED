<?php
namespace app\modules\api\actions;

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
    \Yii::$app->response->statusCode = 200;
    try
    {
      if (\Yii::$app->cache->memcache->get("api_target_shut:" . \Yii::$app->user->id) !== false) {
        \Yii::$app->response->statusCode = 429;
        return ["message"=>\Yii::t('app',"Rate-limited, wait a few seconds and try again."),"code"=>0,"status"=>\Yii::$app->response->statusCode];
      }
      \Yii::$app->cache->memcache->set("api_target_shut:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_target_shut_timeout) + 1);

      $target=$this->findTarget($id);
      if($target->status!=='online')
      {
        \Yii::$app->response->statusCode = 422;
        throw new UserException(\Yii::t('app',"Target is not online yet!"));
      }
      $ti=TargetInstance::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$id]);
      // Check if the instance exists
      if($ti!==null && $ti->reboot!==2)
      {
        $ti->reboot=2;
        $ti->save();
        \Yii::$app->response->statusCode = 201;
        \Yii::$app->response->data=["message"=>\Yii::t('app',"Instance scheduled for shutdown."),"code"=>0,"status"=>\Yii::$app->response->statusCode];
      }
      else
      {
        \Yii::$app->response->statusCode = 422;
        throw new UserException(\Yii::t('app',"No private instance found for target!"));
      }
    }
    catch(\Exception $e)
    {
      \Yii::$app->response->data=["message"=>$e->getMessage(),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }
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
