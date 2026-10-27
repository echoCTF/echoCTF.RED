<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_spin".
 *
 * @property int $player_id
 * @property int $counter
 * @property int $total
 * @property int $perday
 * @property int $max_instances
 * @property string $updated_at
 *
 * @property Player $player
 */
class PlayerSpin extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_spin';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id'], 'required'],
      [['player_id', 'counter', 'total', 'perday', 'max_instances'], 'integer'],
      [['max_instances'], 'integer', 'min' => 1, 'max' => 255],
      [['updated_at'], 'safe'],
      [['player_id'], 'unique'],
      [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'player_id' => 'Player ID',
      'counter' => 'Counter',
      'total' => 'Total',
      'max_instances' => 'Max instances',
      'updated_at' => 'Updated At',
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  /**
   * {@inheritdoc}
   * @return PlayerSpinQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PlayerSpinQuery(get_called_class());
  }

  /**
   * Add (or subtract with a negative value) to the player max_instances.
   * The result stays between 1 and 255.
   *
   * @param int $player_id
   * @param int $delta
   * @return int number of affected rows
   */
  public static function adjustMaxInstances(int $player_id, int $delta): int
  {
    if ($delta === 0) {
      return 0;
    }
    return \Yii::$app->db->createCommand('UPDATE {{%player_spin}} SET [[max_instances]]=LEAST(255,GREATEST(1,CAST([[max_instances]] AS SIGNED)+:delta)) WHERE [[player_id]]=:player_id')
      ->bindValue(':delta', $delta)
      ->bindValue(':player_id', $player_id)
      ->execute();
  }
}
