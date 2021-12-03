<?php

namespace app\modules\gameplay\controllers;

use Yii;
use app\modules\gameplay\models\TutorialTarget;
use app\modules\gameplay\models\TutorialTargetSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TutorialTargetController implements the CRUD actions for TutorialTarget model.
 */
class TutorialTargetController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all TutorialTarget models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TutorialTargetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TutorialTarget model.
     * @param integer $tutorial_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($tutorial_id, $target_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($tutorial_id, $target_id),
        ]);
    }

    /**
     * Creates a new TutorialTarget model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TutorialTarget();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'tutorial_id' => $model->tutorial_id, 'target_id' => $model->target_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TutorialTarget model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $tutorial_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($tutorial_id, $target_id)
    {
        $model = $this->findModel($tutorial_id, $target_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'tutorial_id' => $model->tutorial_id, 'target_id' => $model->target_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TutorialTarget model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $tutorial_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($tutorial_id, $target_id)
    {
        $this->findModel($tutorial_id, $target_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TutorialTarget model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $tutorial_id
     * @param integer $target_id
     * @return TutorialTarget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($tutorial_id, $target_id)
    {
        if (($model = TutorialTarget::findOne(['tutorial_id' => $tutorial_id, 'target_id' => $target_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
