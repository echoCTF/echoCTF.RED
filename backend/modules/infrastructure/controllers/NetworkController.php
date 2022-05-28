<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\gameplay\models\Network;
use app\modules\gameplay\models\NetworkSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * NetworkController implements the CRUD actions for Network model.
 */
class NetworkController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[
        'rules'=>[
            'class' => 'yii\filters\AjaxFilter',
            'only' => ['ajax-search']
          ],
      ]);
    }

    /**
     * Lists all Network models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new NetworkSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Network model.
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
     * Creates a new Network model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Network();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Network model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
     * Deletes an existing Network model.
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
     * Perform an ajax search for a network
     */
    public function actionAjaxSearch($term,$load=false)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $results=[];
        if (Yii::$app->request->isAjax)
        {
          $pq=Network::find()->select(['id','name','codename']);
          if($load===false)
          {
            $pq->where(['like','name',$term.'%',false]);
            $pq->orWhere(['LIKE','codename',$term.'%',false]);
          }
          else
          {
            $pq->where(['=','id',$term]);
          }
          $results=array_values(ArrayHelper::map($pq->all(),'id',
            function($model){
              return [
                'id'=>$model->id,
                'label'=>sprintf("(id: %d / %s) %s",$model->id,$model->codename,$model->name),
              ];
            }
          ));

        }
        return $results;
    }

    /**
     * Finds the Network model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Network the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Network::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
