<?php
/**
 * Check MySQL IP counter.
 */
namespace app\components\validators;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class TotalRegistrationsValidator extends Validator
{
    public $max=10;
    public $message="You reached your maximum registrations for this IP!";
    public $counter;
    public $client_ip;
    public function init()
    {
        if(!$this->counter)
          $this->counter=\Yii::$app->db
              ->createCommand('SELECT count(*) FROM player_last WHERE signup_ip=:player_ip')
              ->bindValue(':player_ip',ip2long($this->client_ip))
              ->queryScalar();
        parent::init();
    }
    public function validateValue($value)
    {
      if (\Yii::$app->sys->signup_TotalRegistrationsValidator!==false && intval($this->counter)>=$this->max)
      {
        return [$this->message, [
            'username' => $value,
        ]];
      }
    }
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (\Yii::$app->sys->signup_TotalRegistrationsValidator!==false && intval($this->counter)>=$this->max)
        {
            $model->addError($attribute, $this->message);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
      if(\Yii::$app->sys->signup_TotalRegistrationsValidator!==false)
      {
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
if ({$this->counter}>={$this->max}) {
    messages.push($message);
    return false;
}
JS;
      }
    }
}
