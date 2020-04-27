<?php

namespace app\modules\gameplay\models;

use Yii;
use app\modules\activity\models\PlayerTreasure;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "treasure".
 *
 * @property int $id
 * @property string $name
 * @property string $pubname Name for the public eyes
 * @property string $description
 * @property string $pubdescription Description for the public eyes
 * @property string $points
 * @property string $player_type
 * @property string $csum If there is a file attached to this treasure
 * @property string $category
 * @property int $appears
 * @property string $effects
 * @property int $target_id A target system that this treasure is hidden on. This is not required but its good to have
 * @property string $code
 *
 * @property BadgeTreasure[] $badgeTreasures
 * @property Badge[] $badges
 * @property Hint[] $hints
 * @property Target $target
 * @property PlayerTreasure[] $playerTreasures
 * @property Player[] $players
 */
class Treasure extends \yii\db\ActiveRecord
{
  public $hint;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'treasure';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'points', 'target', 'code'], 'required'],
            [['description', 'category','pubdescription', 'player_type', 'effects'], 'string'],
            [['points'], 'number'],
            [['appears', 'target_id'], 'integer'],
            [['name', 'pubname', 'hint'], 'string', 'max' => 255],
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
            'category' => 'category',
            'code' => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadgeTreasures()
    {
        return $this->hasMany(BadgeTreasure::class, ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadges()
    {
        return $this->hasMany(Badge::class, ['id' => 'badge_id'])->viaTable('badge_treasure', ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHints()
    {
        return $this->hasMany(Hint::class, ['treasure_id' => 'id']);
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
    public function getPlayerTreasures()
    {
        return $this->hasMany(PlayerTreasure::class, ['treasure_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('user_treasure', ['treasure_id' => 'id']);
    }
    public function afterSave($insert,$changedAttributes)
    {
      parent::afterSave($insert, $changedAttributes);
      if(!empty($this->hint))
      {
        $h=new Hint();
        $h->title=$this->hint;
        $h->finding_id=$this->id;
        $h->player_type=$this->player_type;
        if($h->save())
          return true;
        else
          return false;
      }
      return true;
    }

}
