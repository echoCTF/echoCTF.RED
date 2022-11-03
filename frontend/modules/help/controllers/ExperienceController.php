<?php

namespace app\modules\help\controllers;

use Yii;
use app\models\Experience;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * FaqController implements the CRUD actions for Faq model.
 */
class ExperienceController extends Controller
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

    /**
     * Lists all Faq models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider=new ActiveDataProvider([
            'query' => Experience::find()->orderBy(['id'=>SORT_ASC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
