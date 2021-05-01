<?php

namespace app\modules\content\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "objective".
 *
 * @property int $id
 * @property string $title
 * @property string $player_type
 * @property string $message
 * @property int $weight
 * @property string $ts
 */
class Objective extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'objective';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return [
          [
              'class' => TimestampBehavior::class,
              'createdAtAttribute' => 'ts',
              'updatedAtAttribute' => 'ts',
              'value' => new Expression('NOW()'),
          ],
      ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_type', 'message'], 'string'],
            [['weight'], 'integer'],
            [['weight'], 'default', 'value'=> 0],
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
}
