<?php
namespace app\widgets;

use yii\jui\AutoComplete;
use yii\helpers\Url;
use yii\web\JsExpression;

class JumpToWidget extends \yii\base\Widget
{
    /** @var string Input name */
    public $name = 'jumpto';

    /** @var string Placeholder text */
    public $placeholder = '🔍 Jump to...';

    /** @var string|array The AJAX source endpoint (controller/action or URL) */
    public $sourceUrl;

    /** @var string|array The redirect route (controller/action or URL) */
    public $redirectUrl;

    /** @var string The key returned by AJAX to extract the value from (default = 'id') */
    public $idField = 'id';

    /** @var string The query parameter name used in the redirect (default = 'id') */
    public $paramName = 'id';

    /** @var int Minimum characters before search triggers */
    public $minLength = 2;

    public function run()
    {
        if (!$this->sourceUrl || !$this->redirectUrl) {
            throw new \yii\base\InvalidConfigException("Both 'sourceUrl' and 'redirectUrl' must be set.");
        }

        $redirectBase = Url::to($this->redirectUrl);

        return AutoComplete::widget([
            'name' => $this->name,
            'options' => [
                'class' => 'form-control-sm rounded-pill shadow-sm border-primary',
                'placeholder' => $this->placeholder,
            ],
            'clientOptions' => [
                'source' => Url::to($this->sourceUrl),
                'minLength' => $this->minLength,
                'select' => new JsExpression("
                    function(event, ui) {
                        window.location.href = '{$redirectBase}&{$this->paramName}=' + ui.item.{$this->idField};
                    }
                "),
            ],
        ]);
    }
}
