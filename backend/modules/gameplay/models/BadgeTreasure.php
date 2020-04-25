<?php

namespace app\modules\gameplay\models;

use Yii;

/**
 * This is the model class for table "badge_treasure".
 *
 * @property int $badge_id
 * @property int $treasure_id
 *
 * @property Badge $badge
 * @property Treasure $treasure
 */
class BadgeTreasure extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'badge_treasure';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['badge_id', 'treasure_id'], 'required'],
            [['badge_id', 'treasure_id'], 'integer'],
            [['badge_id', 'treasure_id'], 'unique', 'targetAttribute' => ['badge_id', 'treasure_id']],
            [['badge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Badge::class, 'targetAttribute' => ['badge_id' => 'id']],
            [['treasure_id'], 'exist', 'skipOnError' => true, 'targetClass' => Treasure::class, 'targetAttribute' => ['treasure_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'badge_id' => 'Badge ID',
            'treasure_id' => 'Treasure ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadge()
    {
        return $this->hasOne(Badge::class, ['id' => 'badge_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasure()
    {
        return $this->hasOne(Treasure::class, ['id' => 'treasure_id']);
    }
}
