<?php

namespace app\modules\subscription\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\base\UserException;

use app\overloads\yii\filters\AccessControl;

use app\modules\target\models\Target;

use app\modules\network\models\PrivateNetworkTarget;
use app\modules\network\models\PrivateNetwork;

use \app\modules\subscription\models\PlayerSubscription;
use \app\modules\subscription\models\PlayerProduct;
use \app\modules\subscription\models\Customer;
use \app\modules\subscription\models\Product;
use \app\modules\subscription\models\Price;

/**
 * Perk controller for the `subscription` module
 */
class PerkController extends \app\components\BaseController
{

  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['configure',],
        'rules' => [
          [
            'allow' => false,
            'actions' => ['configure'],
            'matchCallback' => function () {
              return \Yii::$app->sys->subscriptions_emergency_suspend === true || \Yii::$app->sys->subscriptions_menu_show !== true;
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is temporarily disabled, please try again in a couple of hours.'));
              return  \Yii::$app->getResponse()->redirect([\Yii::$app->sys->default_homepage]);
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
    return $actions;
  }


  public function actionConfigure($id)
  {
    try {
      $model = $this->findModel($id);
      $view = $model->product->shortcode;
      $viewPath = $this->getViewPath() . DIRECTORY_SEPARATOR . $view . '.php';
      if (Yii::$app->request->isPost && Yii::$app->request->post('target_id') !== null) {
        $this->{'process' . $view}($model);
        return $this->redirect(['/subscription/perk/configure', 'id' => $model->id]);
      }

      if (file_exists($viewPath)) {
        return $this->render($view, [
          'model' => $model
        ]);
      }
      throw new \yii\web\NotFoundHttpException("Perk is not configurable!");
    } catch (\Exception $e) {
      \Yii::$app->session->setFlash('error', $e->getMessage());
    }
    return $this->redirect(['/subscription/default/index']);
  }

  protected function findModel($id)
  {
    if (($model = PlayerProduct::find()->where(['id' => $id])->mine()->active()->one()) !== null) {
      return $model;
    }

    throw new \yii\web\NotFoundHttpException(\Yii::t('app', 'The requested product does not exist.'));
  }

  protected function processteamNetwork($model)
  {
    $targetId = intval(Yii::$app->request->post('target_id'));
    if ($targetId === 0)
      return;
    $target = Target::find()->where(['id' => intval($targetId)])->one();
    if ($target === null)
      return;

    if (isset($model->metadataObj->private_network_id)) {
      $privateNetwork = PrivateNetwork::findOne($model->metadataObj->private_network_id);
    } else {
      $transaction = Yii::$app->db->beginTransaction();
      try {
        $privateNetwork = new PrivateNetwork([
          'name' => 'player_product_' . $model->id,
          'player_id' => Yii::$app->user->id,
          'team_accessible' => $model->product->metadataObj->team_accessible
        ]);

        if (!$privateNetwork->save())
          throw UserException($privateNetwork->getErrorSummary(false));

        $privateNetwork->refresh();
        $metadata = json_decode($model->metadata);
        $metadata->private_network_id = $privateNetwork->id;
        $model->metadata = json_encode($metadata);
        if (!$model->save())
          throw UserException($model->getErrorSummary(false));
        $transaction->commit();
      } catch (\Exception $e) {
        $transaction->rollBack();
        \Yii::$app->session->setFlash('error', $e->getMessage());
      }
    }

    if ($privateNetwork === null)
      return;

    // Check if network if full
    if (isset($model->product->metadataObj->private_instances) && count($privateNetwork->privateTargets) >= intval($model->product->metadataObj->private_instances)) {
      \Yii::$app->session->setFlash('error', 'You have reached your limit of targets for this network');
      return $this->redirect(['/network/private/view','id'=>$model->id]);
    }

    try {
      $pnt = new PrivateNetworkTarget(['private_network_id' => $privateNetwork->id, 'target_id' => $targetId]);
      if (!$pnt->save())
        throw new UserException(implode(" ", $pnt->getErrorSummary(true)));
      \Yii::$app->session->setFlash('success', \Yii::t('app', 'Target {name} added to your network!', ['name' => $target->name]));
    } catch (\Exception $e) {
      if (isset($e->errorInfo) && is_array($e->errorInfo) && $e->errorInfo[1] == 1062) {
        \Yii::$app->session->setFlash('warning', \Yii::t('app', 'Target {name} is already in your network!', ['name' => $target->name]));
      } else
        \Yii::$app->session->setFlash('error', $e->getMessage());
    }
  }
}
