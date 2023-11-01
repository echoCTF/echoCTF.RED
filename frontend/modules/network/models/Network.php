<?php

namespace app\modules\network\models;

use Yii;
use app\models\Player;

/**
 * This is the model class for table "network".
 *
 * @property int $id
 * @property string $name
 * @property string $icon
 * @property string $description
 * @property int $active
 * @property string $ts
 *
 * @property int targetsCount
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
            [['public','active','guest'], 'boolean'],
            [['ts'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['name'], 'unique'],
        ];
    }
    public function behaviors()
    {
      return [
        'typecast' => [
          'class' => \yii\behaviors\AttributeTypecastBehavior::class,
          'attributeTypes' => [
            'public' => \yii\behaviors\AttributeTypecastBehavior::TYPE_BOOLEAN,
            'active' => \yii\behaviors\AttributeTypecastBehavior::TYPE_BOOLEAN,
            'guest' => \yii\behaviors\AttributeTypecastBehavior::TYPE_BOOLEAN,
          ],
          'typecastAfterValidate' => false,
          'typecastBeforeSave' => false,
          'typecastAfterFind' => true,
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
     * @return int
     */
    public function getTargetsCount():int
    {
        return (int)$this->hasMany(NetworkTarget::class, ['network_id' => 'id'])->count();
    }

    public function getInProducts():int
    {
        return intval(\Yii::$app->db->createCommand("SELECT count(*) FROM product_network WHERE network_id=:network_id")->bindValue(':network_id',$this->id)->queryScalar());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargets()
    {
        return $this->hasMany(\app\modules\target\models\Target::class, ['id' => 'target_id'])->viaTable('network_target', ['network_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return NetworkQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NetworkQuery(get_called_class());
    }

}
