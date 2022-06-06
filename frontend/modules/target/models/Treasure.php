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
 * @property int $weight
 * @property string|null $effects
 * @property int $target_id A target system that this treasure is hidden on. This is not required but its good to have
 * @property string|null $code
 * @property string $ts
 *
 * @property string|null $category
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
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'points' => AttributeTypecastBehavior::TYPE_FLOAT,
                    'weight' => AttributeTypecastBehavior::TYPE_INTEGER,
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
            [['appears', 'target_id','weight'], 'integer'],
            [['ts'], 'safe'],
            [['name', 'pubname'], 'string', 'max' => 255],
            [['csum', 'code'], 'string', 'max' => 128],
            [['name', 'target_id', 'code', 'csum'], 'unique', 'targetAttribute' => ['name', 'target_id', 'code', 'csum']],
            [['code'], 'unique'],
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
//    public function getBadgeTreasures()
//    {
//        return $this->hasMany(BadgeTreasure::class, ['treasure_id' => 'id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadges()
    {
        return $this->hasMany(\app\modules\game\models\Badge::class, ['id' => 'badge_id'])->viaTable('badge_treasure', ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHints()
    {
        return $this->hasMany(\app\models\Hint::class, ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerTreasures()
    {
        return $this->hasMany(\app\models\PlayerTreasure::class, ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(\app\models\Player::class, ['id' => 'player_id'])->viaTable('player_treasure', ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationRedacted()
    {
        return str_replace($this->code,"*REDACTED*",$this->location);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getTreasureActions()
//    {
//        return $this->hasMany(TreasureAction::class, ['treasure_id' => 'id']);
//    }

    public static function find()
    {
        return new TreasureQuery(get_called_class());
    }

    public function save($runValidation=true, $attributeNames=null)
    {
        throw new \LogicException("Saving is disabled for this model.");
    }

}
