<?php

namespace app\modules\gameplay\models;

use Yii;

/**
 * This is the model class for table "badge_finding".
 *
 * @property int $badge_id
 * @property int $finding_id
 *
 * @property Badge $badge
 * @property Finding $finding
 */
class BadgeFinding extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'badge_finding';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['badge_id', 'finding_id'], 'required'],
            [['badge_id', 'finding_id'], 'integer'],
            [['badge_id', 'finding_id'], 'unique', 'targetAttribute' => ['badge_id', 'finding_id']],
            [['badge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Badge::class, 'targetAttribute' => ['badge_id' => 'id']],
            [['finding_id'], 'exist', 'skipOnError' => true, 'targetClass' => Finding::class, 'targetAttribute' => ['finding_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'badge_id' => 'Badge ID',
            'finding_id' => 'Finding ID',
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
    public function getFinding()
    {
        return $this->hasOne(Finding::class, ['id' => 'finding_id']);
    }
}
