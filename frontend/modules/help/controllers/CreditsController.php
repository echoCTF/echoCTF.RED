<?php

namespace app\modules\help\controllers;
use Yii;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\modules\help\models\Credits;
class CreditsController extends \app\components\BaseController
{
    public function behaviors()
    {
        return [
          'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                  'disabledRoute'=>[
                      'allow' => false,
                      'matchCallback' => function ($rule, $action) {
                        return Yii::$app->DisabledRoute->disabled($action);
                      },
                      'denyCallback' => function() {
                        throw new \yii\web\HttpException(404,\Yii::t('app','This area is disabled.'));
                      },
                  ],
                  [
                    'allow' => true,
                  ],
              ],
            ],
        ];
    }

    public function actionIndex()
    {
      $dataProvider = new ActiveDataProvider([
        'query' => Credits::find()->orderBy('id ASC'),
        'pagination' =>false,
      ]);
      return $this->render('index',['dataProvider'=>$dataProvider]);
    }


}
