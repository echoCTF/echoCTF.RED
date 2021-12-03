<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\gameplay\models\NetworkPlayer;
use app\modules\gameplay\models\NetworkPlayerSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * NetworkPlayerController implements the CRUD actions for NetworkPlayer model.
 */
class NetworkPlayerController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all NetworkPlayer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new NetworkPlayerSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NetworkPlayer model.
     * @param integer $network_id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($network_id, $player_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($network_id, $player_id),
        ]);
    }

    /**
     * Creates a new NetworkPlayer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new NetworkPlayer();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'network_id' => $model->network_id, 'player_id' => $model->player_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing NetworkPlayer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $network_id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($network_id, $player_id)
    {
        $model=$this->findModel($network_id, $player_id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'network_id' => $model->network_id, 'player_id' => $model->player_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing NetworkPlayer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $network_id
     * @param integer $player_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($network_id, $player_id)
    {
        $this->findModel($network_id, $player_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the NetworkPlayer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $network_id
     * @param integer $player_id
     * @return NetworkPlayer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($network_id, $player_id)
    {
        if(($model=NetworkPlayer::findOne(['network_id' => $network_id, 'player_id' => $player_id])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
