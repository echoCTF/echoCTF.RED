<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="notification-form">

  <?php $form = ActiveForm::begin(['id' => 'notifyForm-' . uniqid()]); ?>
  <div class="row">
    <div class="col-md-6">
      <?= $form->field($notificationModel, 'category')->dropDownList(\app\modules\activity\models\Notification::supportedCategories())->hint('Choose the notification type. <code>swal:</code> prefixed notifications invoke a modal popup.') ?>
    </div>
    <div class="col-md-3">
      <?= $form->field($notificationModel, 'online')->checkBox(['label' => false,'uncheck' => null])->label('Online')->hint('Notify online players only') ?>
    </div>
    <div class="col-md-3">
      <?= $form->field($notificationModel, 'ovpn')->checkBox(['label' => false,'uncheck' => null])->label('On VPN')->hint('Notify only players on the VPN') ?>
    </div>
  </div>
  <?= $form->field($notificationModel, 'title')->textInput(['maxlength' => true]) ?>

  <?= $form->field($notificationModel, 'body')->textarea(['rows' => 6]) ?>


  <div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
$(document).on('beforeSubmit', '#{$form->id}', function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        success: function (data) {
            prom=new Promise(r => setTimeout(r, 1000));
            $('#notifyModal').modal('hide');
            location.reload();

        },
        error: function () {
          $('#notifyModal').modal('hide');
          location.reload();
        }
    });
    return false;
});
JS;
$this->registerJs($js);
