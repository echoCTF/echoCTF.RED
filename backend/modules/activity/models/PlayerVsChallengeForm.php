<?php
namespace app\modules\activity\models;

use Yii;
use yii\base\Model;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Challenge;
use app\modules\gameplay\models\Question;

class PlayerVsChallengeForm extends \yii\base\Model
{
  public $player_id;
  public $challenge_id;

  public function rules()
  {
      return [
          [['player_id', 'challenge_id'], 'required'],
      ];
  }

  public function getPlayer()
  {
      return Player::findOne($this->player_id);
  }

  public function getChallenge()
  {
      return Challenge::findOne($this->challenge_id);
  }

  public function hasQuestion($question_id)
  {
    if(PlayerQuestion::findOne(['player_id' => $this->player_id, 'question_id' => $question_id]))
      return true;
    return false;
  }

}
