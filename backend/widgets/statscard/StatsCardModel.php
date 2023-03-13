<?php

namespace app\widgets\statscard;

use yii\base\UserException;

/**
 * Render a stats card widget based on a modelClass
 *
 * @author Pantelis Roditis <proditis@echothrust.com>
 */
class StatsCardModel extends StatsCard
{
  public $modelClass;
  public $field;
  public function init()
  {
    parent::init();

    if ($this->modelClass === null) {
      throw new UserException("modelClass is required");
    }

    if ($this->field === null) {
      $this->field = 'created_at';
    }

    if ($this->today === null) {
      $todayQ = sprintf("date([[%s]])=date(now())", $this->field);
      $this->today = intval($this->modelClass::find()->where($todayQ)->count());
    }

    if ($this->yesterday === null) {
      $yesterQ = sprintf("date([[%s]])=date(now()-interval 1 day)", $this->field);
      $this->yesterday = intval($this->modelClass::find()->where($yesterQ)->count());
    }

    if ($this->total === null) {
      $this->total = number_format(intval($this->modelClass::find()->count()));
    }
  }
}
