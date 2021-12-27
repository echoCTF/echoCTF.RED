<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Player|null $player This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe=true;

    private $_player=null;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username'], 'required','message' => 'Please provide your username or email.'],
            [['password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute)
    {
        if(!$this->hasErrors())
        {
            $player=$this->player;

            $failed_login_ip=intval(\Yii::$app->cache->memcache->get('failed_login_ip:'.\Yii::$app->request->userIp));
            $failed_login_username=intval(\Yii::$app->cache->memcache->get('failed_login_username:'.$this->username));

            if($failed_login_ip>=5  /* || $failed_login_username>=10 */ )
            {
              $this->addError($attribute, 'Too many failed log in attempts. Please wait and try again. ['.$failed_login_ip.'/'.$failed_login_username.']');
              return;
            }
            if(!$player || !$player->validatePassword($this->password))
            {
                $failed_login_ip++;
                $failed_login_username++;
                \Yii::$app->cache->memcache->set('failed_login_ip:'.\Yii::$app->request->userIp,$failed_login_ip, 900);
                \Yii::$app->cache->memcache->set('failed_login_username:'.$this->username,$failed_login_username, 900);
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a player using the provided username and password.
     * @return bool whether the player is logged in successfully
     */
    public function login()
    {
        if($this->validate() && $this->player !== null)
        {
            return Yii::$app->user->login($this->player, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds player by [[username]]
     *
     * @return Player|null
     */
    public function getPlayer()
    {
      $this->_player=Player::findByUsername($this->username);
      if($this->_player === null)
      {
        $this->_player=Player::findByEmail($this->username);
      }
      return $this->_player;
    }
}
