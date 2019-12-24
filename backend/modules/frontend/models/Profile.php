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
 * @property string $discord Profile avatar
 * @property string $twitter Twitter handle
 * @property string $github Github handle
 * @property string $htb HTB avatar
 * @property int $terms_and_conditions
 * @property int $mail_optin
 * @property int $gdpr
 * @property string $created_at
 * @property string $updated_at
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
            [['terms_and_conditions','mail_optin','gdpr'],'boolean', 'trueValue' => true, 'falseValue' => false],
            [['visibility'],'in', 'range' => ['public', 'private', 'ingame']],
            [['visibility'],'default', 'value' =>  'private'],
            [['id'],'default', 'value' =>  new Expression('round(rand()*10000000)')],
            [['id', 'player_id'], 'integer'],
            [['bio'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['avatar', 'twitter','github'], 'string', 'max' => 255],
            [['country'], 'string','max'=>3],
            [['player_id'], 'unique'],
            [['id'], 'unique'],
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
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }
    public function getLink()
  	{
  		return Html::a(Html::encode($this->owner->username),['frontend/profile/view','id'=>$this->id]);
  	}

}
