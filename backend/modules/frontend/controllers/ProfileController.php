<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\Profile;
use app\modules\frontend\models\ProfileSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\activity\models\PlayerVpnHistorySearch;
use app\modules\activity\models\PlayerTargetHelpSearch;
use app\modules\activity\models\HeadshotSearch;
use app\modules\activity\models\WriteupSearch;
use app\modules\activity\models\ChallengeSolverSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class ProfileController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'approve-avatar' => ['POST'],
          'clear-validation' => ['POST'],
          'clear-all-validation' => ['POST'],
          'reset-key' => ['POST'],
        ],
      ],
    ]);
  }

  /**
   * Lists all Profile models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new ProfileSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Lists all Profile models that fail validation.
   * @return mixed
   */
  public function actionFailValidation()
  {
    $profile_ids = [];
    $allRecords = Profile::find()->all();
    foreach ($allRecords as $p) {
      $p->scenario = 'validator';
      if (!$p->validate()) {
        $profile_ids[] = $p->id;
      }
    }
    $query = Profile::find()->joinWith(['owner']);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);
    $query->where(['in', 'profile.id', $profile_ids]);
    $searchModel = new ProfileSearch();

    return $this->render('fail-validation', [
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Profile model.
   * @param string $id
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
   * Displays a full Profile model.
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionViewFull($id)
  {
    return $this->render('view_full', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Creates a new Profile model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new Profile();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Profile model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param string $id
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
   * Deletes an existing Profile model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    $this->findModel($id)->delete();

    return $this->redirect(['index']);
  }


  /**
   * Clear fields that fail validation
   * @param mixed $id
   * @return \yii\web\Response
   */
  public function actionClearValidation($id)
  {
    $fields = ['twitter', 'youtube', 'htb', 'discord', 'github'];
    $model = $this->findModel($id);
    $model->scenario = 'validator';
    $model->validate();
    foreach ($model->getErrors() as $attribute => $errors) {
      if ($attribute === 'twitter' && $model->twitter[0] === '@') {
        $model->twitter = str_replace('@', '', $model->twitter);
      } elseif (array_search($attribute, $fields) !== false) {
        $model->$attribute = null;
      } else
        Yii::$app->session->setFlash('error', Yii::t('app',"Failing attribute not on the list {attribute}",['attribute'=>$attribute]));
    }
    $model->save();
    return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null
    ));
  }

  public function actionClearAllValidation()
  {
    $fields = ['twitter', 'youtube', 'htb', 'discord', 'github'];
    $allRecords = Profile::find()->all();
    foreach ($allRecords as $model) {
      $model->scenario = 'validator';
      if (!$model->validate()) {
        foreach ($model->getErrors() as $attribute => $errors) {
          if ($attribute === 'twitter' && $model->twitter[0] === '@') {
            $model->twitter = str_replace('@', '', $model->twitter);
          } elseif (array_search($attribute, $fields) !== false) {
            $model->$attribute = null;
          } else
          Yii::$app->session->setFlash('error', Yii::t('app',"Failing attribute not on the list {attribute}",['attribute'=>$attribute]));
        }
        $model->save();
      }
    }
    return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null
    ));
  }

  /**
   * Approve the avatar of a given profile id
   * @param mixed $id
   * @return \yii\web\Response
   */
  public function actionApproveAvatar($id)
  {
    $model = $this->findModel($id);
    $model->approved_avatar = true;
    $model->save();
    return $this->redirect(['index']);
  }

  /**
   * Return VPN history for a given player profile
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionVpnHistory($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new PlayerVpnHistorySearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->sort->defaultOrder = ['ts' => SORT_DESC];

    return Json::encode(trim($this->renderAjax('_vpn_history', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return Headshots for a given player profile
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionHeadshots($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new HeadshotSearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('_headshots', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return submitted writeups for a given player profile
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionWriteups($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new WriteupSearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('_writeups', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return Spin History for a given player profile
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionSpinHistory($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new \app\modules\activity\models\SpinHistorySearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];

    return Json::encode(trim($this->renderAjax('_spin_history', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  /**
   * Return submitted writeups for a given target
   * @param string $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionNotifications($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new \app\modules\activity\models\NotificationSearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('_notifications', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  public function actionScoreMonthly($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new \app\modules\activity\models\PlayerScoreMonthlySearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->sort->defaultOrder = ['dated_at' => SORT_DESC];

    return Json::encode(trim($this->renderAjax('_score_monthly', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  public function actionTargetProgress($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new \app\modules\activity\models\TargetPlayerStateSearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('_target_progress', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  public function actionWriteupRating($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new \app\modules\activity\models\WriteupRatingSearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return Json::encode(trim($this->renderAjax('_writeup_rating', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  public function actionActivatedWriteups($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new PlayerTargetHelpSearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->setSort([
      'defaultOrder' => ['created_at' => SORT_DESC,'target_id'=>SORT_ASC],
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'username' => [
              'asc' => ['player.username' => SORT_ASC],
              'desc' => ['player.username' => SORT_DESC],
          ],
          'target_name' => [
              'asc' => ['target.name' => SORT_ASC],
              'desc' => ['target.name' => SORT_DESC],
          ],
          'created_at' => [
            'asc' =>  ['player_target_help.created_at' => SORT_ASC],
            'desc' => ['player_target_help.created_at' => SORT_DESC],
        ],
      ]
    ),
    ]);
    return Json::encode(trim($this->renderAjax('_activated_writeups', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  public function actionSolves($id)
  {
    $profile = $this->findModel($id);
    $searchModel = new ChallengeSolverSearch();
    $searchModel->player_id = $profile->player_id;
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];

    return Json::encode(trim($this->renderAjax('_solves', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel
    ])));
  }

  public function actionResetKey($id)
  {
    $model = $this->findModel($id);
    $key = \Yii::$app->request->post('key');
    if (strstr('player_session:', $key) !== false) {
      $val = Yii::$app->cache->memcache->get($key);
      Yii::$app->cache->memcache->delete('memc.sess.key.' . $val);
    }
    Yii::$app->cache->memcache->delete($key);
    return $this->redirect(Yii::$app->request->referrer ?? ['index']);
  }

  /**
   * Finds the Profile model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param string $id
   * @return Profile the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Profile::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
