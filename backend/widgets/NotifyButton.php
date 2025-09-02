<?php

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Modal;
use yii\web\JsExpression;

class NotifyButton extends Widget
{
    public $url;         // URL to load via AJAX
    public $label = '<i class="fas fa-paper-plane"></i> Notify';
    public $title = 'Send a Notification';
    public $modalId = 'notifyModal';
    public $modalTitle;  // optional, defaults to title
    public $buttonOptions = ['class' => 'btn btn-purple text-white'];

    public function run()
    {
        $modalId = $this->modalId;
        $modalTitle = $this->modalTitle ?: $this->title;
        $url = Url::to($this->url);

        // Render button
        echo Html::a($this->label, '#', array_merge($this->buttonOptions, [
            'title' => $this->title,
            'class' => isset($this->buttonOptions['class']) ? $this->buttonOptions['class'].' view-notify-btn' : 'view-notify-btn',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => "#$modalId",
            'data-url' => $url,
        ]));

        // Render modal (only once per page)
        static $modalRendered = false;
        if (!$modalRendered) {
            Modal::begin([
                'id' => $modalId,
                'title' => $modalTitle,
                'size' => Modal::SIZE_LARGE,
            ]);
            echo '<div id="'.$modalId.'Content"></div>';
            Modal::end();

            // JS
            $js = <<<JS
var notifyModal = new bootstrap.Modal(document.getElementById('$modalId'));
$(document).on('click', '.view-notify-btn', function(e){
    e.preventDefault();
    var url = $(this).data('url');
    $('#{$modalId}Content').html('<div class="text-center">Loading...</div>');
    $.get(url, function(data){
        $('#{$modalId}Content').html(data);
    });
    notifyModal.show();
});
JS;
            $this->view->registerJs(new JsExpression($js));
            $modalRendered = true;
        }
    }
}
