<?php

namespace app\modules\target\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "finding".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $pubname Name for the public eyes
 * @property string|null $description
 * @property string|null $pubdescription Description for the public eyes
 * @property float|null $points
 * @property int $stock
 * @property string|null $protocol
 * @property int|null $target_id
 * @property int|null $port
 * @property string $ts
 *
 * @property BadgeFinding[] $badgeFindings
 * @property Badge[] $badges
 * @property Target $target
 * @property Hint[] $hints
 * @property PlayerFinding[] $playerFindings
 * @property Player[] $players
 */
class Finding extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'finding';
    }
    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'stock' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'target_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'port' => AttributeTypecastBehavior::TYPE_INTEGER,
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
            [['description', 'pubdescription'], 'string'],
            [['points'], 'number'],
            [['stock', 'target_id', 'port'], 'integer'],
            [['ts'], 'safe'],
            [['name', 'pubname'], 'string', 'max' => 255],
            [['protocol'], 'string', 'max' => 30],
            [['protocol', 'target_id', 'port'], 'unique', 'targetAttribute' => ['protocol', 'target_id', 'port']],
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
            'stock' => 'Stock',
            'protocol' => 'Protocol',
            'target_id' => 'Target ID',
            'port' => 'Port',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getBadgeFindings()
//    {
//        return $this->hasMany(BadgeFinding::class, ['finding_id' => 'id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadges()
    {
        return $this->hasMany(\app\modules\game\models\Badge::class, ['id' => 'badge_id'])->viaTable('badge_finding', ['finding_id' => 'id']);
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
    public function getHints()
    {
        return $this->hasMany(\app\models\Hint::class, ['finding_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getPlayerFindings()
//    {
//        return $this->hasMany(PlayerFinding::class, ['finding_id' => 'id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(\app\models\Player::class, ['id' => 'player_id'])->viaTable('player_finding', ['finding_id' => 'id']);
    }

    public function save($runValidation=true, $attributeNames=null)
    {
        throw new \LogicException("Saving is disabled for this model.");
    }

}
