<?php

namespace app\modules\infrastructure\controllers;

use Yii;
use app\modules\infrastructure\models\Server;
use app\modules\infrastructure\models\ServerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ServerController implements the CRUD actions for Server model.
 */
class ServerController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),['rules'=>[
        'class' => 'yii\filters\AjaxFilter',
        'only' => ['ajax-search']
      ],]);
    }

    /**
     * Lists all Server models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Server model.
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
     * Creates a new Server model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Server();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Server model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Server model.
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
     * Finds the Server model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Server the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Server::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionAjaxSearch($term)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $results=[];
        if (Yii::$app->request->isAjax) {
            $tmpres=Server::find()
                    ->where(['id'=>$term])
                    ->orWhere(['like','name',$term])
                    ->orWhere(['like','INET_NTOA(ip)',$term])
                    ->all();
            foreach($tmpres as $res)
              $results[]=[
                'id'=>$res->id,
                'label'=>sprintf("(id: %d) %s",$res->id,$res->name),
              ];
        }
        return $results;
    }

}
