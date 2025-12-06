<?php

namespace app\modules\network\controllers;

use Yii;
use app\modules\network\models\Network;
use app\modules\network\models\PrivateNetwork;
use app\modules\network\models\PrivateNetworkTarget;
use app\modules\team\models\TeamPlayer;
use yii\base\UserException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * PrivateController implements the CRUD actions for Private Network model.
 */
class PrivateController extends \app\components\BaseController
{

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge([
      'access' => [
        'class' => AccessControl::class,
        'only' => ['view','spin'],
        'rules' => [
          'eventStartEnd' => [
            'actions' => ['view','spin'],
          ],
          'disabledRoute' => [
            'actions' => ['view','spin'],
          ],
          [
            'actions' => ['view','spin'],
            'allow' => true,
            'roles' => ['@']
          ],
        ],
      ]
    ], parent::behaviors());
  }


  /**
   * View Private Network model by id.
   * @return mixed
   */
  public function actionView(int $id)
  {
    try {
      $network = $this->findModel($id);
    } catch (\Exception $e) {
      Yii::$app->session->setFlash('error', $e->getMessage());
      return $this->redirect(['/']);
    }
    $tmod = \app\modules\target\models\Target::find();
    $query = $tmod->forPrivateNet($id)->player_progress(Yii::$app->user->id)->private_network_player_progress_select();

    $targetProgressProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => [
        'pageSizeParam' => 'target-perpage',
        'pageParam' => 'target-page',
      ]
    ]);

    $targetProgressProvider->setSort([
      'defaultOrder' => ['status' => SORT_DESC, 'scheduled_at' => SORT_ASC, 't.weight' => SORT_ASC, 'difficulty' => SORT_ASC, 'name' => SORT_ASC],
      'attributes' => [
        'scheduled_at' => [
          'asc' =>  ['scheduled_at' => SORT_ASC],
          'desc' => ['scheduled_at' => SORT_DESC],
        ],
        't.weight' => [
          'asc' => ['t.weight' => SORT_ASC],
          'desc' => ['t.weight' => SORT_DESC],
        ],
        'name' => [
          'asc' => ['name' => SORT_ASC],
          'desc' => ['name' => SORT_DESC],
        ],
        'status' => [
          'asc' => ['status' => SORT_ASC],
          'desc' => ['status' => SORT_DESC],
        ],
        'headshots' => [
          'asc' => ['total_headshots' => SORT_ASC],
          'desc' => ['total_headshots' => SORT_DESC],
        ],
        'rootable' => [
          'asc' =>  ['rootable' => SORT_ASC],
          'desc' => ['rootable' => SORT_DESC],
        ],
        'total_findings' => [
          'asc' => ['total_findings' => SORT_ASC],
          'desc' => ['total_findings' => SORT_DESC],
        ],
        'total_treasures' => [
          'asc' => ['total_treasures' => SORT_ASC],
          'desc' => ['total_treasures' => SORT_DESC],
        ],
        'difficulty' => [
          'asc' => ['average_rating' => SORT_ASC],
          'desc' => ['average_rating' => SORT_DESC],
        ],
        'progress' => [
          'asc' => ['progress' => SORT_ASC],
          'desc' => ['progress' => SORT_DESC],
          'default' => SORT_ASC
        ]
      ],
    ]);

    return $this->render('view', [
      'networkTargetProvider' => $targetProgressProvider,
      'model' => $network,
    ]);
  }

  /**
   * View Private Network target.
   * @return mixed
   */
  public function actionSpin(int $target_id, int $network_id = 0)
  {
    $model = PrivateNetworkTarget::findOne([
      'private_network_id' => $network_id,
      'target_id' => $target_id,
    ]);

    if ($model == NULL || intval($model->privateNetwork->player_id) !== intval(Yii::$app->user->id)) {
      Yii::$app->session->setFlash('error', 'No such private network target exists!!!');
      return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }
    if (intval($model->state) !== 0) {
      Yii::$app->session->setFlash('warning', 'Target already scheduled for restart!!!');
      return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    $transaction = Yii::$app->db->beginTransaction();
    try {
      if (intval(Yii::$app->user->identity->profile->spins->counter) >= intval(Yii::$app->user->identity->profile->spins->perday)) {
        throw new UserException('You have run out of spins!');
      }

      $playerSpin = Yii::$app->user->identity->profile->spins;
      $playerSpin->counter = intval($playerSpin->counter) + 1;
      $playerSpin->total = intval($playerSpin->total) + 1;
      $model->updateAttributes(['state' => 1]);
      if (!$playerSpin->save())
        throw new UserException('Failed to update player spins!');
      $transaction->commit();
      Yii::$app->session->setFlash('success', 'Target scheduled for restart!!!');
    } catch (\Exception $e) {
      $transaction->rollBack();
      Yii::$app->session->setFlash('error', $e->getMessage());
    }

    return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
  }

  protected function findModel(int $id)
  {
    if (Yii::$app->user->identity->teamPlayer)
      $teamPlayerIds = TeamPlayer::find()
        ->select('player_id')
        ->where(['team_id' => Yii::$app->user->identity->teamPlayer->team_id, 'approved' => 1])
        ->column();
    else $teamPlayerIds = [];
    $model = PrivateNetwork::find()
      ->where(['id' => $id])
      ->andWhere([
        'or',
        ['player_id' => Yii::$app->user->id],
        [
          'and',
          ['team_accessible' => 1],
          ['player_id' => $teamPlayerIds]
        ]
      ])
      ->one();

    if ($model !== null) {
      return $model;
    }

    throw new NotFoundHttpException(\Yii::t('app', 'The requested network does not exist.'));
  }
}
