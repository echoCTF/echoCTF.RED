<?php
namespace app\models\forms;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use app\models\Player;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;

    /**
     * @var \app\models\Player
     */
    private $_player;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, $config=[])
    {
        if(empty($token) || !is_string($token))
        {
            throw new InvalidArgumentException('Password reset token cannot be blank.');
        }
        $this->_player=Player::findByPasswordResetToken($token);

        if(!$this->_player)
        {
            throw new InvalidArgumentException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $player=$this->_player;
        $player->setPassword($this->password);
        $player->removePasswordResetToken();

        return $player->save(false);
    }

    public function getPlayer()
    {
      return $this->_player;
    }
}
