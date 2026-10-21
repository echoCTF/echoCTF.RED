<?php

namespace app\actions\profile;

use Yii;
use yii\data\ActiveDataProvider;

class HintsRestAction extends \yii\rest\IndexAction
{
  public $modelClass = "\app\models\Hint";
  public $serializer = 'yii\rest\Serializer';
  public function run()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $requestParams = Yii::$app->request->get();
    $playerHints = \app\models\PlayerHint::find()->forAjax()->forPlayer((int) Yii::$app->user->id);
    $dataProvider = new ActiveDataProvider([
      'query' => $playerHints,
      'pagination' => [
        'pageParam' => 'playerHint-page',
        'pageSize' => 5,
        'params' => $requestParams,
      ],
      'sort' => [
        'params' => $requestParams,
      ]
    ]);
    $ret = Yii::createObject($this->serializer)->serialize($dataProvider);
    $hints = \yii\helpers\ArrayHelper::getColumn($dataProvider->getModels(), 'hint_id');
    if ($hints)
      \app\models\PlayerHint::updateAll(['status' => 0], ['player_id' => Yii::$app->user->id, 'hint_id' => $hints]);
    return $ret;
  }
}
