<?php
/**
 * Twitter Handle Validator
 */
namespace app\components\validators\social;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class YoutubeValidator extends CommonsValidator
{
    public $maxlen=42;
    public $range=['admin', 'twitter', 'help','about'];
    public $pattern='^[a-zA-Z0-9_-]+$';
    public $range_msg="Value not allowed.";
    public $len_msg="Value too long.";
    public $pattern_msg='Invalid characters only <kbd>a-z</kbd>, <kbd>A-Z</kbd>, <kbd>0-9</kbd>, <kbd>-</kbd> and <kbd>_</kbd>';
}
