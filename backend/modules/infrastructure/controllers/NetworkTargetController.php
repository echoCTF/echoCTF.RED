<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\gameplay\models\NetworkTarget;
use app\modules\gameplay\models\NetworkTargetSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * NetworkTargetController implements the CRUD actions for NetworkTarget model.
 */
class NetworkTargetController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all NetworkTarget models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new NetworkTargetSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NetworkTarget model.
     * @param integer $network_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($network_id, $target_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($network_id, $target_id),
        ]);
    }

    /**
     * Creates a new NetworkTarget model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new NetworkTarget();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'network_id' => $model->network_id, 'target_id' => $model->target_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing NetworkTarget model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $network_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($network_id, $target_id)
    {
        $model=$this->findModel($network_id, $target_id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'network_id' => $model->network_id, 'target_id' => $model->target_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing NetworkTarget model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $network_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($network_id, $target_id)
    {
        $this->findModel($network_id, $target_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the NetworkTarget model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $network_id
     * @param integer $target_id
     * @return NetworkTarget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($network_id, $target_id)
    {
        if(($model=NetworkTarget::findOne(['network_id' => $network_id, 'target_id' => $target_id])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
