<?php

namespace app\modules\frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\activity\models\PlayerQuestion;
use app\modules\activity\models\PlayerTreasure;
use app\modules\activity\models\SpinQueue;
use app\modules\activity\models\SpinHistory;
use app\modules\activity\models\Report;
use app\modules\activity\models\Stream;
use app\modules\gameplay\models\Hint;
use app\modules\gameplay\models\Finding;
use app\modules\gameplay\models\Treasure;
use yii\helpers\html;
/**
 * This model class extends Player model for moderation purposes and operations.
 *
 * @property int $player_target_help_count
 *
 */
class ModerationPlayer extends Player
{
  // The number of activated writeups a player has
  public $player_target_help_count;
}
