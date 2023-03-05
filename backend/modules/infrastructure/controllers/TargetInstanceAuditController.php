<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\infrastructure\models\TargetInstanceAudit;
use app\modules\infrastructure\models\TargetInstanceAuditSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * TargetInstanceAuditController implements the CRUD actions for TargetInstanceAudit model.
 */
class TargetInstanceAuditController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'truncate' => ['POST'],
                ]
            ]
        ]);
    }


    /**
     * Lists all TargetInstanceAudit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TargetInstanceAuditSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Truncate the existing records from TargetInstanceAudit table.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionTruncate()
    {
        try
        {
            TargetInstanceAudit::deleteAll();
            Yii::$app->session->setFlash('success', Yii::t('app',"All audit records have been deleted."));
        }
        catch(\Exception $e)
        {
            Yii::$app->session->setFlash('error', Yii::t('app',"Failed to delete audit records. <b>{exception}</b>",['exception'=>Html::encode($e->getMessage())]));
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the TargetInstanceAudit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TargetInstanceAudit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TargetInstanceAudit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
