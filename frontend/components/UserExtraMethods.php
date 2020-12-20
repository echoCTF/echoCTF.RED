<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\rbac\CheckAccessInterface;
use yii\web\UserEvent;
use yii\web\Response;
use yii\web\ForbiddenHttpException;

/**
 * @property null|int $expire
 * @property null|int $expireAbsolute
 */
class UserExtraMethods extends \yii\web\User
{

  public function doAutoLogin($identity,$id)
  {
    if ($this->enableAutoLogin) {
        if ($this->getIsGuest()) {
            $this->loginByCookie();
        } elseif ($this->autoRenewCookie) {
            $authKey = json_decode(Yii::$app->getRequest()->getCookies()->getValue($this->identityCookie['name']),TRUE)[1];
            if($identity->validateAuthKey($authKey)) {
                $this->renewIdentityCookie();
                Yii::info("User $id succeeded authKey validation");
            } else {
                $this->logout();
                Yii::info("User $id failed authKey validation");
            }
        }
    }
  }

  /**
   * Returns a value indicating whether the user is a guest (not authenticated).
   * @return bool whether the current user is a guest.
   * @see getIdentity()
   */
  public function getIsGuest()
  {
      return $this->getIdentity() === null;
  }

  /**
   * Returns a value that uniquely represents the user.
   * @return string|int the unique identifier for the user. If `null`, it means the user is a guest.
   * @see getIdentity()
   */
  public function getId()
  {
      $identity = $this->getIdentity();

      return $identity !== null ? $identity->getId() : null;
  }

  protected function getIdFromSession($session)
  {
    return $session->getHasSessionId() || $session->getIsActive() ? $session->get($this->idParam) : null;
  }

  protected function getTimeoutsSet()
  {
    return ($this->authTimeout !== null || $this->absoluteAuthTimeout !== null);
  }
  protected function getExpire()
  {
    return $this->authTimeout !== null ? $session->get($this->authTimeoutParam) : null;
  }
  protected function getExpireAbsolute()
  {
    $session=Yii::$app->getSession();
    return $this->absoluteAuthTimeout !== null ? $session->get($this->absoluteAuthTimeoutParam) : null;
  }
  protected function getExpired()
  {
    $expire = $this->expire;
    $expireAbsolute = $this->expireAbsolute;
    return ($expire !== null && $expire < time() || $expireAbsolute !== null && $expireAbsolute < time());

  }

  protected function identitySanityCheck($identity,$class)
  {
    if (!$identity instanceof \app\models\Player) {
        throw new InvalidValueException("$class::findIdentity() must return an object implementing IdentityInterface.");
    }
  }


  /**
   * Logs in a user by the given access token.
   * This method will first authenticate the user by calling [[IdentityInterface::findIdentityByAccessToken()]]
   * with the provided access token. If successful, it will call [[login()]] to log in the authenticated user.
   * If authentication fails or [[login()]] is unsuccessful, it will return null.
   * @param string $token the access token
   * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
   * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
   * @return null the identity associated with the given access token. Null is returned if
   * the access token is invalid or [[login()]] is unsuccessful.
   */
  public function loginByAccessToken($token, $type = null)
  {
      return null;
  }

}
