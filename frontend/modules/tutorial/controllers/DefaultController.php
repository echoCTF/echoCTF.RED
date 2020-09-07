<?php

namespace app\modules\tutorial\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use app\modules\tutorial\models\Tutorial;
use app\modules\tutorial\models\TutorialTask;
/**
 * TutorialController implements the CRUD actions for Tutorial model.
 */
class DefaultController extends Controller
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
      return [
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
      ];
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
//      $model=Tutorial::find()->where(['t.id'=>$id])->player_progress(Yii::$app->user->id)->one();
      if($model===null)
      {
          throw new NotFoundHttpException('The requested challenge could not be found.');
      }
      $query=TutorialTask::find()->orderBy(['weight'=>SORT_ASC, 'id'=>SORT_ASC]);
      $dataProvider=new ActiveDataProvider([
          'query' => $query,
      ]);
      $query->andFilterWhere(['tutorial_id'=>$model->id]);

//      $answer=new AnswerForm();
//      if($answer->load(Yii::$app->request->post()) && $answer->validate() && $answer->give($id))
//      {
//            Yii::$app->session->setFlash('success', sprintf('Accepted answer for question [%s] for %d pts.', $answer->question->name, intval($answer->question->points)));
//            return $this->redirect(Yii::$app->request->referrer);
//      }


      return $this->render('view', [
//          'answer'=>$answer,
          'model' => $model,
  //        'solvers' => $solvers,
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

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
