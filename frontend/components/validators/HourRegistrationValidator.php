<?php
/**
 * Check memcache key for registrations performed by this IP.
 */
namespace app\components\validators;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class HourRegistrationValidator extends Validator
{
    public $max=3;
    public $message;
    public $counter;
    public $client_ip;
    public function init()
    {
        $this->message=\Yii::t('app',"You reached your maximum registrations for this hour!");
        if(!$this->counter)
          $this->counter=intval(\Yii::$app->sys->{'registeredip:'.$this->client_ip});
        parent::init();
    }
    public function validateValue($value)
    {
      if (intval($this->counter)>=$this->max)
      {
        return [$this->message, [
            'signup_ip' => $value,
        ]];
      }

    }
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (intval($this->counter)>=$this->max)
        {
            $model->addError($attribute, $this->message);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
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
