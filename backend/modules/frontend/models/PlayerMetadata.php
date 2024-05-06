<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_metadata".
 *
 * @property int $player_id
 * @property string|null $identificationFile
 * @property string|null $affiliation
 *
 * @property Player $player
 */
class PlayerMetadata extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_metadata';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['identificationFile', 'affiliation'], 'string', 'max' => 64],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => Yii::t('app', 'Player ID'),
            'identificationFile' => Yii::t('app', 'Identification File'),
            'affiliation' => Yii::t('app', 'Affiliation'),
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
     * @return PlayerMetadataQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerMetadataQuery(get_called_class());
    }
}
