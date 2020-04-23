<?php

namespace app\modules\challenge\controllers;

use Yii;
use app\modules\challenge\models\Challenge;
use app\modules\challenge\models\Question;
use app\modules\challenge\models\ChallengeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\modules\challenge\models\AnswerForm;
use yii\filters\AccessControl;

/**
 * ChallengeController implements the CRUD actions for Challenge model.
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
              'class' => AccessControl::className(),
              'only' => ['index','view','download'],
              'rules' => [
                  [
                      'actions' => ['index','view','download'],
                      'allow' => true,
                      'roles' => ['@'],
                  ],
              ],
          ],
      ];
  }

    /**
     * Lists all Challenge models.
     * @return mixed
     */
    public function actionIndex()
    {
      $dataProvider = new ActiveDataProvider([
          'query' => Challenge::find()->player_progress(Yii::$app->user->id),
      ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Challenge model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
      $model=$this->findModel($id);
      $model=Challenge::find()->where(['t.id'=>$id])->player_progress(Yii::$app->user->id)->one();
      $query=Question::find()->orderBy(['weight'=>SORT_ASC,'id'=>SORT_ASC]);

      $dataProvider = new ActiveDataProvider([
          'query' => $query,
      ]);
      $query->andFilterWhere(['challenge_id'=>$model->id]);

      $answer = new AnswerForm();
      if ($answer->load(Yii::$app->request->post()) && $answer->validate() && $answer->give($id)) {
            Yii::$app->session->setFlash('success',sprintf('Accepted answer for question [%s] for %d pts.',$answer->question->name, intval($answer->question->points)));
            return $this->redirect(Yii::$app->request->referrer);
      }


      return $this->render('view', [
          'answer'=>$answer,
          'model' => $model,
          'dataProvider' => $dataProvider,
      ]);
    }

    /**
     * Force download of Challenge file.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDownload(int $id)
    {
        $model=$this->findModel($id);
        if($model->filename===null) {
                    throw new NotFoundHttpException('The requested challenge does not have a file to download.');
        }
        $storagePath = Yii::getAlias(Yii::$app->sys->challenge_home);

        return Yii::$app->response->sendFile("{$storagePath}/{$model->filename}", $model->filename)->send();
    }

    /**
     * Finds the Challenge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Challenge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Challenge::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
