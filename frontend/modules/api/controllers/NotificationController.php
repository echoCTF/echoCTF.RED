<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\data\ActiveDataFilter;

class NotificationController extends \yii\rest\ActiveController
{
  public $modelClass = "\app\models\Notification";
  public $serializer = [
    'class' => 'yii\rest\Serializer',
    'collectionEnvelope' => 'items',
  ];
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'content'=>[
        'class' => yii\filters\ContentNegotiator::class,
        'formats' => [
          'application/json' => \yii\web\Response::FORMAT_JSON,
        ],
      ],
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['@'],
            'matchCallback'=> function() {
              if(!Yii::$app->request->isAjax)
                return false;
              return true;
            }
          ],
        ],
        'denyCallback' => function () {
          return \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
        }
      ],
    ]);
  }

  public function actions()
  {
    $actions = parent::actions();
    // disable the "delete", "create", "view","update" actions
    unset($actions['delete'], $actions['create'], $actions['view'], $actions['update']);
    $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

    return $actions;
  }

  public function prepareDataProvider()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $query = \app\models\Notification::find()->my()->pending();
    if(intval($query->count())===0)
    {
      \Yii::$app->response->statusCode=201;
      return [];
    }

    $dataProvider = new ActiveDataProvider([
      'query' => $query->orderBy(['created_at'=>SORT_DESC, 'id'=>SORT_DESC]),
      'pagination' => false,
    ]);

    foreach($dataProvider->getModels() as $n)
    {
      $n->touch('updated_at');
      $n->updateAttributes(['archived' => 1,'updated_at']);
    }

    return $dataProvider;
  }
}
