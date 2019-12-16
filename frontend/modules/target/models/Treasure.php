<?php

namespace app\modules\target\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "treasure".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $pubname Name for the public eyes
 * @property string|null $description
 * @property string|null $pubdescription Description for the public eyes
 * @property float|null $points
 * @property string $player_type
 * @property string $csum If there is a file attached to this treasure
 * @property int|null $appears
 * @property string|null $effects
 * @property int $target_id A target system that this treasure is hidden on. This is not required but its good to have
 * @property string|null $code
 * @property string $ts
 *
 * @property BadgeTreasure[] $badgeTreasures
 * @property Badge[] $badges
 * @property Hint[] $hints
 * @property PlayerTreasure[] $playerTreasures
 * @property Player[] $players
 * @property Target $target
 * @property TreasureAction[] $treasureActions
 */
class Treasure extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'treasure';
    }
    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::className(),
                'attributeTypes' => [
                    'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'points' => AttributeTypecastBehavior::TYPE_FLOAT,
                    'appears' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'target_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                ],
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => true,
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
            [['description', 'pubdescription', 'player_type', 'effects'], 'string'],
            [['points'], 'number'],
            [['appears', 'target_id'], 'integer'],
            [['ts'], 'safe'],
            [['name', 'pubname'], 'string', 'max' => 255],
            [['csum', 'code'], 'string', 'max' => 128],
            [['name', 'target_id', 'code', 'csum'], 'unique', 'targetAttribute' => ['name', 'target_id', 'code', 'csum']],
            [['code'], 'unique'],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::className(), 'targetAttribute' => ['target_id' => 'id']],
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
            'csum' => 'Csum',
            'appears' => 'Appears',
            'effects' => 'Effects',
            'target_id' => 'Target ID',
            'code' => 'Code',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadgeTreasures()
    {
        return $this->hasMany(BadgeTreasure::className(), ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadges()
    {
        return $this->hasMany(Badge::className(), ['id' => 'badge_id'])->viaTable('badge_treasure', ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHints()
    {
        return $this->hasMany(Hint::className(), ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerTreasures()
    {
        return $this->hasMany(PlayerTreasure::className(), ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::className(), ['id' => 'player_id'])->viaTable('player_treasure', ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::className(), ['id' => 'target_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasureActions()
    {
        return $this->hasMany(TreasureAction::className(), ['treasure_id' => 'id']);
    }

    public static function find()
    {
        return new TreasureQuery(get_called_class());
    }
}
