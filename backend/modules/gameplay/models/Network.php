<?php

namespace app\modules\gameplay\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "network".
 *
 * @property int $id
 * @property string $name
 * @property string $codename
 * @property string $icon
 * @property string $description
 * @property boolean $public
 * @property boolean $guest
 * @property boolean $active
 * @property integer $announce
 * @property integer $weight
 * @property string $ts
 *
 * @property NetworkPlayer[] $networkPlayers
 * @property Player[] $players
 * @property NetworkTarget[] $networkTargets
 * @property Target[] $targets
 */
class Network extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','codename'], 'required'],
            [['description','codename','icon'], 'string'],
            [['public','active','announce','guest'], 'boolean'],
            [['ts'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['name'], 'unique'],
            [['weight'],'integer'],
            [['weight'],'default','value'=>0]
        ];
    }

    public function behaviors()
    {
        return [
          'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'active' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'announce' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'public' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'guest' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                ],
                'typecastAfterValidate' => false,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
            ],
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'ts',
                'updatedAtAttribute' => 'ts',
                'value' => new \yii\db\Expression('NOW()'),
            ],
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
            'description' => 'Description',
            'codename' => 'Codename',
            'icon' => 'Icon',
            'public' => 'Public',
            'guest' => 'Guest',
            'active' => 'Active',
            'announce' => 'Announce',
            'weight' => 'Weight',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkPlayers()
    {
        return $this->hasMany(NetworkPlayer::class, ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('network_player', ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkTargets()
    {
        return $this->hasMany(NetworkTarget::class, ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargets()
    {
        return $this->hasMany(Target::class, ['id' => 'target_id'])->viaTable('network_target', ['network_id' => 'id']);
    }
}
