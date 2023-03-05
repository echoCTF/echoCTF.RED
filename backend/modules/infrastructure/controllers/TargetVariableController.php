<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\gameplay\models\TargetVariable;
use app\modules\gameplay\models\TargetVariableSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TargetvariableController implements the CRUD actions for TargetVariable model.
 */
class TargetVariableController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all TargetVariable models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new TargetVariableSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TargetVariable model.
     * @param integer $target_id
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($target_id, $key)
    {
        return $this->render('view', [
            'model' => $this->findModel($target_id, $key),
        ]);
    }

    /**
     * Creates a new TargetVariable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new TargetVariable();
        if(\app\modules\gameplay\models\Target::find()->count() == 0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', Yii::t('app',"No targets found create one first."));
          return $this->redirect(['/gameplay/target/create']);
        }

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'target_id' => $model->target_id, 'key' => $model->key]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TargetVariable model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $target_id
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($target_id, $key)
    {
        $model=$this->findModel($target_id, $key);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'target_id' => $model->target_id, 'key' => $model->key]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TargetVariable model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $target_id
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($target_id, $key)
    {
        $this->findModel($target_id, $key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TargetVariable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $target_id
     * @param string $key
     * @return TargetVariable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($target_id, $key)
    {
        if(($model=TargetVariable::findOne(['target_id' => $target_id, 'key' => $key])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }
}
