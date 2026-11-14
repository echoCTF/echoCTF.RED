<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\models\PlayerSubscription */
/* @var $form yii\widgets\ActiveForm */

\yii\jui\JuiAsset::register($this);
// pick the date only, the time part of the value is kept as is
$this->registerJs(<<<JS
$('.date-picker').datepicker({
  dateFormat: 'yy-mm-dd',
  beforeShow: function (input) {
    $(input).data('time', input.value.split(' ')[1] || '00:00:00');
    return { defaultDate: input.value.split(' ')[0] || null };
  },
  onSelect: function (date) {
    this.value = date + ' ' + $(this).data('time');
  }
});
JS);
?>

<div class="player-subscription-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ]) ?>

    <div class="row">
      <div class="col-md-4"><?= $form->field($model, 'subscription_id')->textInput(['maxlength' => true]) ?></div>
      <div class="col-md-4"><?= $form->field($model, 'session_id')->textInput(['maxlength' => true]) ?></div>
      <div class="col-md-4"><?= $form->field($model, 'price_id')->textInput(['maxlength' => true]) ?></div>
    </div>

    <?= $form->field($model, 'active')->checkbox() ?>

    <div class="row">
      <div class="col-md-3"><?= $form->field($model, 'starting')->textInput(['class' => 'form-control date-picker']) ?></div>
      <div class="col-md-3"><?= $form->field($model, 'ending')->textInput(['class' => 'form-control date-picker']) ?></div>
      <div class="col-md-3"><?= $form->field($model, 'created_at')->textInput(['class' => 'form-control date-picker']) ?></div>
      <div class="col-md-3"><?= $form->field($model, 'updated_at')->textInput(['class' => 'form-control date-picker']) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
