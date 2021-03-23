<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "headshot".
 *
 * @property int $player_id
 * @property int $target_id
 * @property string|null $created_at
 * @property int $timer
 * @property int $rating
 * @property boolean $first
 * @property string|null $rated
 *
 * @property Player $player
 * @property Target $target
 * @property Writeup $writeup
 */
class Headshot extends \app\modules\game\models\Headshot
{
  public $profile_id;
  public $target_name;

  public static function find()
  {
      return new HeadshotQuery(get_called_class());
  }

  public function fields()
  {
      return [
          'profile_id',
          'target_id',
          'target_name',
          'timer',
          'first',
          'rating',
          'created_at'
      ];
  }
  public function extraFields()
  {
    return [];
    //  return ['profile','target'];
  }


}
