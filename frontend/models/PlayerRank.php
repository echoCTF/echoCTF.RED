<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_rank".
 *
 * @property int $id
 * @property int $player_id
 */
class PlayerRank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_rank';
    }
    public function behaviors()
    {
      return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::className(),
            'attributeTypes' => [
                'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
            ],
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => false,
            'typecastAfterFind' => true,
        ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id'], 'required'],
            [['id', 'player_id'], 'integer'],
            [['player_id'], 'unique'],
            [['id'], 'unique'],
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
        ];
    }
  function getOrdinalPlace() {
    if (!in_array(($this->id % 100),array(11,12,13))){
      switch ($this->id % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $this->id.'st';
        case 2:  return $this->id.'nd';
        case 3:  return $this->id.'rd';
      }
    }
    return $this->id.'th';
  }
}
