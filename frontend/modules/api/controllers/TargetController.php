<?php

namespace app\modules\api\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\overloads\yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\base\UserException;
use yii\web\ServerErrorHttpException;
use app\modules\target\models\Target;
use app\modules\target\models\ClaimService;
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
            'actions' => ['spawn', 'shut', 'spin'],
            'allow' => false,
            'roles' => ['@'],
            'matchCallback' => function () {
              $id = intval(Yii::$app->request->get('id'));
              if ($id > 0 && ($model = Target::findOne($id)) !== null) {
                $network = Yii::$app->getModule('network');
                if ($model->network && !$network->checkTarget($model))
                  return true;
                return false;
              }
              return false;
            },
            'denyCallback' =>  function () {
              Yii::$app->session->setFlash('warning', \Yii::t('app', "You don't have access to the network for this target."));
              return  \Yii::$app->getResponse()->redirect(Yii::$app->request->referrer ?: [Yii::$app->sys->default_homepage]);
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

  public function checkAccess($action, $model = null, $params = [])
  {
    if ($action === 'view' && $model !== null)
      \Yii::$app->getModule('target')->checkNetwork($model);
  }

  public function actionInstances()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $response = Yii::$app->getResponse();
    if (!\Yii::$app->cache->memcache->add("api_target_instances:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_target_instances_timeout) + 1)) {
      $response->statusCode = 429;
      return ["message" => \Yii::t('app', "Rate-limited, wait a few seconds and try again."), "code" => 0, "status" => \Yii::$app->response->statusCode];
    }

    $teamInstances = \app\modules\api\models\TargetInstance::find()->rest()->where(['target_instance.player_id' => Yii::$app->user->identity->id]);

    if ((Yii::$app->sys->teams || Yii::$app->sys->team_visible_instances) && Yii::$app->user->identity->teamPlayer) {
      $TP = TeamPlayer::find()->where(['team_id' => Yii::$app->user->identity->teamPlayer->team_id])->orderBy(['approved' => SORT_DESC, 'ts' => SORT_ASC]);
      $teamPlayers = ArrayHelper::getColumn($TP->all(), 'player_id');
      $teamInstances->leftJoin('team_player', 'target_instance.player_id=team_player.player_id');
      if (\Yii::$app->sys->team_visible_instances === true) {
        $teamInstances->orWhere(['in', 'target_instance.player_id', $teamPlayers]);
      } else
        $teamInstances->orWhere(['team_allowed' => 1, 'target_instance.player_id' => $teamPlayers]);
    }

    $dataProvider = new ActiveDataProvider([
      'query' => $teamInstances,
      'pagination' => false,
    ]);

    return $dataProvider;
  }

  // Do Claim operation
  public function actionClaim()
  {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $response = Yii::$app->getResponse();
    if (!\Yii::$app->cache->memcache->add("api_claim:" . \Yii::$app->user->id, time(), intval(\Yii::$app->sys->api_claim_timeout) + 1)) {
      $response->statusCode = 429;
      return ["message" => \Yii::t('app', "Rate-limited, wait a few seconds and try again."), "code" => 0, "status" => \Yii::$app->response->statusCode];
    }
    $string = Yii::$app->getRequest()->getBodyParam('hash');
    if (!is_string($string) || trim($string) === "") {
      $response->statusCode = 422;
      return ["message" => Yii::t('app', 'Need to provide hash value.'), "code" => 0, "status" => \Yii::$app->response->statusCode];
    }

    $result = ClaimService::claim($string, Yii::$app->user->identity);
    [$code, $message] = match ($result['status']) {
      ClaimService::CLAIMED => [201, "Flag claimed!"],
      ClaimService::CLAIMED_BEFORE => [422, \Yii::t('app', 'Flag claimed before')],
      ClaimService::OWNED_BY_OTHER => [422, \Yii::t('app', 'Flag belongs to someone else you will get penalized!')],
      ClaimService::NO_FINDING => [422, \Yii::t('app', 'You need to discover at least one service before claiming a flag for this system.')],
      ClaimService::NO_NETWORK => [403, $result['message']],
      ClaimService::NO_CLAIMS_LEFT => [422, \Yii::t('app', 'Flag has no claims left')],
      default => [422, \Yii::t('app', 'Flag does not exist!')],
    };
    $response->statusCode = $code;
    return ["message" => $message, "code" => 0, "status" => $code];
  }
}
