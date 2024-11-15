<?php

namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class ProfileVisibility extends Component
{

  public static function visible(int $player_id, $visibility): bool
  {
    if ($visibility === 'public') return true;
    if (self::visibilityAllowed($player_id)) return true;
    if (self::visibilityDenied($visibility)) return false;
    return true;
  }

  public static function visibilityAllowed(int $player_id): bool
  {
    if (!\Yii::$app->user->isGuest) {
      if (intval(\Yii::$app->user->id) === intval($player_id)) return true;
      if (\Yii::$app->user->identity->isAdmin) return true;
    }
    return false;
  }

  public static function visibilityDenied($visibility): bool
  {
    if (\Yii::$app->sys->player_profile === false) return true;

    if ($visibility === 'private') return true;

    return false;
  }
}
