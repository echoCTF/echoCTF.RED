<?php

namespace app\components\columns;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
/**
 * Column for boolean value
 * [[attribute]] Reflects to the attribute name expected from the model
 */

class BooleanColumn extends \yii\grid\DataColumn
{

  public function init()
  {
    parent::init();
    $this->format = "html";
    if (!$this->attribute) {
      throw new InvalidConfigException('No {attribute} provided.');
    }

    $this->filterAttribute = $this->attribute;
  }

  protected function renderDataCellContent($model, $key, $index)
  {
    $value=(bool) ArrayHelper::getValue($model, $this->attribute);
    if ($this->content === null) {
      if($value)
        return $this->grid->formatter->format('<i class="fas fa-check text-success"></i>', $this->format);
      return $this->grid->formatter->format('<i class="fas fa-times text-danger"></i>', $this->format);
    }
    return parent::renderDataCellContent($model, $key, $index);
  }
}
