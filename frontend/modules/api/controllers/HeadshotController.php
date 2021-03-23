<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class HeadshotController extends \yii\rest\ActiveController
{
  public $modelClass="\app\modules\game\models\Headshot";
  public $serializer=[
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

//    public function behaviors()
//      {
//          return ArrayHelper::merge(parent::behaviors(), [
//              [
//                  'class' => 'yii\filters\ContentNegotiator',
//                  'formats' => [
//                      'application/json' => \yii\web\Response::FORMAT_JSON,
//                      'application/xml' => \yii\web\Response::FORMAT_XML,
//                  ],
//              ],
//          ]);
//      }

  public function actions()
  {
      $actions = parent::actions();

      // disable the "delete", "create", "view","update" actions
      unset($actions['delete'], $actions['create'],$actions['view'],$actions['update']);
      $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

      return $actions;
  }

  public function prepareDataProvider()
  {
    //\Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;

    $requestParams = Yii::$app->getRequest()->getBodyParams();
    if (empty($requestParams)) {
        $requestParams = Yii::$app->getRequest()->getQueryParams();
    }
    $query = (new \yii\db\Query())
        ->select(['profile.id as profile_id', 'target_id','timer','rating','headshot.created_at'])
        ->from('headshot')
        ->leftJoin('profile', '`profile`.`player_id` = `headshot`.`player_id`')
        ->where(['profile.visibility'=>'public'])
        ->orderBy(['headshot.created_at'=>SORT_DESC,'target_id'=>SORT_ASC]);

    return new ActiveDataProvider([
      'query'=>$query,
      'pagination' => [
          'params' => $requestParams,
      ],
      'sort' => [
          'params' => $requestParams,
      ],
    ]);

  }
}
