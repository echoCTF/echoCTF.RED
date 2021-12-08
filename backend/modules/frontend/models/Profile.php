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
 * @property string $htb HTB ProfileID
 * @property string $twitch Twitch.tv handle
 * @property string $youtube Youtube channelID
 * @property int $terms_and_conditions
 * @property int $mail_optin
 * @property int $gdpr
 * @property boolean $approved_avatar
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
            [['terms_and_conditions', 'mail_optin', 'gdpr','approved_avatar'], 'boolean', 'trueValue' => true, 'falseValue' => false],
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

            [['discord', 'twitter', 'github', 'htb', 'avatar', 'bio','youtube','twitch'], 'trim','on'=>'validator'],
            ['country', 'exist', 'targetClass' => \app\modules\settings\models\Country::class, 'targetAttribute' => ['country' => 'id'],'on'=>'validator'],
            [['avatar','youtube','twitch'], 'string', 'max' => 255],
            ['twitter', 'string', 'max' => 15],
            ['twitter', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/','message'=>'Invalid characters in Twitter handle, only <kbd>a-z</kbd>, <kbd>A-Z</kbd>, <kbd>0-9</kbd> and <kbd>_</kbd>','on'=>'validator'],
            ['twitter', '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['admin', 'twitter', 'echoctf'],'on'=>'validator'],
            ['twitch', 'string', 'max' => 25,'on'=>'validator'],
            ['twitch', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/','message'=>'Invalid characters only <kbd>a-z</kbd>, <kbd>A-Z</kbd>, <kbd>0-9</kbd> and <kbd>_</kbd>','on'=>'validator'],
            ['twitch', '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['admin', 'twitter', 'echoctf'],'on'=>'validator'],
            ['github', 'string', 'max' => 39,'on'=>'validator'],
            ['github', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/','message'=>'Invalid characters in github handle, only <kbd>a-z</kbd>, <kbd>A-Z</kbd>, <kbd>0-9</kbd>, <kbd>-</kbd> and <kbd>_</kbd>','on'=>'validator'],
            ['github', '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['help','about'],'on'=>'validator'],
            ['discord', 'string', 'max' => 32,'on'=>'validator'],
            ['discord', function ($attribute, $params) {
                       //returns true / false (preg_replace returns the string with replaced matched regex)
                       if (strpos($this->{$attribute}, '```') !== false
                       || strpos($this->{$attribute}, ':') !== false
                       || strpos($this->{$attribute}, '@') !== false) {
                            $this->addError($attribute, 'Discord usernames cannot contain any of the following [<kbd>@</kbd>,<kbd>:</kbd>,<kbd>```</kbd>]');
                       }

                   },'on'=>'validator'
            ],
            ['discord', function ($attribute, $params) {
                       if (substr_count($this->{$attribute}, '#')>1) {
                            $this->addError($attribute, 'Discord username must contain only one hash (#) character.');
                       }
                   },'on'=>'validator'
            ],
            ['discord', '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['discordtag', 'everyone', 'here'],'on'=>'validator'],
            ['youtube', 'string', 'max' => 25,'on'=>'validator'],
            ['youtube', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/','message'=>'Invalid characters only <kbd>a-z</kbd>, <kbd>A-Z</kbd>, <kbd>0-9</kbd>, <kbd>-</kbd> and <kbd>_</kbd>','on'=>'validator'],

            ['htb', 'string', 'max' => 8,'on'=>'validator'],
            ['htb', 'match', 'pattern' => '/^[0-9]+$/','message'=>'Only numberic HTB id is allowed','on'=>'validator'],

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
