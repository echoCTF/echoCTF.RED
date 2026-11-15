<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "player_streak".
 * Streaks are counted in weeks (monday to sunday) with at least one claim.
 *
 * @property int $player_id
 * @property int $current
 * @property int $best
 * @property string $last_week monday of the last week with a claim
 *
 * @property Player $player
 * @property int $active current streak, or 0 when a week was missed
 */
class PlayerStreak extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_streak';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id'], 'required'],
      [['player_id', 'current', 'best'], 'integer'],
      [['last_week'], 'date', 'format' => 'php:Y-m-d'],
    ];
  }

  /**
   * Monday of the previous week. A streak is still alive while the last claim
   * is on or after this date.
   */
  public static function aliveSince(): string
  {
    return date('Y-m-d', strtotime('monday this week -7 days'));
  }

  /**
   * Streaks that have not been broken yet
   * @return \yii\db\ActiveQuery
   */
  public static function findAlive()
  {
    return static::find()->where(['>=', 'player_streak.last_week', static::aliveSince()])->andWhere(['>', 'player_streak.current', 0]);
  }

  public function getActive(): int
  {
    return $this->last_week !== null && $this->last_week >= static::aliveSince() ? (int) $this->current : 0;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }
}
