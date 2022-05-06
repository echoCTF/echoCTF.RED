<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\PlayerScoreMonthly;
use app\modules\activity\models\PlayerScoreMonthlySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PlayerScoreMonthlyController implements the CRUD actions for PlayerScoreMonthly model.
 */
class PlayerScoreMonthlyController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }


    /**
     * Lists all PlayerScoreMonthly models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlayerScoreMonthlySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerScoreMonthly model.
     * @param integer $player_id
     * @param string $dated_at
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($player_id, $dated_at)
    {
        return $this->render('view', [
            'model' => $this->findModel($player_id, $dated_at),
        ]);
    }

    /**
     * Creates a new PlayerScoreMonthly model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlayerScoreMonthly();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'dated_at' => $model->dated_at]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerScoreMonthly model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $player_id
     * @param string $dated_at
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($player_id, $dated_at)
    {
        $model = $this->findModel($player_id, $dated_at);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'dated_at' => $model->dated_at]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerScoreMonthly model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $player_id
     * @param string $dated_at
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($player_id, $dated_at)
    {
        $this->findModel($player_id, $dated_at)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerScoreMonthly model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $player_id
     * @param string $dated_at
     * @return PlayerScoreMonthly the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($player_id, $dated_at)
    {
        if (($model = PlayerScoreMonthly::findOne(['player_id' => $player_id, 'dated_at' => $dated_at])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
