<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\models\PlayerSubscription */
/* @var $form yii\widgets\ActiveForm */
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
      <?php foreach (['starting', 'ending', 'created_at', 'updated_at'] as $attr): ?>
      <div class="col-md-3"><?= $form->field($model, $attr)->input('datetime-local', ['step' => 1, 'value' => str_replace(' ', 'T', (string) $model->$attr)]) ?></div>
      <?php endforeach; ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
