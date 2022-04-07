<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\infrastructure\models\TargetInstance;
use app\modules\infrastructure\models\TargetInstanceSearch;
use app\modules\infrastructure\models\DockerContainer;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * TargetInstanceController implements the CRUD actions for TargetInstance model.
 */
class TargetInstanceController extends \app\components\BaseController
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
                    'delete' => ['POST'],
                    'create' => ['POST'],
                    'destroy' => ['POST'],
                    'restart' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all TargetInstance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TargetInstanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TargetInstance model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TargetInstance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TargetInstance();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->player_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TargetInstance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->player_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Start a Target Instance .
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRestart($id)
    {
        try 
        {
            $val=$this->findModel($id);
            $dc=new DockerContainer($val->target);
            $dc->targetVolumes=$val->target->targetVolumes;
            $dc->targetVariables=$val->target->targetVariables;
            $dc->name=$val->name;
            $dc->server=$val->server->connstr;
            try 
            {
                $dc->destroy();
            } 
            catch (\Exception $e) { }
            $dc->pull();
            $dc->spin();
            $val->ipoctet=$dc->container->getNetworkSettings()->getNetworks()->{$val->server->network}->getIPAddress();
            $val->reboot=0;
            $val->save();
        }
        catch (\Exception $e)
        {
            if(method_exists($e,'getErrorResponse'))
                echo $e->getErrorResponse()->getMessage(),"\n";
            else
                echo $e->getMessage(),"\n";
        }

        return $this->redirect(['index']);
    }

    /**
     * Destroy a Target Instance .
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDestroy($id)
    {
        try
        {
            $val=$this->findModel($id);
            $dc=new DockerContainer($val->target);
            $dc->targetVolumes=$val->target->targetVolumes;
            $dc->targetVariables=$val->target->targetVariables;
            $dc->name=$val->name;
            $dc->server=$val->server->connstr;
            try 
            {
                $dc->destroy();
            } 
            catch (\Exception $e) { }
            $val->delete();
        }
        catch (\Exception $e)
        {
          if(method_exists($e,'getErrorResponse'))
            echo $e->getErrorResponse()->getMessage(),"\n";
          else
            echo $e->getMessage(),"\n";
        }
  
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TargetInstance model.
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
     * Finds the TargetInstance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TargetInstance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TargetInstance::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
