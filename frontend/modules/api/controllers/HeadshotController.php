<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ActiveDataFilter;
class HeadshotController extends \yii\rest\ActiveController
{
  public $modelClass="\app\modules\api\models\Headshot";
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
    \Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;

    $requestParams = Yii::$app->getRequest()->getBodyParams();
    if (empty($requestParams)) {
        $requestParams = Yii::$app->getRequest()->getQueryParams();
    }
    $filter = new ActiveDataFilter([
        'searchModel' => '\app\modules\api\models\HeadshotSearch',
        'attributeMap' => [
            'profile_id' => 'profile.id',
            'target_name' => 't.name',
            'created_at' => 'headshot.created_at',
            'timer' => 'headshot.timer'
        ]
    ]);

    $filterCondition = null;

    // You may load filters from any source. For example,
    // if you prefer JSON in request body,
    // use Yii::$app->request->getBodyParams() below:
    if ($filter->load(\Yii::$app->request->get())) {
        $filterCondition = $filter->build();
        if ($filterCondition === false) {
            // Serializer would get errors out of it
            return $filter;
        }
    }
    $query=\app\modules\api\models\Headshot::find()->rest();
    if ($filterCondition !== null) {
        $query->andWhere($filterCondition);
    }

    $dataProvider=new ActiveDataProvider([
      'query'=>$query,
      'pagination' => [
          'pageSizeLimit' => [1,100],
          'defaultPageSize'=>10,
          'params' => $requestParams,
      ],
      'sort' => [
        'params' => $requestParams,
      ],
    ]);
    $dataProvider->setSort([
        'defaultOrder' => ['created_at' => SORT_DESC],
        'attributes' => array_merge(
            $dataProvider->getSort()->attributes,
            [
              'profile_id' => [
                  'asc' => ['profile.id' => SORT_ASC],
                  'desc' => ['profile.id' => SORT_DESC],
              ],
              'created_at' => [
                  'asc' => ['headshot.created_at' => SORT_ASC],
                  'desc' => ['headshot.created_at' => SORT_DESC],
              ],
              'target_name' => [
                  'asc' => ['target.name' => SORT_ASC],
                  'desc' => ['target.name' => SORT_DESC],
              ],
            ]
        ),
    ]);
    return $dataProvider;
  }
}
