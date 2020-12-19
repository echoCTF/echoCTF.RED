<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\validators\ValidationAsset;
/**
 * RangeValidator validates that the attribute value is among a list of values.
 *
 * The range can be specified via the [[range]] property.
 * If the [[not]] property is set true, the validator will ensure the attribute value
 * is NOT among the specified range.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LowerRangeValidator extends \yii\validators\RangeValidator
{
    /**
     * @var array|\Traversable|\Closure $range
     */
    public $range;
    /**
     * @var bool whether the comparison is strict (both type and value must be the same)
     */
    public $strict=false;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the attribute value should NOT be among the list of values defined via [[range]].
     */
    public $not=false;
    /**
     * @var bool whether to allow array type attribute.
     */
    public $allowArray=false;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if($this->message === null)
        {
            $this->message=Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        $in=false;

        if(ArrayHelper::isIn(mb_strtolower($value), (array) $this->range, $this->strict))
        {
            $in=true;
        }
        return $this->not !== $in ? null : [$this->message, []];
    }
}
