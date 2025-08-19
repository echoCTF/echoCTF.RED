<?php

namespace app\modules\moderation\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "abuser".
 *
 * @property int $id
 * @property int $player_id
 * @property string|null $title
 * @property string|null $body
 * @property string|null $reason
 * @property string $model
 * @property int $model_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 */
class Abuser extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'abuser';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'body', 'reason', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['player_id', 'model', 'model_id'], 'required'],
            [['player_id', 'model_id'], 'integer'],
            [['body'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'reason', 'model'], 'string', 'max' => 255],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'title' => 'Title',
            'body' => 'Body',
            'reason' => 'Reason',
            'model' => 'Model',
            'model_id' => 'Model ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * {@inheritdoc}
     * @return AbuserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AbuserQuery(get_called_class());
    }

}
