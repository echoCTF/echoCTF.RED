<?php

namespace app\components\columns;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
/**
 * Column for boolean value
 * [[attribute]] Reflects to the attribute name expected from the model
 */

class AcademicColumn extends \yii\grid\DataColumn
{

  public function init()
  {
    parent::init();
    $this->format = "html";
    if (!$this->attribute) {
      throw new InvalidConfigException('No {attribute} provided.');
    }
    if (intval(\Yii::$app->sys->academic_grouping)>0) {
      $filter=[];
      for ($i = 0; $i < intval(\Yii::$app->sys->academic_grouping); $i++) {
        $filter[] = $i.': '.\Yii::$app->sys->{"academic_" . $i . "short"};
      }
      $this->filter=$filter;
      $this->visible=true;
    } else {
      $this->filter=[ 'No groupings' ];
      $this->visible=false;
    }
    $this->filterAttribute = $this->attribute;
  }

  public function getFilter(){
    return $this->filter;
  }
}
