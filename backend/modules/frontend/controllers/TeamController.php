<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamSearch;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\TeamPlayerSearch;
use app\modules\frontend\models\Player;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * TeamController implements the CRUD actions for Team model.
 */
class TeamController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'rules' => [
        'class' => 'yii\filters\AjaxFilter',
        'only' => ['free-player-ajax-search']
      ],
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'toggle-academic' => ['POST'],
          'repopulate-stream' => ['POST'],
        ],
      ],
    ]);
  }

  /**
   * Lists all Team models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new TeamSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Team model.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id)
  {
    $model = $this->findModel($id);
    $newTP = new TeamPlayer();
    if (Yii::$app->request->isPost) {
      $newTP->load(Yii::$app->request->post());
      $newTP->team_id = $id;
      $newTP->approved = 0;
      if ($newTP->save()) {
        \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Added player: [{username}] to the team: [{team}]', ['username' => $newTP->player->username, 'team' => $newTP->team->name]));
        return $this->redirect(['view', 'id' => $id]);
      }
    }
    $searchModel = new TeamPlayerSearch();
    $searchModel->team_id = $id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('view', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
      'newTP' => $newTP,
      'model' => $model,
    ]);
  }

  /**
   * Creates a new Team model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new Team();
    if (\app\modules\frontend\models\Player::find()->count() == 0) {
      // If there are no player redirect to create player page
      Yii::$app->session->setFlash('warning', Yii::t('app', "No Players found create one first."));
      return $this->redirect(['/frontend/player/create']);
    }
    $trans = Yii::$app->db->beginTransaction();

    try {
      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        $model->refresh();
        $ts = new \app\modules\activity\models\TeamScore();
        $ts->team_id = $model->id;
        $ts->points = 0;
        $ts->save();
        Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid', $model->id)->execute();
        $trans->commit();
        return $this->redirect(['view', 'id' => $model->id]);
      }
    } catch (\Exception $e) {
      $trans->rollBack();
      \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Failed to create team. {exception}', ['exception' => Html::encode($e->getMessage())]));
    }
    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Team model.
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
   * Deletes an existing Team model.
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
   * Repopulates an existing Team activity stream or all of no $id provided.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionRepopulateStream($id = 0)
  {
    if (intval($id) !== 0) {
      $this->findModel($id)->repopulateStream();
      \Yii::$app->getSession()->setFlash('warning', Yii::t('app', 'Repopulated the team stream'));
    } else {
      foreach (Team::find()->all() as $item) {
        $item->repopulateStream();
      }
      \Yii::$app->getSession()->setFlash('warning', Yii::t('app', 'Repopulated all the team streams'));
    }

    return $this->redirect(['index']);
  }

  /**
   * Toggles an existing Team academic flag model.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionToggleAcademic($id)
  {
    $model = $this->findModel($id);
    $model->updateAttributes(['academic' => ($model->academic + 1) % \Yii::$app->sys->academic_grouping]);
    return $this->redirect(Yii::$app->request->referrer ?? ['frontend/teamplayer/index']);
  }

  /**
   * Finds the Team model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Team the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Team::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  public function actionAjaxSearch($term, $load = false, $active = null, $status = null)
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $results = [];
    if (Yii::$app->request->isAjax) {
      $pq = Team::find()->where(['=', 'id', $term]);
      if ($load === false) {
        $pq->orWhere(['like', 'name', $term]);
      }
      $results = array_values(ArrayHelper::map(
        $pq->all(),
        'id',
        function ($model) {
          return [
            'id' => $model->id,
            'label' => sprintf("(id: %d / pid: %d) %s", $model->id, $model->owner->id, \yii\helpers\Html::encode($model->name)),
          ];
        }
      ));
    }
    return $results;
  }

  public function actionFreePlayerAjaxSearch($term, $load = false, $active = null, $status = null)
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $results = [];
    if (Yii::$app->request->isAjax) {
      $pq = Player::find()->select(['player.id', 'player.username', 'player.email', 'player.status'])->leftJoin('team_player', 'team_player.player_id = player.id')->where([
        'AND',
        ['=', 'player.id', $term],
        ['team_player.id' => null]
      ]);
      if ($active !== null && $status !== null) {
        $pq->andWhere(['status' => $status, 'active' => $active]);
      }
      if ($load === false) {
        $pq->orWhere([
          'AND',
          ['like', 'username', $term],
          ['team_player.id' => null]
        ]);
      }
      $results = array_values(ArrayHelper::map(
        $pq->all(),
        'id',
        function ($model) {
          return [
            'id' => $model->id,
            'label' => sprintf("(id: %d / pid: %d) %s <%s>%s", $model->id, $model->profile->id, $model->username, $model->email, $model->status === 10 ? '' : ' (innactive)'),
          ];
        }
      ));
    }
    return $results;
  }

  public function actionNotify($id)
  {
    $team = $this->findModel($id);
    $notificationModel = new \app\modules\activity\models\Notification();
    if (Yii::$app->request->isPost) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $msg = "";
      $ownerOnly = boolval(Yii::$app->request->post('DynamicModel')['owner'] ?? 0);
      $ovpn = boolval(Yii::$app->request->post('Notification')['ovpn'] ?? 0);
      $online = boolval(Yii::$app->request->post('Notification')['online'] ?? 0);
      $notificationModel->load(Yii::$app->request->post());

      if ($notificationModel->validate()) {
        if ($ownerOnly) {
          if ($this->notifyLogic($team->owner, $notificationModel, $ovpn, $online) === null) {
            Yii::$app->session->addFlash('warning', 'Owner not notified due to filters.');
          } else {
            $msg = "Notified owner [" . $team->owner->username . "]";
            Yii::$app->session->addFlash('success', $msg);
          }
        } else {
          $_p = [];
          foreach ($team->players as $player) {
            if($this->notifyLogic($player, $notificationModel, $ovpn, $online)!==NULL)
              Yii::$app->session->addFlash('success', "Notified [".$player->username."].");
            else
              Yii::$app->session->addFlash('warning', "Player [".$player->username."] not notified due to filters.");

            $_p[] = $player->username;
          }
          $msg = "Notified [" . implode(", ", $_p) . "]";
        }
        return ['status' => 'success', 'message' => $msg];
      }
    }
    return trim($this->renderAjax('_notify', [
      'team' => $team,
      'notificationModel' => $notificationModel,
    ]));
  }

  private function notifyLogic($player, $notificationModel, $ovpn = false, $online = false)
  {
    if ($ovpn && $player->playerLast->vpn_local_address===null)
      return null;
    if ($online && !boolval($player->online))
      return null;
    return $player->notify($notificationModel->category, $notificationModel->title, $notificationModel->body);
  }
}
