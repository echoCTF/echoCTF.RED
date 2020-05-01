<?php

namespace app\modules\smartcity\controllers;

use Yii;
use app\modules\smartcity\models\Infrastructure;
use app\modules\smartcity\models\InfrastructureSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InfrastructureController implements the CRUD actions for Infrastructure model.
 */
class InfrastructureController extends Controller
{
    /**
     * {@inheritdoc}
     */
      public function behaviors()
      {
          return [
            'access' => [
                  'class' => \yii\filters\AccessControl::class,
                  'rules' => [
                      [
                          'allow' => true,
                          'roles' => ['@'],
                      ],
                  ],
              ],
              'verbs' => [
                  'class' => VerbFilter::class,
                  'actions' => [
                      'delete' => ['POST'],
                  ],
              ],
          ];
      }

    /**
     * Lists all Infrastructure models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new InfrastructureSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Infrastructure model.
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
     * Creates a new Infrastructure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Infrastructure();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Infrastructure model.
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
     * Deletes an existing Infrastructure model.
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
     * Finds the Infrastructure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Infrastructure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Infrastructure::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
