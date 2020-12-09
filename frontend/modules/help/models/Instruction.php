<?php

namespace app\modules\help\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "instruction".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $player_type
 * @property string|null $message
 * @property int $weight
 * @property string $ts
 */
class Instruction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'instruction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_type', 'message'], 'string'],
            [['weight'], 'integer'],
            [['ts'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'unique'],
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
            'player_type' => 'Player Type',
            'message' => 'Message',
            'weight' => 'Weight',
            'ts' => 'Ts',
        ];
    }

    public function save($runValidation=true, $attributeNames=null)
    {
        throw new \LogicException("Saving is disabled for this model.");
    }

}
