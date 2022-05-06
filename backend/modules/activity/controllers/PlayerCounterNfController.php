<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\PlayerCounterNf;
use app\modules\activity\models\PlayerCounterNfSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PlayerCounterNfController implements the CRUD actions for PlayerCounterNf model.
 */
class PlayerCounterNfController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all PlayerCounterNf models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlayerCounterNfSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerCounterNf model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($player_id,$metric)
    {
        return $this->render('view', [
            'model' => $this->findModel(['player_id'=>$player_id,'metric'=>$metric]),
        ]);
    }

    /**
     * Creates a new PlayerCounterNf model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlayerCounterNf();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->player_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerCounterNf model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($player_id,$metric)
    {
        $model = $this->findModel(['player_id'=>$player_id,'metric'=>$metric]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->player_id,'metric'=>$model->metric]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerCounterNf model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerCounterNf model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PlayerCounterNf the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlayerCounterNf::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
