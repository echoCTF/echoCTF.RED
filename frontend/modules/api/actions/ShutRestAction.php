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
      $target=$this->findTarget($id);
      if($target->status!=='online')
      {
        \Yii::$app->response->statusCode = 222;
        throw new UserException(\Yii::t('app',"Target is not online yet!"));
      }
      $ti=TargetInstance::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$id]);
      // Check if the instance exists
      if($ti!==null && $ti->reboot!==2)
      {
        $ti->reboot=2;
        $ti->save();
        \Yii::$app->response->statusCode = 201;
      }
      else
      {
        \Yii::$app->response->statusCode = 422;
      }

    }
    catch(\Exception $e)
    {
    }

    return [];
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
