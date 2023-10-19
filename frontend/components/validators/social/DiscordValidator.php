<?php
/**
 * Twitter Handle Validator
 */
namespace app\components\validators\social;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class DiscordValidator extends Validator
{
    public $maxlen=32;
    public $minlen=2;
    public $range=['discordtag', 'everyone', 'here'];
    public $pattern='^[a-z0-9_.]+$';
    public $range_msg="Value administratively prohibited.";
    public $len_msg="Value must be between 2 and 32 characters long.";
    public $pattern_msg;

    public function init()
    {
        parent::init();
        $this->pattern_msg=\Yii::t('app',"Invalid characters only <kbd>a-z</kbd>, <kbd>0-9</kbd> and the special characters underscore <kbd>_</kbd> and period <kbd>.</kbd>");
        $this->message = \Yii::t('app','Invalid value on input.');
    }

    public function validateAttribute($model, $attribute)
    {
        $value = mb_strtolower($model->$attribute);
        if (strlen($value)>$this->maxlen || strlen($value)<2)
        {
            $model->addError($attribute, $this->len_msg);
        }
        if (preg_match('/'.$this->pattern.'/',$value)!==1)
        {
            $model->addError($attribute, $this->pattern_msg);
        }
        if (ArrayHelper::isIn($value, (array) $this->range, false))
        {
            $model->addError($attribute, $this->range_msg);
        }
        if (strpos($value, '..')!==false) {
             $model->addError($attribute,\Yii::t('app','Discord username must not include two consecutive dots [<kbd>..</kbd>]'));
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $range = json_encode($this->range);
        $range_msg = json_encode($this->range_msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $pattern_msg = json_encode($this->pattern_msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $len_msg = json_encode($this->len_msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $tag_msg = json_encode('Discord username must not include two consecutive dots [<kbd>..</kbd>]', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
const regex = new RegExp('{$this->pattern}');
if(value.length==0)
  return;

if ($.inArray(value, $range) !== -1) {
    messages.push($range_msg);
    return false;
}
if (value.length > {$this->maxlen} || value.length < {$this->minlen}) {
    messages.push($len_msg);
    return false;
}

if (value.length>=2 && !regex.test(value)) {
    messages.push($pattern_msg);
    return false;
}
if(value.includes('..'))
{
  messages.push($tag_msg);
  return false;
}
JS;
    }
}
