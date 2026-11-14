<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\PlayerAudit;
use app\modules\frontend\models\PlayerAuditSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerAuditController lists the player changes logged by the tau_player trigger.
 * There is no create or update, the entries come only from the trigger.
 */
class PlayerAuditController extends BaseController
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
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'truncate' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all PlayerAudit models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PlayerAuditSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerAudit model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing PlayerAudit model.
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
     * Truncates PlayerAudit.
     * @return \yii\web\Response
     */
    public function actionTruncate()
    {
        Yii::$app->db->createCommand()->truncateTable('player_audit')->execute();
        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerAudit model based on its primary key value.
     * @param int $id ID
     * @return PlayerAudit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlayerAudit::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
