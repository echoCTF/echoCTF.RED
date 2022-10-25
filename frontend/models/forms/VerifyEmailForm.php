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
    public function __construct($token, array $config=[])
    {
        if(empty($token) || !is_string($token))
        {
            throw new InvalidArgumentException('Verify email token cannot be blank.');
        }
        $this->_player=Player::findByVerificationToken($token);
        if(!$this->_player)
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
        $player=$this->_player;
        $oldStatus=$this->_player->status;
        $player->status=Player::STATUS_ACTIVE;
        $player->active=1;
        $this->genAvatar();
        if($player->saveWithSsl())
        {
          if($oldStatus===Player::STATUS_INACTIVE)
          {
            $player->trigger(Player::NEW_PLAYER);
            $player->profile->last->signin_ip=ip2long(\Yii::$app->request->userIp);
            $player->profile->last->save();
          }
          return $this->_player;
        }
        return null;
    }
    private function genAvatar()
    {
      $dst_img=\Yii::getAlias('@app/web/images/avatars/'.$this->_player->profile->id.'.png');
      if(file_exists($dst_img))
        return;
      $robohash=new \app\models\Robohash($this->_player->profile->id,'set1');
      $image=$robohash->generate_image();
      if(get_resource_type($image)=== 'gd')
      {
        $dst_img=\Yii::getAlias('@app/web/images/avatars/'.$this->_player->profile->id.'.png');
        imagepng($image,$dst_img);
        imagedestroy($image);
        $this->_player->profile->avatar=$this->_player->profile->id.'.png';
        $this->_player->profile->save(false);
      }
    }
    public function getPlayer()
    {
      return $this->_player;
    }
}
