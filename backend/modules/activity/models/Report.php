<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "report".
 *
 * @property int $id
 * @property string $title
 * @property int $player_id
 * @property string $body
 * @property string $status
 * @property int $points
 * @property string $modcomment The comment from the moderator about the report
 * @property string $pubtitle
 * @property string $pubbody
 *
 * @property Player $player
 */
class Report extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'points'], 'required'],
            [['player_id', 'points'], 'integer'],
            [['body', 'status', 'modcomment', 'pubbody'], 'string'],
            [['title', 'pubtitle'], 'string', 'max' => 255],
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
            'title' => 'Title',
            'player_id' => 'Player ID',
            'body' => 'Body',
            'status' => 'Status',
            'points' => 'Points',
            'modcomment' => 'Modcomment',
            'pubtitle' => 'Pubtitle',
            'pubbody' => 'Pubbody',
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
