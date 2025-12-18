<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $notificationModel app\modules\activity\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

  <?php $form = ActiveForm::begin(['id' => 'notifyForm-' . uniqid()]); ?>
  <div class="row">
    <div class="col-md-6">
      <?= $form->field($notificationModel, 'category')->dropDownList(\app\modules\activity\models\Notification::supportedCategories())->hint('Choose the notification type. <code>swal:</code> prefixed notifications invoke a modal popup.') ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <?= $form->field(new \yii\base\DynamicModel(['owner' => false]), 'owner')
        ->checkbox(['label' => false])
        ->label('Owner')
        ->hint('Notify owner only?');
      ?>

    </div>

    <div class="col-md-4">
      <?= $form->field($notificationModel, 'online')->checkBox(['label' => false])->label('Online')->hint('Notify online players only') ?>
    </div>
    <div class="col-md-4">
      <?= $form->field($notificationModel, 'ovpn')->checkBox(['label' => false])->label('On VPN')->hint('Notify only players on the VPN') ?>
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
$('#{$form->id}').on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        success: function (data) {
          if (data.status === 'success') {
            $('#notify-team-modal').modal('hide');
            location.reload();
          }
        },
        error: function () {
        }
    });
    return false;
});
JS;
$this->registerJs($js);
