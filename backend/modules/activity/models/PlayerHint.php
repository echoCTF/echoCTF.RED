<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Hint;

/**
 * This is the model class for table "player_hint".
 *
 * @property int $player_id
 * @property int $hint_id
 * @property int $status
 * @property string $ts
 *
 * @property Player $player
 * @property Hint $hint
 */
class PlayerHint extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_hint';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'hint_id'], 'required'],
            [['player_id', 'hint_id', 'status'], 'integer'],
            [['ts'], 'safe'],
            [['player_id', 'hint_id'], 'unique', 'targetAttribute' => ['player_id', 'hint_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['hint_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hint::class, 'targetAttribute' => ['hint_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'hint_id' => 'Hint ID',
            'status' => 'Status',
            'ts' => 'Ts',
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
    public function getHint()
    {
        return $this->hasOne(Hint::class, ['id' => 'hint_id']);
    }
}
