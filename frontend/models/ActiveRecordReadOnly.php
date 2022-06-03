<?php

namespace app\models;

use Yii;

class ActiveRecordReadOnly extends \yii\db\ActiveRecord
{
  public function save($runValidation=true, $attributeNames=null)
  {
    throw new \LogicException("Saving is disabled for this model.");
  }
}