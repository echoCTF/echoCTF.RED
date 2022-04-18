<?php

namespace app\modules\target\models;

use Yii;

use app\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "target_ondemand".
 *
 * @property int $target_id
 * @property int|null $player_id
 * @property int $state
 * @property string|null $heartbeat
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 * @property Target $target
 */
class Ondemand extends \yii\db\ActiveRecord
{
    public $expired=0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'target_ondemand';
    }

    public function behaviors()
    {
      return [
        'typecast' => [
                   'class' => AttributeTypecastBehavior::class,
                   // 'attributeTypes' will be composed automatically according to `rules()`
               ],
          [
              'class' => TimestampBehavior::class,
              'createdAtAttribute' => 'created_at',
              'updatedAtAttribute' => 'updated_at',
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
            [['player_id', 'state'], 'integer'],
            [['player_id'], 'default','value'=>null],
            [['heartbeat', 'created_at', 'updated_at'], 'safe'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'target_id' => Yii::t('app', 'Target ID'),
            'player_id' => Yii::t('app', 'Player ID'),
            'state' => Yii::t('app', 'State'),
            'heartbeat' => Yii::t('app', 'Heartbeat'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|TargetQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * {@inheritdoc}
     * @return TargetOndemandQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OndemandQuery(get_called_class());
    }
}
