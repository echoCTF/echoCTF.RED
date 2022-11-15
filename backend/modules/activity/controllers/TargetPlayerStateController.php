<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\TargetPlayerState;
use app\modules\activity\models\TargetPlayerStateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TargetPlayerStateController implements the CRUD actions for TargetPlayerState model.
 */
class TargetPlayerStateController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all TargetPlayerState models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TargetPlayerStateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TargetPlayerState model.
     * @param integer $id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $player_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $player_id),
        ]);
    }

    /**
     * Creates a new TargetPlayerState model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TargetPlayerState();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'player_id' => $model->player_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TargetPlayerState model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $player_id)
    {
        $model = $this->findModel($id, $player_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'player_id' => $model->player_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TargetPlayerState model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $player_id)
    {
        $this->findModel($id, $player_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TargetPlayerState model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $player_id
     * @return TargetPlayerState the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $player_id)
    {
        if (($model = TargetPlayerState::findOne(['id' => $id, 'player_id' => $player_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
