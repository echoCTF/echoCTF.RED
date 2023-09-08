<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\ChallengeSolver;
use app\modules\activity\models\ChallengeSolverSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ChallengeSolverController implements the CRUD actions for ChallengeSolver model.
 */
class ChallengeSolverController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all ChallengeSolver models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChallengeSolverSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ChallengeSolver model.
     * @param integer $challenge_id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($challenge_id, $player_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($challenge_id, $player_id),
        ]);
    }

    /**
     * Creates a new ChallengeSolver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      $submit=Yii::$app->request->post('submit');
      $model=new ChallengeSolver();
      $model->load(Yii::$app->request->post());
      if($submit && $submit[0]==='give' && $model->validate() && $this->give())
      {
        return $this->redirect(['view', 'challenge_id' => $model->challenge_id, 'player_id' => $model->player_id]);
      }
      elseif($submit && $submit[0]==='save' && $model->load(Yii::$app->request->post()) && $model->save())
      {
        return $this->redirect(['view', 'challenge_id' => $model->challenge_id, 'player_id' => $model->player_id]);
      }
      return $this->render('create', [
        'model' => $model,
      ]);
    }

    /**
     * Updates an existing ChallengeSolver model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $challenge_id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($challenge_id, $player_id)
    {
        $model = $this->findModel($challenge_id, $player_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'challenge_id' => $model->challenge_id, 'player_id' => $model->player_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Gives a Solve for a challenge on a Player model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function give()
    {
      $model=new ChallengeSolver();
      $connection=Yii::$app->db;
      if($model->load(Yii::$app->request->post()) && $model->validate())
      {
        if(intval($model->timer)===0) $model->timer=random_int(1000, 100000);
        $transaction=$connection->beginTransaction();
        try {
          $connection->createCommand('INSERT IGNORE INTO player_question (player_id,question_id) SELECT :player_id,id FROM question WHERE challenge_id=:challenge_id ORDER BY id DESC')
          ->bindValue(':player_id', $model->player_id)
          ->bindValue(':challenge_id', $model->challenge_id)->query();
          if(!ChallengeSolver::find()->where(['challenge_id'=>$model->challenge_id,'player_id'=>$model->player_id])->exists())
          {
            $connection->createCommand('INSERT IGNORE INTO challenge_solver (challenge_id,player_id,timer) values (:challenge_id,:player_id,:timer)')
            ->bindValue(':player_id', $model->player_id)
            ->bindValue(':challenge_id', $model->challenge_id)
            ->bindValue(':timer', $model->timer)->query();
          }
          $connection->createCommand('UPDATE challenge_solver SET timer=:timer WHERE player_id=:player_id AND challenge_id=:challenge_id')
          ->bindValue(':player_id', $model->player_id)
          ->bindValue(':challenge_id', $model->challenge_id)
          ->bindValue(':timer', $model->timer)->query();
          $transaction->commit();
          return true;
        }
        catch (\Throwable $e)
        {
          $transaction->rollBack();
          Yii::$app->session->addFlash('error',$e->getMessage());
        }
      }
      return false;
    }

    /**
     * Deletes an existing ChallengeSolver model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $challenge_id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($challenge_id, $player_id)
    {
        $this->findModel($challenge_id, $player_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ChallengeSolver model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $challenge_id
     * @param integer $player_id
     * @return ChallengeSolver the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($challenge_id, $player_id)
    {
        if (($model = ChallengeSolver::findOne(['challenge_id' => $challenge_id, 'player_id' => $player_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
