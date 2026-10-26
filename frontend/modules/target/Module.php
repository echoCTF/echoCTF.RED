<?php

namespace app\modules\target;

use app\modules\network\models\NetworkPlayer;
use \yii\web\NotFoundHttpException;

/**
 * target module definition class
 */
class Module extends \yii\base\Module
{
  /**
   * {@inheritdoc}
   */
  public $controllerNamespace = 'app\modules\target\controllers';

  /**
   * {@inheritdoc}
   */
  public function init()
  {
    parent::init();

    // custom initialization code goes here
  }

  /**
   * Whether the platform runs in "instances only" mode.
   * When enabled, on-demand operations and support are turned off and every
   * player (VIP or not) can spawn private instances. Spin limits still apply.
   */
  public static function instancesOnly(): bool
  {
    return \Yii::$app->sys->instances_only === true;
  }

  /**
   * Whether on-demand target operations (power up / spin queue) are disabled,
   * either explicitly or implicitly through instances only mode.
   */
  public static function ondemandDisabled(): bool
  {
    return \Yii::$app->sys->disable_ondemand_operations === true || self::instancesOnly();
  }

  /**
   * Whether the given player may spawn private instances.
   * @param \app\models\Player|null $player
   */
  public static function canSpawnInstances($player): bool
  {
    if ($player === null)
      return false;
    return self::instancesOnly() || $player->isVip === true || \Yii::$app->sys->all_players_vip === true;
  }

  public function checkNetwork($target)
  {
    if ($target->network === null)
      return;

    if (!$target->network->active)
      throw new NotFoundHttpException('This target belongs to a network that is not active.');

    if (!$target->network->public && NetworkPlayer::findOne(['network_id' => $target->network->id, 'player_id' => \Yii::$app->user->id]) === null)
      throw new NotFoundHttpException('This target belongs to a network you don\'t have access.');
  }
}
