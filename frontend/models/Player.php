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
 * @property bool $isVip
 */
class Player extends PlayerAR implements IdentityInterface
{
  const NEW_PLAYER = 'new-player';
  const SCENARIO_SETTINGS = 'settings';
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
          'academic' =>  AttributeTypecastBehavior::TYPE_INTEGER,
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
      'default' => ['id', 'username', 'email', 'password', 'fullname', 'active', 'status', 'new_password', 'confirm_password', 'created', 'ts'],
      self::SCENARIO_SETTINGS => ['username', 'email', 'fullname', 'new_password', 'confirm_password'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function findIdentity($id)
  {
    return static::findOne(['player.id' => $id, 'player.status' => self::STATUS_ACTIVE]);
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
    return static::findOne(['username' => $username, 'active' => 1, 'status' => self::STATUS_ACTIVE]);
  }

  /**
   * Finds player by email
   *
   * @param string $email
   * @return static|null
   */
  public static function findByEmail($email)
  {
    return static::findOne(['email' => $email, 'active' => 1, 'status' => self::STATUS_ACTIVE]);
  }

  /**
   * Finds player by verification email token
   *
   * @param string $token verify email token
   * @return static|null
   */
  public static function findByVerificationToken($token)
  {
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
    return $this->getAuthKey() === $authKey && $this->status === self::STATUS_ACTIVE && $this->active === 1;
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
    $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    $this->password = Yii::$app->security->generatePasswordHash($password);
  }

  /**
   * Generates "remember me" authentication key
   */
  public function generateAuthKey()
  {
    $this->auth_key = Yii::$app->security->generateRandomString();
  }

  /**
   * Generates new password reset token
   */
  public function generatePasswordResetToken()
  {
    $this->password_reset_token = str_replace('_', '-', Yii::$app->security->generateRandomString() . '-' . time());
  }

  public function generateEmailVerificationToken()
  {
    $this->verification_token = str_replace('_', '-', Yii::$app->security->generateRandomString() . '-' . time());
  }

  /**
   * Removes password reset token
   */
  public function removePasswordResetToken()
  {
    $this->password_reset_token = null;
  }

  /**
   * Get status Label
   */
  public function getStatusLabel()
  {
    switch ($this->status) {
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
    $command = Yii::$app->db->createCommand('select counter from player_spin WHERE player_id=:player_id');
    return (int) $command->bindValue(':player_id', $this->id)->queryScalar();
  }


  public function getHeadshotsCount()
  {
    return $this->hasMany(Headshot::class, ['player_id' => 'id'])->count();
  }

  public function getOnVPN(): bool
  {
    return Yii::$app->cache->memcache->get('ovpn:' . $this->id) !== false;
  }

  public function getVpnIP()
  {
    return Yii::$app->cache->memcache->get('ovpn:' . $this->id);
  }

  public function getIsAdmin(): bool
  {
    $admin_ids = \Yii::$app->params['admin_ids'];
    $memc_admin_ids = [];
    if (Yii::$app->sys->admin_ids !== false)
      $memc_admin_ids = \yii\helpers\ArrayHelper::getColumn(explode(",", Yii::$app->sys->admin_ids), function ($element) {
        return intval($element);
      });
    if (Yii::$app->sys->{'admin_player:' . $this->id} !== false)
      return true;
    return !(array_search(intval($this->id), \yii\helpers\ArrayHelper::merge($admin_ids, $memc_admin_ids), true) === false); // error is here
  }

  /**
   * Check if the user is considered a VIP
   *
   * @return bool
   */
  public function getIsVip(): bool
  {
    if ($this->isAdmin || Yii::$app->sys->all_players_vip === true || ($this->subscription !== null && $this->subscription->active > 0))
      return true;
    return false;
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
    if (empty($token) || trim($token) === "") {
      return false;
    }
    return true;
    $timestamp = (int) substr($token, strrpos($token, '_') + 1);
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
    if (!static::isPasswordResetTokenValid($token)) {
      return null;
    }

    return static::findOne([
      'password_reset_token' => $token,
      'status' => self::STATUS_ACTIVE,
    ]);
  }

  public function saveWithSsl($validation = true)
  {

    if (!$this->save($validation))
      return false;

    if ($this->active == 1 && $this->status == 10 && $this->sSL === null) {
      $playerSsl = new PlayerSsl();
      $playerSsl->player_id = $this->id;
      $playerSsl->generate();
      if ($playerSsl->save()) {
        return $playerSsl->refresh();
      }
      return false;
    }
    return true;
  }

  public function saveNewPlayer($validation = true)
  {
    if (!$this->saveWithSsl($validation))
      return false;

    if (($profile = $this->profile) == null) {
      $profile = new Profile();
      $profile->player_id = $this->id;
    }

    $profile->scenario = 'signup';
    $profile->visibility = \Yii::$app->sys->profile_visibility !== false ? Yii::$app->sys->profile_visibility : 'ingame';
    $profile->gdpr = true;
    $profile->terms_and_conditions = true;
    if (!$profile->save())
      return false;
    return $profile;
  }

  public function genAvatar()
  {
    $_pID = $this->profile->id;
    $avatarsDIR = \Yii::getAlias('@app/web/images/avatars/');
    $avatarPNG = \Yii::getAlias('@app/web/images/avatars/' . $_pID . '.png');
    if (is_writable($avatarsDIR) === false || (file_exists($avatarPNG) && is_writable($avatarPNG) === false)) {
      \Yii::error('The avatars folder or avatar file is not writeable. correct the permissions for the avatars to be generated.');
      return;
    }
    if (file_exists($avatarPNG))
      return;
    $robohash = new \app\models\Robohash($_pID, 'set1');
    $image = $robohash->generate_image();
    if ((gettype($image) === "object" && get_class($image) === "GdImage") || ((int) phpversion() === 7 && gettype($image) === 'resource')) {
      imagepng($image, $avatarPNG);
      imagedestroy($image);
      $this->profile->avatar = $_pID . '.png';
      $this->profile->save(false);
    }
  }

  public function getAcademicWord()
  {
    switch ($this->academic) {
      case 0:
        return ".gov";
      case 1:
        return ".edu";
      default:
        return ".pro";
    }
  }

  public function getAcademicShort()
  {
    switch ($this->academic) {
      case 0:
        return ".gov";
      case 1:
        return ".edu";
      default:
        return ".pro";
    }
  }
  public function getAcademicIcon()
  {
    switch ($this->academic) {
      case 0:
        return "government.svg";
      case 1:
        return "education.svg";
      default:
        return "professional.svg";
    }
  }

  /**
   * Send mail to player with
   * @param string $subject
   * @param string $html
   * @param string $txt
   * @param array $headers
   * @return bool
   */
  public function mail($subject, $html, $txt, $headers = [])
  {
    // Get mailer
    try {
      $message = \Yii::$app->mailer->compose()
        ->setFrom([\Yii::$app->sys->mail_from => \Yii::$app->sys->mail_fromName])
        ->setTo([$this->email => $this->username])
        ->setSubject($subject)
        ->setTextBody($txt)
        ->setHtmlBody($html);

      foreach ($headers as $entry) {
        $message->addHeader($entry[0], $entry[1]);
      }
      $message->send();
      if (Yii::$app instanceof \yii\web\Application)
        \Yii::$app->session->setFlash('success', Yii::t('app', "The user has been mailed."));
      else {
        echo Yii::t('app', "The user has been mailed.\n");
      }
    } catch (\Exception $e) {
      if (Yii::$app instanceof \yii\web\Application)
        \Yii::$app->session->setFlash('notice', Yii::t('app', "Failed to send mail to user."));
      else
        echo Yii::t('app', "Failed to send mail to user.\n");
      return false;
    }
    return true;
  }
}
