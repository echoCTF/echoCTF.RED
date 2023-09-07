<?php

namespace app\modules\frontend\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * This is the model class for table "profile".
 *
 * @property string $id
 * @property int $player_id
 * @property string $visibility
 * @property string $bio
 * @property string $country Player Country
 * @property string $avatar Profile avatar
 * @property string $discord Profile handle
 * @property string $twitter Twitter handle
 * @property string $github Github handle
 * @property string $echoctf echoCTF ProfileID
 * @property string $htb HTB ProfileID
 * @property string $twitch Twitch.tv handle
 * @property string $youtube Youtube channelID
 * @property int $terms_and_conditions
 * @property int $mail_optin
 * @property int $gdpr
 * @property boolean $approved_avatar
 * @property boolean $pending_progress
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Owner[] $owner
 * @property boolean $isMine
 */

class Profile extends \yii\db\ActiveRecord
{
  public $visibilities=[
      'private'=>'Private',
      'public'=>'Public',
      'ingame'=>'In Game',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'required'],
//            [['terms_and_conditions','mail_optin','gdpr'],'in', 'range' => ['public', 'private', 'ingame']],
            [['terms_and_conditions', 'mail_optin', 'gdpr','approved_avatar','pending_progress'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['visibility'], 'in', 'range' => ['public', 'private', 'ingame']],
            [['visibility'], 'default', 'value' =>  'private'],
            [['id'], 'default', 'value' =>  new Expression('round(rand()*10000000)')],
            [['id', 'player_id'], 'integer'],
            [['bio'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['avatar', 'twitter', 'github','htb','twitch','youtube'], 'string', 'max' => 255],
            [['country'], 'string', 'max'=>3],
            [['player_id'], 'unique'],
            [['id'], 'unique'],

            ['country', 'exist', 'targetClass' => \app\modules\settings\models\Country::class, 'targetAttribute' => ['country' => 'id'],'on'=>'validator'],
            [['avatar'], 'string', 'max' => 255],

            [['discord', 'twitter', 'github', 'echoctf', 'htb', 'avatar', 'bio','youtube','twitch'], 'trim','on'=>'validator'],
            ['twitter', '\app\components\validators\social\TwitterValidator','on'=>'validator'],
            ['twitch', '\app\components\validators\social\TwitchValidator','on'=>'validator'],
            ['github', '\app\components\validators\social\GithubValidator','on'=>'validator'],
            ['youtube', '\app\components\validators\social\YoutubeValidator','on'=>'validator'],
            ['discord', '\app\components\validators\social\DiscordValidator','on'=>'validator'],

            ['htb', 'string', 'max' => 8,'on'=>'validator'],
            ['htb', 'match', 'pattern' => '/^[0-9]+$/','message'=>'Only numberic HTB id is allowed','on'=>'validator'],
            [['htb','echoctf'], 'integer', 'min'=>1, 'max' => 99999999,'on'=>'validator'],
            [['htb','echoctf'], 'match', 'pattern' => '/^[0-9]+$/','message'=>'Only numberic values are allowed for {attribute}','on'=>'validator'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'bio' => 'Bio',
            'avatar' => 'Avatar',
            'visibility' => 'Visibility',
            'twitter' => 'Twitter',
            'github' => 'Github',
            'echoctf'=>'echoCTF.RED',
            'countr'=>'Country',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'owner.username' => 'Username',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }
    public function getLink()
    {
      return Html::a(Html::encode($this->owner->username), ['frontend/profile/view', 'id'=>$this->id]);
    }

    public function getAvtr()
    {
      if($this->approved_avatar || $this->isMine)
        return  $this->avatar;
      return '../default_avatar.png';
    }

}
