<?php
namespace app\modules\activity\models;

use Yii;
use yii\base\Model;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;

class PlayerVsTargetForm extends \yii\base\Model
{
  public $player_id;
  public $target_id;

  public function rules()
  {
      return [
          [['player_id', 'target_id'], 'required'],
      ];
  }

  public function getPlayer()
  {
      return Player::findOne($this->player_id);
  }

  public function getTarget()
  {
      return Target::findOne($this->target_id);
  }

  public function hasFinding($finding_id)
  {
    if(PlayerFinding::findOne(['player_id' => $this->player_id, 'finding_id' => $finding_id]))
      return true;
    return false;
  }
  public function hasTreasure($treasure_id)
  {
    if(PlayerTreasure::findOne(['player_id' => $this->player_id, 'treasure_id' => $treasure_id]))
      return true;
    return false;
  }

}
