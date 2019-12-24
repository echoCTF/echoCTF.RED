<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\Arpdat;
use app\modules\activity\models\ArpdatSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArpdatController implements the CRUD actions for Arpdat model.
 */
class ArpdatController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
          'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index','create','update','view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Arpdat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArpdatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Arpdat model.
     * @param integer $ip
     * @param string $mac
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ip, $mac)
    {
        return $this->render('view', [
            'model' => $this->findModel($ip, $mac),
        ]);
    }

    /**
     * Creates a new Arpdat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Arpdat();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ip' => $model->ip, 'mac' => $model->mac]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Arpdat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $ip
     * @param string $mac
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ip, $mac)
    {
        $model = $this->findModel($ip, $mac);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ip' => $model->ip, 'mac' => $model->mac]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Arpdat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $ip
     * @param string $mac
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ip, $mac)
    {
        $this->findModel($ip, $mac)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Arpdat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $ip
     * @param string $mac
     * @return Arpdat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ip, $mac)
    {
        if (($model = Arpdat::findOne(['ip' => $ip, 'mac' => $mac])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
