<?php

namespace app\modules\frontend\controllers;

use app\modules\frontend\models\PlayerMetadata;
use app\modules\frontend\models\PlayerMetadataSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerMetadataController implements the CRUD actions for PlayerMetadata model.
 */
class PlayerMetadataController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all PlayerMetadata models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PlayerMetadataSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerMetadata model.
     * @param int $player_id Player ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($player_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($player_id),
        ]);
    }

    /**
     * Creates a new PlayerMetadata model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new PlayerMetadata();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'player_id' => $model->player_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerMetadata model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $player_id Player ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($player_id)
    {
        $model = $this->findModel($player_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'player_id' => $model->player_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerMetadata model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $player_id Player ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($player_id)
    {
        $this->findModel($player_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerMetadata model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $player_id Player ID
     * @return PlayerMetadata the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($player_id)
    {
        if (($model = PlayerMetadata::findOne(['player_id' => $player_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
