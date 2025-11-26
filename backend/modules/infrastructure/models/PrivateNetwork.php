<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "private_network".
 *
 * @property int $id
 * @property int|null $player_id
 * @property string|null $name
 * @property int|null $team_accessible
 * @property string|null $created_at
 *
 * @property Player $player
 */
class PrivateNetwork extends \yii\db\ActiveRecord
{


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'private_network';
  }

  public function behaviors()
  {
    return [
      [
        'class' => TimestampBehavior::class,
        'createdAtAttribute' => 'created_at',
        'updatedAtAttribute' => false,
        'value' => new Expression('NOW()'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id', 'name', 'team_accessible', 'created_at'], 'default', 'value' => null],
      [['player_id', 'team_accessible'], 'integer'],
      [['created_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
      [['name'], 'string', 'max' => 255],
      [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
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
      'name' => Yii::t('app', 'Name'),
      'team_accessible' => Yii::t('app', 'Team Accessible'),
      'created_at' => Yii::t('app', 'Created At'),
    ];
  }

  /**
   * Gets query for [[Player]].
   *
   * @return \yii\db\ActiveQuery|PlayerQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  /**
   * {@inheritdoc}
   * @return PrivateNetworkQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new PrivateNetworkQuery(get_called_class());
  }
}
