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

    public function init()
    {
        if(!$this->counter)
          $this->counter=\Yii::$app->db
              ->createCommand('SELECT count(*) FROM player_last WHERE signup_ip=:player_ip)')
              ->bindValue(':player_ip',ip2long(\Yii::$app->request->userIp))
              ->queryScalar();
        parent::init();
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
