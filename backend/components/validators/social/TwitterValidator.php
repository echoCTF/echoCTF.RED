<?php
/**
 * Twitter Handle Validator
 */
namespace app\components\validators\social;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class TwitterValidator extends CommonsValidator
{
    public $maxlen=15;
    public $range=['admin', 'twitter','about','info'];
    public $pattern='^[a-zA-Z0-9_]+$';
    public $range_msg="Twitter handle value not allowed.";
    public $len_msg="Twitter handle too long.";
    public $pattern_msg="Invalid characters only <kbd>a-z</kbd>, <kbd>A-Z</kbd>, <kbd>0-9</kbd> and <kbd>_</kbd>";
}
