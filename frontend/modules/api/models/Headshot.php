<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "headshot" for the REST API
 *
 * @property int $profile_id
 * @property int $target_id
 * @property string $target_name
 * @property int $timer
 * @property int $rating
 * @property boolean $first
 * @property string|null $created_at
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
  }
}
