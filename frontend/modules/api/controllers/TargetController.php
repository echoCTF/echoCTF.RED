<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\overloads\yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use app\models\PlayerTreasure;
use yii\base\UserException;
use yii\web\ServerErrorHttpException;
use app\modules\target\models\Treasure;
use yii\helpers\Html;
use app\modules\target\models\TargetPlayerState as TPS;
use app\modules\team\models\TeamPlayer;

class TargetController extends \yii\rest\ActiveController
{
  public $modelClass = 'app\modules\api\models\Target';
  public $serializer = [
    'class' => 'yii\rest\Serializer',
    'collectionEnvelope' => 'items',
  ];
  public function behaviors()
  {
    \Yii::$app->user->enableSession = false;
    \Yii::$app->user->loginUrl = null;

    return ArrayHelper::merge(parent::behaviors(), [
      'authenticator' => [
        'authMethods' => [
          HttpBearerAuth::class,
        ],
      ],
      'content' => [
        'class' => yii\filters\ContentNegotiator::class,
        'formats' => [
          'application/json' => \yii\web\Response::FORMAT_JSON,
        ],
      ],
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [ //api_bearer_disable
            'allow' => false,
            'matchCallback' => function () {
              return \Yii::$app->sys->api_bearer_enable !== true;
            }
          ],
          [
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
    ]);
  }

  public function actions()
  {
    $actions = parent::actions();
    // disable the "delete", "create", "view","update" actions
    unset($actions['delete'], $actions['create'], $actions['update'], $actions['index']);
    //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
    $actions['spin']['class'] =  'app\modules\api\actions\SpinRestAction';
    $actions['spawn']['class'] = 'app\modules\api\actions\SpawnRestAction';
    $actions['shut']['class'] =  'app\modules\api\actions\ShutRestAction';

    return $actions;
  }
  public function actionInstances()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $response = Yii::$app->getResponse();
    if (\Yii::$app->cache->memcache->get("api_target_instances:" . \Yii::$app->user->id) !== false) {
      $response->statusCode = 429;
      return ["message"=>\Yii::t('app',"Rate-limited, wait a few seconds and try again."),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }

    $teamInstances = \app\modules\api\models\TargetInstance::find()->rest()->where(['target_instance.player_id'=>Yii::$app->user->identity->id]);

    if ((Yii::$app->sys->teams || Yii::$app->sys->team_visible_instances) && Yii::$app->user->identity->teamPlayer) {
      $TP = TeamPlayer::find()->where(['team_id' => Yii::$app->user->identity->teamPlayer->team_id])->orderBy(['approved' => SORT_DESC, 'ts' => SORT_ASC]);
      $teamPlayers = ArrayHelper::getColumn($TP->all(), 'player_id');
      $teamInstances->leftJoin('team_player', 'target_instance.player_id=team_player.player_id');
      if (\Yii::$app->sys->team_visible_instances === true) {
        $teamInstances->orWhere(['in', 'target_instance.player_id', $teamPlayers]);
      }
      else
        $teamInstances->orWhere(['team_allowed' => 1,'target_instance.player_id'=>$teamPlayers]);
    }

    $dataProvider = new ActiveDataProvider([
      'query' => $teamInstances,
      'pagination' => false,
    ]);
    \Yii::$app->cache->memcache->set("api_target_instances:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_target_instances_timeout) + 1);

    return $dataProvider;
  }

  // Do Claim operation
  public function actionClaim()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $response = Yii::$app->getResponse();
    if (\Yii::$app->cache->memcache->get("api_claim:" . \Yii::$app->user->id) !== false) {
      $response->statusCode = 429;
      return ["message"=>\Yii::t('app',"Rate-limited, wait a few seconds and try again."),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }
    \Yii::$app->cache->memcache->set("api_claim:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_claim_timeout) + 1);
    $string = Yii::$app->getRequest()->getBodyParam('hash');
    if (trim($string) === "" || $string === null) {
      $response->statusCode = 422;
      return ["message"=>Yii::t('app', 'Need to provide hash value.'),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }

    $treasure = Treasure::find()->claimable()->byCode($string)->one();
    if ($treasure !== null && Treasure::find()->byCode($string)->claimable()->notBy((int) Yii::$app->user->id)->one() === null) {
      $response->statusCode = 422;
      return ["message"=>\Yii::t('app', 'Flag claimed before'),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    } elseif ($treasure === null) {
      Yii::$app->counters->increment('failed_claims');
      $response->statusCode = 422;
      return ["message"=>\Yii::t('app', 'Flag does not exist!'),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }

    $player_progress = TPS::findOne(['id' => $treasure->target_id, 'player_id' => Yii::$app->user->id]);
    if ((Yii::$app->sys->force_findings_to_claim || $treasure->target->require_findings) && $player_progress === null && intval($treasure->target->getFindings()->count()) > 0) {
      Yii::$app->counters->increment('claim_no_finding');
      $response->statusCode = 422;
      return ["message"=>\Yii::t('app', 'You need to discover at least one service before claiming a flag for this system.'),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }

    try {
      $module = \Yii::$app->getModule('target');
      $module->checkNetwork($treasure->target);
    } catch (\Throwable $e) {
      \Yii::$app->response->statusCode = 403;
      return ["message"=>$e->getMessage(),"code"=>0,"status"=>\Yii::$app->response->statusCode];
    }

    Yii::$app->counters->increment('claims');
    $this->doClaim($treasure);
    \Yii::$app->response->statusCode = 201;
    return ["message"=>"Flag claimed!","code"=>0,"status"=>\Yii::$app->response->statusCode];
  }

  protected function doClaim($treasure)
  {
    $connection = Yii::$app->db;
    $transaction = $connection->beginTransaction();
    try {
      $PT = new PlayerTreasure();
      $PT->player_id = (int) Yii::$app->user->id;
      $PT->treasure_id = $treasure->id;
      $PT->save();
      if ($treasure->appears !== -1) {
        $treasure->updateAttributes(['appears' => intval($treasure->appears) - 1]);
      }
      $transaction->commit();
      $this->doOndemand($treasure->target);
      $PT->refresh();
    } catch (\Exception $e) {
      $transaction->rollBack();
      throw $e;
    } catch (\Throwable $e) {
      $transaction->rollBack();
      throw $e;
    }
  }

  protected function doOndemand($target)
  {
    if ($target->ondemand && $target->ondemand->state > 0) {
      $target->ondemand->updateAttributes(['heartbeat' => new \yii\db\Expression('NOW()')]);
    }
  }
}
