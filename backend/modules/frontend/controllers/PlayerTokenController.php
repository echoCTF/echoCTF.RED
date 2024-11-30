<?php

namespace app\modules\frontend\controllers;
use Yii;
use app\modules\frontend\models\PlayerToken;
use app\modules\frontend\models\PlayerTokenSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerTokenController implements the CRUD actions for PlayerToken model.
 */
class PlayerTokenController extends BaseController
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
          ],
        ],
      ]
    );
  }

  /**
   * Lists all PlayerToken models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new PlayerTokenSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single PlayerToken model.
   * @param int $player_id Player ID
   * @param string $type Type
   * @return string
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($player_id, $type)
  {
    return $this->render('view', [
      'model' => $this->findModel($player_id, $type),
    ]);
  }

  /**
   * Creates a new PlayerToken model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new PlayerToken();
    if ($this->request->isPost) {
      if ($model->load($this->request->post()) && $model->save()) {
        return $this->redirect(['view', 'player_id' => $model->player_id, 'type' => $model->type]);
      }
    } else {
      $model->loadDefaultValues();
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing PlayerToken model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $player_id Player ID
   * @param string $type Type
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($player_id, $type)
  {
    $model = $this->findModel($player_id, $type);

    if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
      return $this->redirect(['view', 'player_id' => $model->player_id, 'type' => $model->type]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing PlayerToken model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $player_id Player ID
   * @param string $type Type
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($player_id, $type)
  {
    $this->findModel($player_id, $type)->delete();

    return $this->redirect(['index']);
  }

  /**
   * Finds the PlayerToken model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $player_id Player ID
   * @param string $type Type
   * @return PlayerToken the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($player_id, $type)
  {
    if (($model = PlayerToken::findOne(['player_id' => $player_id, 'type' => $type])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
