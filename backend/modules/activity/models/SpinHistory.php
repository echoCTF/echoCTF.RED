<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;

/**
 * This is the model class for table "spin_history".
 *
 * @property int $id
 * @property int $target_id
 * @property int $player_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Player $player
 * @property Target $target
 */
class SpinHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'spin_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['target_id', 'player_id'], 'required'],
            [['target_id', 'player_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
            'id' => 'ID',
            'target_id' => 'Target ID',
            'player_id' => 'Player ID',
            'created_at' => 'Created At',
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
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }
}
