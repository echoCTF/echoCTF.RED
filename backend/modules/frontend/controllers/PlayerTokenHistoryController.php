<?php

namespace app\modules\frontend\controllers;

use app\modules\frontend\models\PlayerTokenHistory;
use app\modules\frontend\models\PlayerTokenHistorySearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerTokenHistoryController implements the CRUD actions for PlayerTokenHistory model.
 */
class PlayerTokenHistoryController extends BaseController
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
                        'truncate' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all PlayerTokenHistory models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PlayerTokenHistorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing PlayerTokenHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes All PlayerTokenHistory models.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionTruncate()
    {
        if(PlayerTokenHistory::deleteAll()!==0)
          \Yii::$app->session->addFlash('success','Token History truncated');
        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerTokenHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PlayerTokenHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlayerTokenHistory::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app', 'The requested page does not exist.'));
    }
}
