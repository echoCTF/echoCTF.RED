<?php
namespace app\actions\profile;
use Yii;
use yii\data\ActiveDataProvider;

class NotificationsRestAction extends \yii\rest\IndexAction
{
  public $modelClass="\app\models\Notification";
  public $serializer='yii\rest\Serializer';
  public function run()
  {
    \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
    $requestParams=Yii::$app->request->get();
    $notifications=\app\models\Notification::find()->forPlayer(Yii::$app->user->id)->forAjax()->orderBy(['created_at'=>SORT_DESC, 'id'=>SORT_DESC]);
    $notificationsProvider=new ActiveDataProvider([
        'query' => $notifications,
        'pagination' => [
            'pageParam'=>'notifications-page',
            'pageSize' => 5,
            'params' => $requestParams,
        ],
        'sort'=>[
          'params' => $requestParams,
        ]
      ]);
      $ret=Yii::createObject($this->serializer)->serialize($notificationsProvider);
      foreach($notificationsProvider->getModels() as $n)
      {
        if(intval($n->archived) === 0)
        {
          $n->touch('updated_at');
          $n->updateAttributes(['archived' => 1,'updated_at']);
        }
      }
      return $ret;
  }
}
