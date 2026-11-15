<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_audit".
 * Entries are written by the tau_player trigger.
 *
 * @property int $id
 * @property int $player_id
 * @property string $field
 * @property string|null $old_value
 * @property string|null $new_value
 * @property string $ts
 *
 * @property Player $player
 */
class PlayerAudit extends \yii\db\ActiveRecord
{
  /**
   * Fields tracked by the tau_player trigger.
   */
  const FIELDS = ['username' => 'username', 'email' => 'email', 'fullname' => 'fullname', 'academic' => 'academic', 'status' => 'status', 'type' => 'type'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'player_audit';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id', 'field'], 'required'],
      [['player_id'], 'integer'],
      [['ts'], 'safe'],
      [['field'], 'string', 'max' => 32],
      [['old_value', 'new_value'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'player_id' => Yii::t('app', 'Player ID'),
      'field' => Yii::t('app', 'Field'),
      'old_value' => Yii::t('app', 'Old Value'),
      'new_value' => Yii::t('app', 'New Value'),
      'ts' => Yii::t('app', 'Ts'),
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }
}
