<?php
/**
 * Dummy Subscription module
 */

namespace app\models;
use Yii;

class DummySubscription extends yii\base\Model
{

    public function rules()
    {
        return [];
    }

    public function attributeLabels()
    {
        return [];
    }
    public function getExists()
    {
      return false;
    }
    public function getIsActive()
    {
      return false;
    }
}
