<?php

namespace app\modules\team\models;

use Yii;
use app\models\Player;
use yii\web\UploadedFile;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $academic
 * @property string $logo
 * @property int $owner_id
 * @property string $token
 * @property boolean $inviteonly
 * @property boolean $locked
 * @property string $recruitment
 *
 * @property Player $owner
 * @property TeamPlayer[] $teamPlayers
 * @property Player[] $players
 */
class Team extends \yii\db\ActiveRecord
{
  public $uploadedAvatar;
  const SCENARIO_CREATE = 'create';
  const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }
    public function behaviors()
    {
      return [
        'typecast' => [
          'class' => AttributeTypecastBehavior::class,
          'attributeTypes' => [
            'id' => AttributeTypecastBehavior::TYPE_INTEGER,
            'inviteonly' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            'locked' => AttributeTypecastBehavior::TYPE_BOOLEAN,
            'academic' => AttributeTypecastBehavior::TYPE_INTEGER,
          ],
          'typecastAfterValidate' => true,
          'typecastBeforeSave' => true,
          'typecastAfterFind' => true,
        ],
      ];
    }

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'createScore']);
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['name', 'description','token','academic'],
            self::SCENARIO_UPDATE => ['name', 'description', 'uploadedAvatar','inviteonly','recruitment','locked'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'owner_id'], 'required'],
            [['description', 'logo'], 'string'],
            [['academic', 'owner_id'], 'integer'],
            [['inviteonly','locked'], 'boolean'],
            [['inviteonly'], 'default','value'=>true],
            [['locked'], 'default','value'=>false],
            [['name'], 'trim'],
            [['name'], 'string', 'length' => [3, 32]],
            [['description','recruitment'], 'string', 'max' =>250],
            [['token'], 'string', 'max' => 30],
            [['token'], 'default', 'value' => Yii::$app->security->generateRandomString(10)],
            [['name'], 'unique',  'when' => function($model, $attribute) {
                return $model->{$attribute} !== $model->getOldAttribute($attribute);
            }],
            [['token'], 'unique'],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['owner_id' => 'id']],
            [['uploadedAvatar'], 'file',  'extensions' => 'png', 'mimeTypes' => 'image/png','maxSize' =>  512000, 'tooBig' => 'File larger than expected, limit is 500KB'],
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
            'academic' => 'Academic',
            'logo' => 'Logo',
            'owner_id' => 'Owner ID',
            'token' => 'Token',
            'inviteonly'=>'Invite Only',
            'locked'=>'Locked',
            'recruitment'=>'Recruitment Text'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Player::class, ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScore()
    {
        return $this->hasOne(TeamScore::class, ['team_id' => 'id']);
    }

    public function getRank()
    {
        return $this->hasOne(TeamRank::class, ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamPlayers()
    {
        return $this->hasMany(TeamPlayer::class, ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('team_player', ['team_id' => 'id']);
    }

    public function getValidLogo()
    {
      if($this->logo===null || trim($this->logo)==='')
        return '../../team_player.png';
      return $this->logo;
    }


    public static function find()
    {
        return new TeamQuery(get_called_class());
    }

    public function createScore($event)
    {
      $ts=new TeamScore();
      $ts->team_id=$this->id;
      $ts->save();
    }
    public function saveLogo()
    {
      if(!$this->uploadedAvatar)
      {
        return true;
      }

      if(!$this->HandleUpload($this->uploadedAvatar))
      {
        return false;
      }

      $fname=Yii::getAlias(sprintf('@app/web/images/avatars/team/%s.png',$this->id));
      $this->updateAttributes(['logo' => $this->id.'.png']);
      return $this->uploadedAvatar->saveAs($fname);
    }

    protected function HandleUpload($uploadedAvatar)
    {
      if(!$uploadedAvatar) return false;
      $src = imagecreatefrompng($uploadedAvatar->tempName);
      if($src!==false)
      {

        $old_x = imageSX($src);
        $old_y = imageSY($src);
        list($thumb_w,$thumb_h) = $this->ScaledXY($old_x,$old_y);

        $avatar=imagescale($src,$thumb_w,$thumb_h);

        $image = imagecreatetruecolor(300,300);
        if(!$image) return false;

        imagealphablending($image, false);
        $col=imagecolorallocatealpha($image,255,255,255,127);
        imagefilledrectangle($image,0,0,300, 300,$col);
        imagealphablending($image,true);

        list($dst_x,$dst_y) = $this->DestinationXY($thumb_w,$thumb_h);
        imagecopyresampled($image, $avatar, $dst_x, $dst_y, /*src_x*/ 0, /*src_y*/ 0, /*dst_w*/ $thumb_w, /*dst_h*/ $thumb_h, /*src_w*/ $thumb_w, /*src_y*/ $thumb_h);
        imagesavealpha($image, true);
        imagepng($image,$uploadedAvatar->tempName);
        imagedestroy($image);
        imagedestroy($src);
        imagedestroy($avatar);
        return true;
      }
      return false;
    }

    protected function DestinationXY($x,$y)
    {
      $pos_x = $pos_y = 0;

      if($x<300)
      {
        $pos_x = floor((300-$x)/2);
      }
      if($y<300)
      {
        $pos_y = floor((300-$y)/2);
      }
      return [ $pos_x, $pos_y ];
    }

    protected function ScaledXY($old_x,$old_y)
    {
      $thumb_h = $thumb_w = 300;
      if($old_x > $old_y)
      {
        $thumb_w    =   300;
        $thumb_h    =   $old_y*(300/$old_x);
      }

      if($old_x < $old_y)
      {
        $thumb_w    =   $old_x*(300/$old_y);
        $thumb_h    =   300;
      }

      if($old_x == $old_y)
      {
        $thumb_w    =   300;
        $thumb_h    =   300;
      }
      return [$thumb_w, $thumb_h];
    }

    public function getAcademicShort()
    {
      switch($this->academic)
      {
        case 0:
          return 'gov';
        case 1:
          return 'edu';
        default:
          return 'pro';
      }
    }

    public function getAcademicWord()
    {
      switch($this->academic)
      {
        case 0:
          return 'government';
        case 1:
          return 'education';
        default:
          return 'professional';
      }
    }
}
