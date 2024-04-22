<?php

namespace app\modules\help\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "rule".
 *
 * @property int $id
 * @property string $title
 * @property string $player_type
 * @property string $message
 * @property int $weight
 * @property string $ts
 */
class Rule extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'rule';
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
      [['weight'], 'default', 'value' => 0],
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

  public function save($runValidation = true, $attributeNames = null)
  {
    throw new \LogicException("Saving is disabled for this model.");
  }
  /**
   * {@inheritdoc}
   * @return RuleQuery the active query used by this AR class.
   */

  public static function find()
  {
    return new RuleQuery(get_called_class());
  }
}
