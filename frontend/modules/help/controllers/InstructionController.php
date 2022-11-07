<?php

namespace app\modules\help\controllers;

use Yii;
use app\modules\help\models\Instruction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * InstructionController implements the CRUD actions for Instruction model.
 */
class InstructionController extends Controller
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
     * Lists all Instruction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider=new ActiveDataProvider([
            'query' => Instruction::find()->orderBy(['weight'=>SORT_ASC, 'id'=>SORT_ASC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
