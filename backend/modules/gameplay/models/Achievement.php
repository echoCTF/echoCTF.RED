<?php

namespace app\modules\gameplay\models;

use Yii;

/**
 * This is the model class for table "achievement".
 *
 * @property int $id
 * @property string $name
 * @property string $pubname Name for the public eyes
 * @property string $description
 * @property string $pubdescription Description for the public eyes
 * @property string $points
 * @property string $player_type
 * @property int $appears
 * @property string $effects
 * @property string $code
 */
class Achievement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'achievement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'pubdescription', 'player_type', 'effects'], 'string'],
            [['points'], 'number'],
            [['appears'], 'integer'],
            [['name', 'pubname'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 128],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'pubname' => 'Pubname',
            'description' => 'Description',
            'pubdescription' => 'Pubdescription',
            'points' => 'Points',
            'player_type' => 'Player Type',
            'appears' => 'Appears',
            'effects' => 'Effects',
            'code' => 'Code',
        ];
    }
}
