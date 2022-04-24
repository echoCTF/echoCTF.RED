<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use app\modules\game\models\Headshot;
use app\modules\target\models\TargetInstance;

/**
 * This is the model class that only holds relations for the table "player".
 *
 * @property integer $id
 * @property string $username
 * @property string $fullname
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $active
 * @property integer $academic
 * @property string $password write-only password
 *
 * @property Profile $profile
 * @property PlayerScore $playerScore
 * @property PlayerSsl $playerSsl
 * @property TargetInstance $instance
 * @property Subscription $subscription
 */
class PlayerAR extends ActiveRecord
{

  const STATUS_DELETED=0;
  const STATUS_UNVERIFIED=8;
  const STATUS_INACTIVE=9;
  const STATUS_ACTIVE=10;
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
      return [
          /* fullname rules */
          [['fullname'], 'trim'],
          [['fullname'], 'string', 'max'=>32],

          /* email field rules */
          [['email'], 'trim'],
          [['email'], 'string', 'max'=>255],
          [['email'], 'email'],
          ['email', 'unique', 'targetClass' => '\app\models\Player', 'message' => 'This email has already been taken.', 'when' => function($model, $attribute) {
              return $model->{$attribute} !== $model->getOldAttribute($attribute);
          }],
          ['email', 'unique', 'targetClass' => '\app\models\BannedPlayer', 'message' => 'This email is banned.', 'when' => function($model, $attribute) {
              return $model->{$attribute} !== $model->getOldAttribute($attribute);
          }],
          ['email', function($attribute, $params){
            $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM banned_player WHERE :email LIKE email')
                ->bindValue(':email', $this->email)
                ->queryScalar();

            if(intval($count)!==0)
                $this->addError($attribute, 'This email is banned.');
          }],

          /* username field rules */
          [['username'], 'trim'],
          [['username'], 'string', 'max'=>32],
          [['username'], 'match', 'not'=>true, 'pattern'=>'/[^a-zA-Z0-9]/', 'message'=>'Invalid characters in username.'],
          [['username'], '\app\components\validators\LowerRangeValidator', 'not'=>true, 'range'=>['admin', 'administrator', 'echoctf', 'root', 'support']],
          [['username'], 'required', 'message' => 'Please choose a username.'],
          ['username', 'unique', 'targetClass' => '\app\models\Player', 'message' => 'This username has already been taken.', 'when' => function($model, $attribute) {
              return $model->{$attribute} !== $model->getOldAttribute($attribute);
          }],

          /* active field rules */
          //[['active'], 'filter', 'filter' => 'boolval'],
          [['active'], 'default', 'value' => function ($model){
            if(\Yii::$app->sys->require_activation===true)
            {
              return true;
            }
            return false;
          }],

          /* status field rules */
          //[['status'], 'filter', 'filter' => 'intval'],
          [['status'], 'default', 'value' => function ($model){
            if(\Yii::$app->sys->require_activation===true)
            {
              return Player::STATUS_INACTIVE;
            }
            return Player::STATUS_ACTIVE;
          }],
          [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNVERIFIED, self::STATUS_INACTIVE, self::STATUS_DELETED]],
          /* password field rules */

//            [['password',], 'default','value'=>null],
          [['new_password', ], 'string', 'max'=>255],
          [['confirm_password'], 'string', 'max'=>255],
          [['new_password'], 'compare', 'compareAttribute'=>'confirm_password'],
          [['created', 'ts'], 'safe'],

      ];
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
      return 'player';
  }

  public function getTeamLeader()
  {
    if(array_key_exists('team',Yii::$app->modules))
      return $this->hasOne(\app\modules\team\models\Team::class, ['owner_id' => 'id']);
    return null;
  }
  public function getTeamPlayer()
  {
    if(array_key_exists('team',Yii::$app->modules))
      return $this->hasOne(\app\modules\team\models\TeamPlayer::class, ['player_id' => 'id']);
    return null;
  }

  public function getTeam()
  {
    if(array_key_exists('team',Yii::$app->modules))
      return $this->hasOne(\app\modules\team\models\Team::class, ['id' => 'team_id'])->viaTable('team_player', ['player_id'=>'id']);
    return null;
  }
  public function getPlayerHints()
  {
      return $this->hasMany(PlayerHint::class, ['player_id' => 'id']);
  }
  /**
   * @return \yii\db\ActiveQuery
   */
  public function getHints()
  {
      return $this->hasMany(Hint::class, ['id' => 'player_id'])->viaTable('player_hint', ['hint_id' => 'id']);
  }

  public function getPlayerHintsForTarget(int $target_id)
  {
      return $this->hasMany(PlayerHint::class, ['player_id' => 'id'])->forTarget($target_id);
  }

  public function getPendingHints()
  {
      return $this->hasMany(PlayerHint::class, ['player_id' => 'id'])->pending();
  }

  public function getNotifications()
  {
      return $this->hasMany(Notification::class, ['player_id' => 'id']);
  }
  public function getPendingNotifications()
  {
      return $this->hasMany(Notification::class, ['player_id' => 'id'])->pending();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getTreasures(int $target_id=null)
  {
    if($target_id===null)
      return $this->hasMany(\app\modules\target\models\Treasure::class, ['id' => 'treasure_id'])->viaTable('player_treasure', ['player_id' => 'id']);

    return $this->hasMany(\app\modules\target\models\Treasure::class, ['id' => 'treasure_id'])->onCondition(['target_id' => $target_id])->viaTable('player_treasure', ['player_id' => 'id']);
  }
  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayerFindings()
  {
      return $this->hasMany(PlayerFinding::class, ['player_id' => 'id']);
  }

  public function getChallengeSolvers()
  {
      return $this->hasMany(\app\modules\challenge\models\ChallengeSolver::class, ['player_id' => 'id']);
  }

  public function getChallenges()
  {
    return $this->hasMany(\app\modules\challenge\models\Challenge::class, ['id' => 'challenge_id'])->viaTable('challenge_solver', ['player_id' => 'id']);
  }
  /**
   * @return \yii\db\ActiveQuery
   */
  public function getFindings(int $target_id=null)
  {
    if($target_id===null)
      return $this->hasMany(\app\modules\target\models\Finding::class, ['id' => 'finding_id'])->viaTable('player_finding', ['player_id' => 'id']);

    return $this->hasMany(\app\modules\target\models\Finding::class, ['id' => 'finding_id'])->onCondition(['target_id' => $target_id])->viaTable('player_finding', ['player_id' => 'id']);
  }

  public function getPlayerTreasures()
  {
      return $this->hasMany(PlayerTreasure::class, ['player_id' => 'id']);
  }
  public function getHeadshots()
  {
      return $this->hasMany(Headshot::class, ['player_id' => 'id'])->orderBy(['created_at'=>SORT_ASC]);
  }

  public function getNetworkPlayer()
  {
      return $this->hasMany(\app\modules\network\models\NetworkPlayer::class, ['player_id' => 'id']);
  }
  public function getNetworks()
  {
    return $this->hasMany(\app\modules\network\models\Network::class, ['id' => 'network_id'])->via('networkPlayer');
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getProfile()
  {
      return $this->hasOne(Profile::class, ['player_id' => 'id']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getInstance()
  {
      return $this->hasOne(TargetInstance::class, ['player_id' => 'id']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getSubscription()
  {
      return $this->hasOne(\app\modules\subscription\models\PlayerSubscription::class, ['player_id' => 'id']);
  }

  public function getPlayerScore()
  {
      return $this->hasOne(PlayerScore::class, ['player_id' => 'id']);
  }

  public function getPlayerRank()
  {
      return $this->hasOne(PlayerRank::class, ['player_id' => 'id']);
  }

  public function getSSL()
  {
    return $this->hasOne(PlayerSsl::class, ['player_id' => 'id']);
  }

}
