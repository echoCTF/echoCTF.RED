<?php

namespace app\modules\gameplay\models;

use Yii;
use app\modules\activity\models\PlayerFinding;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "finding".
 *
 * @property int $id
 * @property string $name
 * @property string $pubname Name for the public eyes
 * @property string $description
 * @property string $pubdescription Description for the public eyes
 * @property string $points
 * @property int $stock
 * @property string $protocol
 * @property int $target_id
 * @property int $port
 *
 * @property BadgeFinding[] $badgeFindings
 * @property Badge[] $badges
 * @property Target $target
 * @property Hint[] $hints
 * @property PlayerFinding[] $PlayerFindings
 * @property Player[] $players
 */
class Finding extends \yii\db\ActiveRecord
{
  public $hint;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'finding';
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
            [['name', 'pubname', 'hint'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadgeFindings()
    {
        return $this->hasMany(BadgeFinding::class, ['finding_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadges()
    {
        return $this->hasMany(Badge::class, ['id' => 'badge_id'])->viaTable('badge_finding', ['finding_id' => 'id']);
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
        return $this->hasMany(Hint::class, ['finding_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayerFindings()
    {
        return $this->hasMany(PlayerFinding::class, ['finding_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('user_finding', ['finding_id' => 'id']);
    }

    public function getMatchRule($source=null,$destination=null,$pflog_min=1,$pflog_max=1)
    {

      $pflogif=random_int($pflog_min,$pflog_max);
      if($destination===null)
      {
        $destination=$this->target->ipoctet;
      }
      if($this->protocol === 'icmp')
        $trule=sprintf('match log (to pflog%d) inet proto %s%%s to %s tagged %s icmp-type echoreq label "$dstaddr:$dstport"', $pflogif,$this->protocol, $destination,trim(\app\modules\settings\models\Sysconfig::findOne('offense_registered_tag')->val));
      else
        $trule=sprintf('match log (to pflog%d) inet proto %s%%s to %s port %d tagged %s label "$dstaddr:$dstport"', $pflogif,$this->protocol, $destination,$this->port, trim(\app\modules\settings\models\Sysconfig::findOne('offense_registered_tag')->val));

      if($source!==null)
        $rule=sprintf($trule,' from '.$source);
      else if($this->target->network!==NULL)
        $rule=sprintf($trule,' from <'.$this->target->network->codename.'_clients>');
      else
        $rule=sprintf($trule,'');
      return $rule;
    }

    public function afterSave($insert, $changedAttributes)
    {
      parent::afterSave($insert, $changedAttributes);
      if(!empty($this->hint))
      {
        $h=new Hint();
        $h->title=$this->hint;
        $h->finding_id=$this->id;
        $h->player_type='offense';
        if($h->save())
          return true;
        else
          return false;
      }
      return true;
    }
    /**
     * {@inheritdoc}
     * @return FindingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FindingQuery(get_called_class());
    }

}
