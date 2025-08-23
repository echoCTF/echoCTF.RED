<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\Headshot;
use app\modules\activity\models\HeadshotSearch;
use app\modules\gameplay\models\Treasure;
use app\modules\gameplay\models\Finding;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * HeadshotController implements the CRUD actions for Headshot model.
 */
class HeadshotController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all Headshot models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new HeadshotSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Headshot model.
   * @param integer $player_id
   * @param integer $target_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($player_id, $target_id)
  {
    return $this->render('view', [
      'model' => $this->findModel($player_id, $target_id),
    ]);
  }

  /**
   * Creates a new Headshot model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $submit = Yii::$app->request->post('submit');
    $model = new Headshot();
    if ($submit && $submit[0] === 'give') $this->give();
    elseif ($submit && $submit[0] === 'save' && $model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }


  /**
   * Gives a Headshot for a target on a Player model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function give()
  {
    $model = new Headshot();
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
      if (intval($model->timer) === 0) {
        $model->timer = random_int(240, 10240);
      }
      Yii::$app->db->createCommand('CALL give_headshot(:player_id,:target_id,:timer)')
        ->bindValue(':player_id', $model->player_id)
        ->bindValue(':target_id', $model->target_id)
        ->bindValue(':timer', intval($model->timer))
        ->query();
      return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
    }
  }


  /**
   * Updates an existing Headshot model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $player_id
   * @param integer $target_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($player_id, $target_id)
  {
    $model = $this->findModel($player_id, $target_id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing Headshot model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $player_id
   * @param integer $target_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($player_id, $target_id)
  {
    $this->findModel($player_id, $target_id)->delete();

    return $this->redirect(['index']);
  }

  /**
   * Zero out the timer and points for the given headshot target & player. If a writeup exists also activate it.
   * If it is successful, the browser will be redirected back to referer.
   * @param integer $player_id
   * @param integer $target_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionZeroFiltered()
  {
    $searchModel = new HeadshotSearch();
    $dataProvider = $searchModel->search(['HeadshotSearch' => Yii::$app->request->post()]);
    $dataProvider->pagination = false;


    $db = Yii::$app->db;
    $trans = $db->beginTransaction();
    try {
      $counter = $dataProvider->getTotalCount();
      foreach ($dataProvider->getModels() as $q)
        $q->zero();

      $trans->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] Headshots zeroed', ['counter' => intval($counter)]));
    } catch (\Exception $e) {
      $trans->rollBack();
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed zero out headshots. {exception}', ['exception' => Html::encode($e->getMessage())]));
    }

    return $this->goBack((
      !empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null
    ));
  }

  /**
   * Zero out the timer and points for the given headshot target & player. If a writeup exists also activate it.
   * If it is successful, the browser will be redirected back to referer.
   * @param integer $player_id
   * @param integer $target_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionZero($player_id, $target_id)
  {
    $db = Yii::$app->db;
    $transaction = $db->beginTransaction();
    try {
      $model = $this->findModel($player_id, $target_id);
      $model->zero();
      /*
      $model->updateAttributes(['timer' => 0]);
      $treasure_ids = Treasure::find()->select('id')->where(['target_id' => $model->target_id])->column();
      $finding_ids = Finding::find()->select('id')->where(['target_id' => $model->target_id])->column();
      // Zero out points for findings and treasures associated with the target
      $db->createCommand()
        ->update(
          'stream',
          [
            'points' => 0,
            'ts' => new \yii\db\Expression('ts'),
          ],
          [
            'and',
            ['player_id' => $model->player_id],
            [
              'or',
              ['and', ['model' => 'treasure'], ['in', 'model_id', $treasure_ids]],
              ['and', ['model' => 'finding'], ['in', 'model_id', $finding_ids]],
              ['and', ['model' => 'headhost'], ['model_id' => $model->target_id]],
            ],
          ]
        )->execute();

      // Forcefully activate writeups
      $db->createCommand('INSERT IGNORE INTO player_target_help (player_id,target_id,created_at) VALUES (:player_id,:target_id,:created_at)', [':player_id' => $model->player_id, ':target_id' => $model->target_id, ':created_at' => new \yii\db\Expression('NOW()')])->execute();

      // Update the player points
      $db->createCommand('UPDATE player_score SET points=(SELECT SUM(points) FROM stream WHERE player_id=:player_id) WHERE player_id=:player_id', [':player_id' => $model->player_id])->execute();
      // If player is member of team re-populate the team stream
      if($model->player->teamPlayer)
        $db->createCommand('CALL repopulate_team_stream(:team_id)',[':team_id'=>$model->player->teamPlayer->team_id])->execute();
      */
      // Send a notification to the player about what just happened.
      $model->player->notify('swal:error', 'Headshot and points zeroed out!!!', 'Your timer and points for the headshot on target ' . $model->target->name . ' have been zeroed out. Feel free to contact the moderators for any clarification as to why this happened.');
      $transaction->commit();
    } catch (\Exception $e) {
      $transaction->rollBack();
      \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Failed to zero out headshot details. {exception}', ['exception' => Html::encode($e->getMessage())]));
    }

    return $this->goBack((
      !empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null
    ));
  }

  /**
   * Finds the Headshot model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $player_id
   * @param integer $target_id
   * @return Headshot the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($player_id, $target_id)
  {
    if (($model = Headshot::findOne(['player_id' => $player_id, 'target_id' => $target_id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
