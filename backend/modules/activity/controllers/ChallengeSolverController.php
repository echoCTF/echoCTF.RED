<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\ChallengeSolver;
use app\modules\activity\models\ChallengeSolverSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChallengeSolverController implements the CRUD actions for ChallengeSolver model.
 */
class ChallengeSolverController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
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
        $model = new ChallengeSolver();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
