<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\gameplay\models\TargetVolume;
use app\modules\gameplay\models\TargetVolumeSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TargetvolumeController implements the CRUD actions for TargetVolume model.
 */
class TargetVolumeController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all TargetVolume models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new TargetVolumeSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TargetVolume model.
     * @param integer $target_id
     * @param string $volume
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($target_id, $volume)
    {
        return $this->render('view', [
            'model' => $this->findModel($target_id, $volume),
        ]);
    }

    /**
     * Creates a new TargetVolume model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new TargetVolume();
        if(\app\modules\gameplay\models\Target::find()->count() == 0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', Yii::t('app',"No targets found create one first."));
          return $this->redirect(['/gameplay/target/create']);
        }

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'target_id' => $model->target_id, 'volume' => $model->volume]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TargetVolume model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $target_id
     * @param string $volume
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($target_id, $volume)
    {
        $model=$this->findModel($target_id, $volume);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'target_id' => $model->target_id, 'volume' => $model->volume]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TargetVolume model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $target_id
     * @param string $volume
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($target_id, $volume)
    {
        $this->findModel($target_id, $volume)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TargetVolume model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $target_id
     * @param string $volume
     * @return TargetVolume the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($target_id, $volume)
    {
        if(($model=TargetVolume::findOne(['target_id' => $target_id, 'volume' => $volume])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }
}
