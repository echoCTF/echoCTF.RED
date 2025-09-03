<?php

namespace app\components\columns;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Modal;
use yii\web\JsExpression;

class ActionColumn extends \yii\grid\ActionColumn
{
  protected static $modalRendered = false;

  public $contentOptions = ['class' => 'text-center', 'style' => 'white-space: nowrap;'];
  public $headerOptions = ['class' => 'action-column', 'style' => 'text-align: center;'];
  public $notifyIdValue;

  protected function initDefaultButtons()
  {
    parent::initDefaultButtons();

    // Custom button
    $this->buttons['notify'] = function ($url, $model, $key) {
      return Html::a('<i class="fas fa-paper-plane"></i>', $url, [
        'title' => 'Send a Notification',
        'class' => 'view-notify-btn',
        'data-bs-toggle' => 'modal',
        'data-bs-target' => '#notifyModal',
        'data-url' => Url::to($url),
      ]);
    };
  }

  public function createUrl($action, $model, $key, $index)
  {
    if ($action === 'notify') {
      if (isset($this->urlCreator) && is_callable($this->urlCreator)) {
        return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
      }

      if (is_callable($this->notifyIdValue)) {
        $id = call_user_func($this->notifyIdValue, $model, $key, $index);
      } else {
        $id = $this->notifyIdValue ?? $model->id;
      }

      return ['notify', 'id' => $id];
    }

    return parent::createUrl($action, $model, $key, $index);
  }

  protected function renderDataCellContent($model, $key, $index)
  {
    $content = parent::renderDataCellContent($model, $key, $index);
    if (!self::$modalRendered) {
      \yii\bootstrap5\Modal::begin([
        'id' => 'notifyModal',
        'title' => '<h3>Send notification for ' . str_replace(['-', '_'], ' ', \Yii::$app->controller->id) . '</h3>',
        'size' => \yii\bootstrap5\Modal::SIZE_LARGE,
      ]);
      echo '<div id="notifyModalContent"></div>';
      \yii\bootstrap5\Modal::end();

      $js = <<<JS
var notifyModal = new bootstrap.Modal(document.getElementById('notifyModal'));
$(document).on('click', '.view-notify-btn', function(e){
    e.preventDefault();
    var url = $(this).data('url');
    $('#notifyModalContent').html('<div class="text-center">Loading...</div>');
    $.get(url, function(data){
        $('#notifyModalContent').html(data);
    });

    notifyModal.show();
});
JS;
      $this->grid->view->registerJs(new \yii\web\JsExpression($js));

      self::$modalRendered = true;
    }
    return $content;
  }
}
