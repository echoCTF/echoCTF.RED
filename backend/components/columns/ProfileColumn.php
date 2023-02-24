<?php

namespace app\components\columns;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Column for username link to player profile
 * [[idkey]] The profile id path to be used for generating the link to the profile
 * [[field]] The field name to retrieve the value from
 * [[attribute]] Reflects to the attribute name expected from the search model
 */

class ProfileColumn extends \yii\grid\DataColumn
{
  public $idkey = 'player.profile.id';
  public $field = 'player.username';

  public function init()
  {
    parent::init();
    $this->format = "raw";
    if (!$this->attribute) {
      $this->attribute = 'username';
      $this->label = 'Username';
    }

    $this->filterAttribute = $this->attribute;
  }

  protected function renderDataCellContent($model, $key, $index)
  {
    $username = ArrayHelper::getValue($model, $this->field);
    $id = ArrayHelper::getValue($model, $this->idkey);
    if ($this->content === null) {
      return $this->grid->formatter->format(Html::a($username, ['/frontend/profile/view-full', 'id' => $id]), $this->format);
    }

    return parent::renderDataCellContent($model, $key, $index);
  }
}
