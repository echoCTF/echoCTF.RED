<?php

namespace app\models\forms;

use app\models\Player;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;

class VerifyEmailForm extends Model
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var Player
     */
    private $_player;


    /**
     * Creates a form model with given token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, array $config = [])
    {
        if (empty($token) || !is_string($token))
        {
            throw new InvalidArgumentException('Verify email token cannot be blank.');
        }
        $this->_player = Player::findByVerificationToken($token);
        if (!$this->_player)
        {
            throw new InvalidArgumentException('Wrong verify email token.');
        }
        parent::__construct($config);
    }

    /**
     * Verify email
     *
     * @return Player|null the saved model or null if saving fails
     */
    public function verifyEmail()
    {
        $player = $this->_player;
        $player->status = Player::STATUS_ACTIVE;
        $player->active=1;
        return $player->save(false) ? $player : null;
    }
}
