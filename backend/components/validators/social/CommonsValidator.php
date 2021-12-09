<?php
/**
 * Twitter Handle Validator
 */
namespace app\components\validators\social;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class CommonsValidator extends Validator
{
    public $maxlen=15;
    public $range=['admin', 'twitter', 'echoctf'];
    public $pattern='^[a-zA-Z0-9_]+$';
    public $range_msg="Value administratively prohibited.";
    public $len_msg="Value too long.";
    public $pattern_msg="Invalid characters only <kbd>a-z</kbd>, <kbd>A-Z</kbd>, <kbd>0-9</kbd> and <kbd>_</kbd>";

    public function init()
    {
        parent::init();
        $this->message = 'Invalid value on input.';
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (strlen($value)>$this->maxlen)
        {
            $model->addError($attribute, $this->len_msg);
        }
        if (preg_match('/'.$this->pattern.'/',$value)!==1)
        {
            $model->addError($attribute, $this->pattern_msg);
        }
        if (ArrayHelper::isIn(mb_strtolower($value), (array) $this->range, false))
        {
            $model->addError($attribute, $this->range_msg);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $range = json_encode($this->range);
        $range_msg = json_encode($this->range_msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $pattern_msg = json_encode($this->pattern_msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $len_msg = json_encode($this->len_msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
const regex = new RegExp('{$this->pattern}');
if ($.inArray(value, $range) !== -1) {
    messages.push($range_msg);
    return false;
}
if (value.length > {$this->maxlen}) {
    messages.push($len_msg);
    return false;
}

if (value.length>0 && !regex.test(value)) {
    messages.push($pattern_msg);
    return false;
}
JS;
    }
}
