<?php

namespace app\modules\game\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "badge".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $pubname Name for the public eyes
 * @property string|null $description
 * @property string|null $pubdescription Description for the public eyes
 * @property float|null $points
 * @property string $ts
 *
 * @property BadgeFinding[] $badgeFindings
 * @property Finding[] $findings
 * @property BadgeTreasure[] $badgeTreasures
 * @property Treasure[] $treasures
 * @property Hint[] $hints
 * @property PlayerBadge[] $playerBadges
 * @property Player[] $players
 */
class Badge extends \app\models\ActiveRecordReadOnly
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'badge';
    }
    public function behaviors()
    {
      return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'points' => AttributeTypecastBehavior::TYPE_FLOAT,
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
            [['description', 'pubdescription'], 'string'],
            [['points'], 'number'],
            [['ts'], 'safe'],
            [['name', 'pubname'], 'string', 'max' => 255],
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
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFindings()
    {
        return $this->hasMany(\app\modules\target\models\Finding::class, ['id' => 'finding_id'])->viaTable('badge_finding', ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasures()
    {
        return $this->hasMany(\app\modules\target\models\Treasure::class, ['id' => 'treasure_id'])->viaTable('badge_treasure', ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHints()
    {
        return $this->hasMany(\app\models\Hint::class, ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(\app\models\Player::class, ['id' => 'player_id'])->viaTable('player_badge', ['badge_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return BadgeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BadgeQuery(get_called_class());
    }
}
