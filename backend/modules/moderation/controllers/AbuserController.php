<?php

namespace app\modules\moderation\controllers;

use Yii;
use app\modules\moderation\models\Abuser;
use app\modules\moderation\models\AbuserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AbuserController implements the CRUD actions for Abuser model.
 */
class AbuserController extends \app\components\BaseController
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
            'delete-filtered' => ['POST'],
          ],
        ],
      ]
    );
  }

  /**
   * Lists all Abuser models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new AbuserSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Abuser model.
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
   * Creates a new Abuser model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate($player_id = false)
  {
    $model = new Abuser();

    if ($this->request->isPost) {
      if ($model->load($this->request->post()) && $model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
      }
    } else {
      $model->loadDefaultValues();
    }
    if ($player_id !== false) $model->player_id = $player_id;
    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Abuser model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $id ID
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Analyze an existing Abuser model.
   * If update is successful, the browser will be redirected to the 'analys' page.
   * @param int $id ID
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionAnalyze($id)
  {
    $model = $this->findModel($id);

    if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
      if ($this->request->post('action') === 'giver' && ($abuser=$model->failedClaimGiver())!==null) {
        if($abuser->save())
          \Yii::$app->session->addFlash('success', "Created giver record!");
        else
          \Yii::$app->session->setFlash('error', $abuser->getErrorSummary(true));
      }

      \Yii::$app->session->addFlash('success', "Updated body of abuser!");
      return $this->redirect(['analyze', 'id' => $model->id]);
    }
    $originalPlayer = $originalTreasure = null;

    if ($model->model === 'failed_claim') {
      if (trim($model->body) === "") {
        $msg=$model->forFailedClaim();
        $model->body = trim($model->body) . "\n" . $msg;
      }
    }

    return $this->render('analyze', [
      'model' => $model,
      'originalPlayer' => $originalPlayer,
      'originalTreasure' => $originalTreasure,
    ]);
  }

  /**
   * Deletes an existing Abuser model.
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
   * Truncated the Abuser table.
   * If truncate is successful, the browser will be redirected to the 'index' page.
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionTruncate()
  {
    Yii::$app->db->createCommand()->truncateTable('abuser')->execute();

    return $this->redirect(['index']);
  }

  /**
   * Delete filtered abuse records
   * @return \yii\web\Response
   */
  public function actionDeleteFiltered()
  {
    $searchModel = new AbuserSearch();
    $query = $searchModel->search(['AbuserSearch' => Yii::$app->request->post()]);

    $query->pagination = false;
    if (intval($query->count) === intval(Abuser::find()->count())) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You have attempted to delete all the records. Use the <b>Truncate</b> operation instead.'));
      return $this->redirect(['index']);
    }

    $trans = Yii::$app->db->beginTransaction();
    try {
      $counter = $query->count;
      foreach ($query->getModels() as $q)
        $q->delete();
      $trans->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] Abuser record(s) deleted', ['counter' => intval($counter)]));
    } catch (\Exception $e) {
      $trans->rollBack();
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete abuser record(s)'));
    }
    return $this->redirect(['index']);
  }

  /**
   * Finds the Abuser model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id ID
   * @return Abuser the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Abuser::findOne(['id' => $id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
