<?php

namespace app\modules\network\controllers;

use Yii;
use app\modules\network\models\Network;
use yii\base\UserException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * DefaultController implements the CRUD actions for Network model.
 */
class DefaultController extends \app\components\BaseController
{

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['index', 'view','subscribe'],
        'rules' => [
          'eventStartEnd' => [
            'actions' => ['index', 'view', 'subscribe'],
          ],
          'teamsAccess' => [
            'actions' => ['index', 'view', 'subscribe'],
          ],
          'disabledRoute' => [
            'actions' => ['index', 'view', 'subscribe'],
          ],
          [
            'actions' => ['view'],
            'allow' => true,
            'roles'=>['?'],
            'matchCallback'=>function($rule,$action){
              return Yii::$app->sys->network_view_guest;
            }
          ],
          [
            'actions' => ['index','view', 'subscribe'],
            'allow' => true,
            'roles' => ['@']
          ],
          [
            'actions' => ['view'],
            'allow' => true,
          ],
        ],
      ]
    ]);
  }


  /**
   * Lists all Network models.
   * @return mixed
   */
  public function actionIndex()
  {
    $dataProvider = new ActiveDataProvider([
      'query' => Network::find()->active()->orderBy(['weight' => SORT_ASC, 'name' => SORT_ASC, 'id' => SORT_ASC]),
    ]);

    return $this->render('index', [
      'dataProvider' => $dataProvider,
    ]);
  }
  /**
   * View Network model by id.
   * @return mixed
   */
  public function actionView(int $id)
  {
    try {
      $network=$this->findModel($id);
      if(Yii::$app->user->isGuest && !$network->guest)
        throw new UserException("Network is not pubic");

    } catch (\Exception $e) {
      return $this->redirect(['/']);
    }
    $tmod = \app\modules\target\models\Target::find();
    if (Yii::$app->user->isGuest)
    {
      $query = $tmod->forNet($id)->addState();
    }
    else
      $query = $tmod->forNet($id)->player_progress(Yii::$app->user->id);

    $targetProgressProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => [
        'pageSizeParam' => 'target-perpage',
        'pageParam' => 'target-page',
      ]
    ]);
    $targetProgressProvider->setSort([
      'defaultOrder' => ['status' => SORT_DESC, 'scheduled_at' => SORT_ASC, 't.weight'=>SORT_ASC, 'difficulty' => SORT_ASC, 'name' => SORT_ASC],
      'attributes' => [
        'scheduled_at' => [
          'asc' =>  ['scheduled_at' => SORT_ASC],
          'desc' => ['scheduled_at' => SORT_DESC],
        ],
        't.weight'=> [
          'asc'=>['t.weight'=>SORT_ASC],
         'desc'=>['t.weight'=>SORT_DESC],
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
        'rootable'=>[
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
        'progress'=>[
          'asc'=>['progress'=>SORT_ASC],
          'desc'=>['progress'=>SORT_DESC],
          'default' => SORT_ASC
        ]
      ],
    ]);

    return $this->render('view', [
      'networkTargetProvider' => $targetProgressProvider,
      'model' => $network,
    ]);
  }
  protected function findModel(int $id)
  {
    if (($model = Network::findOne(['id' => $id])) !== null && ($model->active || (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->isAdmin))) {
      return $model;
    }

    throw new NotFoundHttpException(\Yii::t('app', 'The requested network does not exist.'));
  }
}
