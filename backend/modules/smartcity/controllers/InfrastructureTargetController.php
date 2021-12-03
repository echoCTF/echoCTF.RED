<?php

namespace app\modules\smartcity\controllers;

use Yii;
use app\modules\smartcity\models\InfrastructureTarget;
use app\modules\smartcity\models\InfrastructureTargetSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * InfrastructureTargetController implements the CRUD actions for InfrastructureTarget model.
 */
class InfrastructureTargetController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all InfrastructureTarget models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new InfrastructureTargetSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InfrastructureTarget model.
     * @param integer $infrastructure_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($infrastructure_id, $target_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($infrastructure_id, $target_id),
        ]);
    }

    /**
     * Creates a new InfrastructureTarget model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new InfrastructureTarget();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'infrastructure_id' => $model->infrastructure_id, 'target_id' => $model->target_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InfrastructureTarget model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $infrastructure_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($infrastructure_id, $target_id)
    {
        $model=$this->findModel($infrastructure_id, $target_id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'infrastructure_id' => $model->infrastructure_id, 'target_id' => $model->target_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing InfrastructureTarget model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $infrastructure_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($infrastructure_id, $target_id)
    {
        $this->findModel($infrastructure_id, $target_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the InfrastructureTarget model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $infrastructure_id
     * @param integer $target_id
     * @return InfrastructureTarget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($infrastructure_id, $target_id)
    {
        if(($model=InfrastructureTarget::findOne(['infrastructure_id' => $infrastructure_id, 'target_id' => $target_id])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
