<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\Sessions;
use app\modules\activity\models\SessionsSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * SessionController implements the CRUD actions for Sessions model.
 */
class SessionController extends \app\components\BaseController
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
                      'delete-filtered' => ['POST'],
                  ],
              ],
          ]);
      }

    /**
     * Lists all Sessions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new SessionsSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sessions model.
     * @param string $id
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
     * Creates a new Sessions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Sessions();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sessions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model=$this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sessions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes expired Sessions.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteExpired()
    {
        $expired=new \yii\db\Expression('UNIX_TIMESTAMP(NOW() - INTERVAL 2 DAY)');
//        $expired_at=(new \yii\db\Query)->select($expired)->scalar();
        $sess=Sessions::deleteAll(['<', 'expire', $expired]);
        if($sess > 0)
          Yii::$app->session->setFlash('success', Yii::t('app',"Deleted {counter} expired sessions.",['counter'=>$sess]));
        else
          Yii::$app->session->setFlash('warning', Yii::t('app',"No expired sessions found to delete."));

        return $this->redirect(['index']);
    }

    public function actionDeleteFiltered()
    {
      $searchModel=new SessionsSearch();
      $query=$searchModel->search(['SessionsSearch'=>Yii::$app->request->post()]);
      $query->pagination=false;
      $trans=Yii::$app->db->beginTransaction();
      try
      {
        $counter=$query->count;
        foreach($query->getModels() as $q)
          $q->delete();
        $trans->commit();
        Yii::$app->session->setFlash('success', Yii::t('app','[<code><b>{counter}</b></code>] Sessions deleted',['counter'=>intval($counter)]));

      }
      catch(\Exception $e)
      {
        $trans->rollBack();
        Yii::$app->session->setFlash('error', Yii::t('app','Failed to delete Sessions'));
      }
      return $this->redirect(['index']);
    }


    /**
     * Finds the Sessions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Sessions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Sessions::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
