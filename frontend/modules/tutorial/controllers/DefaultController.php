<?php

namespace app\modules\tutorial\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\overloads\yii\filters\AccessControl;
use app\modules\tutorial\models\Tutorial;
use app\modules\tutorial\models\TutorialTask;
use yii\helpers\ArrayHelper;
/**
 * TutorialController implements the CRUD actions for Tutorial model.
 */
class DefaultController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
      return ArrayHelper::merge(parent::behaviors(),[
          'access' => [
              'class' => AccessControl::class,
              'only' => ['index', 'view'],
              'rules' => [
                  [
                      'actions' => ['index', 'view'],
                      'allow' => true,
                      'roles' => ['@'],
                  ],
              ],
          ],
      ]);
  }

    /**
     * Lists all Tutorial models.
     * @return mixed
     */
    public function actionIndex()
    {
      $dataProvider=new ActiveDataProvider([
          'query' => Tutorial::find(),
      ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tutorial model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
      $model=Tutorial::find()->where(['id'=>$id])->one();
      if($model===null)
      {
          throw new NotFoundHttpException(\Yii::t('app','The requested tutorial could not be found.'));
      }
      $query=TutorialTask::find()->orderBy(['weight'=>SORT_ASC, 'id'=>SORT_ASC]);
      $dataProvider=new ActiveDataProvider([
          'query' => $query,
      ]);
      $query->andFilterWhere(['tutorial_id'=>$model->id]);

      return $this->render('view', [
          'model' => $model,
          'dataProvider' => $dataProvider,
      ]);
    }

    /**
     * Finds the Tutorial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tutorial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Tutorial::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app','The requested page does not exist.'));
    }
}
