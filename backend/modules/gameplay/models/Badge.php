<?php

namespace app\modules\gameplay\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\activity\models\PlayerBadge;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "badge".
 *
 * @property int $id
 * @property string $name
 * @property string $pubname Name for the public eyes
 * @property string $description
 * @property string $pubdescription Description for the public eyes
 * @property string $points
 *
 * @property BadgeFinding[] $badgeFindings
 * @property Finding[] $findings
 * @property BadgeTreasure[] $badgeTreasures
 * @property Treasure[] $treasures
 * @property Hint[] $hints
 * @property PlayerBadge[] $playerBadges
 * @property Player[] $players
 */
class Badge extends \yii\db\ActiveRecord
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
                'points' => AttributeTypecastBehavior::TYPE_INTEGER,
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadgeFindings()
    {
        return $this->hasMany(BadgeFinding::class, ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFindings()
    {
        return $this->hasMany(Finding::class, ['id' => 'finding_id'])->viaTable('badge_finding', ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadgeTreasures()
    {
        return $this->hasMany(BadgeTreasure::class, ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasures()
    {
        return $this->hasMany(Treasure::class, ['id' => 'treasure_id'])->viaTable('badge_treasure', ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHints()
    {
        return $this->hasMany(Hint::class, ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerBadges()
    {
        return $this->hasMany(PlayerBadge::class, ['badge_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('user_badge', ['badge_id' => 'id']);
    }
}
