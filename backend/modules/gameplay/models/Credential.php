<?php

namespace app\modules\gameplay\models;

use Yii;

/**
 * This is the model class for table "credential".
 *
 * @property int $id
 * @property string $service
 * @property string $title
 * @property string $pubtitle
 * @property string $username Username
 * @property string $password Password
 * @property int $target_id A target system that this credential is for.
 * @property string $points
 * @property string $player_type
 * @property int $stock
 *
 * @property Target $target
 */
class Credential extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'credential';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service', 'title', 'pubtitle', 'username', 'password', 'target_id'], 'required'],
            [['target_id', 'stock'], 'integer'],
            [['points'], 'number'],
            [['player_type'], 'string'],
            [['service'], 'string', 'max' => 50],
            [['title', 'pubtitle', 'username', 'password'], 'string', 'max' => 255],
            [['service', 'target_id', 'username', 'password'], 'unique', 'targetAttribute' => ['service', 'target_id', 'username', 'password']],
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
            'service' => 'Service',
            'title' => 'Title',
            'pubtitle' => 'Pubtitle',
            'username' => 'Username',
            'password' => 'Password',
            'target_id' => 'Target ID',
            'points' => 'Points',
            'player_type' => 'Player Type',
            'stock' => 'Stock',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }
}
