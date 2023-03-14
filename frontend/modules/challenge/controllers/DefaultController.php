<?php

namespace app\modules\challenge\controllers;

use Yii;
use app\modules\challenge\models\Challenge;
use app\modules\challenge\models\ChallengeSolver;
use app\modules\challenge\models\Question;
use app\modules\challenge\models\ChallengeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\modules\challenge\models\AnswerForm;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * ChallengeController implements the CRUD actions for Challenge model.
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
          'only' => ['index', 'view', 'download'],
          'rules' => [
              'eventStartEnd'=>[
                 'actions' => ['index', 'view', 'download'],
              ],
              'teamsAccess'=>[
                 'actions' => ['index', 'view', 'download'],
              ],
              'disabledRoute'=>[
                  'actions' => ['index', 'view', 'download'],
              ],
              [
                'actions' => ['view'],
                'allow' => false,
                'verbs' => ['POST'],
                'roles'=>['@'],
                'matchCallback' => function () {
                  return !\Yii::$app->request->validateCsrfToken(\Yii::$app->request->getBodyParam(\Yii::$app->request->csrfParam));
                },
              ],

              [
                  'allow' => true,
                  'roles'=>['@']
              ],
          ],
      ]]);
  }

    /**
     * Lists all Challenge models.
     * @return mixed
     */
    public function actionIndex()
    {
      $dataProvider=new ActiveDataProvider([
          'query' => Challenge::find()->active()->player_progress(Yii::$app->user->id)->orderBy([new \yii\db\Expression('FIELD (difficulty,"easy","medium","hard" )')]),
          'pagination' => false,
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
      $model=$this->findModelProgress($id);

      $query=Question::find()->orderBy(['weight'=>SORT_ASC, 'id'=>SORT_ASC]);
      $solvers=ChallengeSolver::find()->where(['challenge_id'=>$model->id])->academic(Yii::$app->user->identity->academic)->orderBy(['created_at'=>SORT_DESC, 'player_id'=>SORT_ASC]);
      $dataProvider=new ActiveDataProvider([
          'query' => $query,
          'pagination'=>false,
      ]);
      $query->andFilterWhere(['challenge_id'=>$model->id]);
      $solverProvider=new ActiveDataProvider([
          'query' => $solvers,
      ]);

      $answer=new AnswerForm();
      if(Yii::$app->request->isPost)
      {
        if($answer->load(Yii::$app->request->post()) && $answer->validate() && $answer->give($id))
        {
          Yii::$app->session->setFlash('success', sprintf(\Yii::t('app','Accepted answer for question [%s] for %d pts.'), $answer->question->name, intval($answer->question->points)));
          //return $this->redirect(Yii::$app->request->referrer);
        }
        else
        {
          Yii::$app->session->setFlash('error',\Yii::t('app','Invalid answer...'));
        }
        return $this->redirect(['view','id'=>$model->id]);
      }
      $solver=ChallengeSolver::findOne(['challenge_id'=>$id,'player_id'=>Yii::$app->user->id]);
      $answer->answer=null;
      return $this->render('view', [
        'answer'=>$answer,
        'model' => $model,
        'solvers' => $solvers,
        'solver'=> $solver,
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
        if(empty($model->filename))
            throw new NotFoundHttpException(\Yii::t('app','The requested challenge does not have a file to download.'));
        $storagePath=Yii::getAlias(Yii::$app->sys->challenge_home);

        Yii::$app->response->sendFile("{$storagePath}/{$model->filename}", $model->filename)->send();
        return;
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
        if(($model=Challenge::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app','The requested page does not exist.'));
    }

    /**
     * Finds the Challenge model with progress based on its primary key and player id value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Challenge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelProgress($id)
    {
        if(($model=Challenge::find()->where(['t.id'=>$id])->active()->player_progress(Yii::$app->user->id)->one()) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app','The requested challenge could not be found.'));
    }

}
