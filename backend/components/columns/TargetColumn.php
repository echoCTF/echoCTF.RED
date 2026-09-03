<?php

namespace app\components\columns;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Column for target name link to target page
 * [[idkey]] The target id path to be used for generating the link to the profile
 * [[field]] The field name to retrieve the value from
 * [[attribute]] Reflects to the attribute name expected from the search model
 */

class TargetColumn extends \yii\grid\DataColumn
{
  public $idkey = 'target_id';
  public $field = 'target.name';

  public function init()
  {
    parent::init();
    $this->format = "raw";
    if (!$this->attribute) {
      $this->attribute = 'target_name';
      $this->label = 'Target';
    }

    $this->filterAttribute = $this->attribute;
  }

  protected function renderDataCellContent($model, $key, $index)
  {
    $target_name = ArrayHelper::getValue($model, $this->field);
    $id = ArrayHelper::getValue($model, $this->idkey);
    if ($this->content === null) {

      return $this->grid->formatter->format(Html::a($target_name, ['/infrastructure/target/full-view', 'id' => $id]), $this->format);
    }

    return parent::renderDataCellContent($model, $key, $index);
  }
}
