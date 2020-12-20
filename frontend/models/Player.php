<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use yii\behaviors\AttributeTypecastBehavior;
use app\modules\game\models\Headshot;

/**
 * Player model
 *

 *
 * @property bool $visibilityAllowed
 * @property bool $visibilityDenied
 */
class Player extends PlayerAR implements IdentityInterface
{
    const NEW_PLAYER='new-player';
    const SCENARIO_SETTINGS='settings';
    public $new_password;
    public $confirm_password;

    public function init()
    {
        parent::init();
        $this->on(self::NEW_PLAYER, ['\app\components\PlayerEvents', 'addToRank']);
        $this->on(self::NEW_PLAYER, ['\app\components\PlayerEvents', 'giveInitialHint']);
        $this->on(self::NEW_PLAYER, ['\app\components\PlayerEvents', 'sendInitialNotification']);
        $this->on(self::NEW_PLAYER, ['\app\components\PlayerEvents', 'addStream']);
    }

    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'status' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'active' =>  AttributeTypecastBehavior::TYPE_INTEGER,
                ],
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
          ],
          [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'ts',
                'value' => new Expression('NOW()'),
          ],
        ];
    }

    public function scenarios()
    {
        return [
            'default' => ['id','username', 'email', 'password','fullname','active','status','new_password','confirm_password','created','ts'],
            self::SCENARIO_SETTINGS => ['username', 'email', 'fullname','new_password','confirm_password'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds player by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds player by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds player by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => [self::STATUS_INACTIVE, self::STATUS_UNVERIFIED]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current player
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash=Yii::$app->security->generatePasswordHash($password);
        $this->password=Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key=Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token=Yii::$app->security->generateRandomString().'_'.time();
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token=Yii::$app->security->generateRandomString().'_'.time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token=null;
    }

    /**
     * Get status Label
     */
    public function getStatusLabel()
    {
      switch($this->status) {
        case self::STATUS_ACTIVE:
          return 'ACTIVE';
        case self::STATUS_INACTIVE:
          return 'INACTIVE';
        case self::STATUS_DELETED:
          return 'DELETED';
      }
    }

    public function getSpins()
    {
      $command=Yii::$app->db->createCommand('select counter from player_spin WHERE player_id=:player_id');
      return (int) $command->bindValue(':player_id', $this->id)->queryScalar();
    }


    public function getHeadshotsCount()
    {
        return $this->hasMany(Headshot::class, ['player_id' => 'id'])->count();
    }


    public function getIsAdmin():bool
    {
      $admin_ids=\Yii::$app->params['admin_ids'];
      return !(array_search(intval($this->id), $admin_ids) === false);// error is here
    }

    public static function find()
    {
        return new PlayerQuery(get_called_class());
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token, $expire = 86400): bool
    {
        if(empty($token))
        {
            return false;
        }

        $timestamp=(int) substr($token, strrpos($token, '_') + 1);
        return $timestamp + $expire >= time();
    }
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if(!static::isPasswordResetTokenValid($token))
        {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function saveWithSsl($validation=true)
    {
      if(!$this->save($validation))
        return false;

      $playerSsl=new PlayerSsl();
      $playerSsl->player_id=$this->id;
      $playerSsl->generate();
      if($playerSsl->save())
      {
        return $playerSsl->refresh();
      }
      return false;
    }

    public function saveNewPlayer($validation=true)
    {
      if(!$this->saveWithSsl($validation))
        return false;

      if(($profile=$this->profile)==null)
      {
        $profile=new Profile();
        $profile->player_id=$this->id;
      }

      $profile->scenario='signup';
      $profile->visibility=\Yii::$app->sys->profile_visibility!==false ? Yii::$app->sys->profile_visibility : 'ingame';
      $profile->gdpr=true;
      $profile->terms_and_conditions=true;
      if(!$profile->save())
        return false;
      return $profile;
    }
}
