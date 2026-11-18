<?php

use app\modules\moderation\models\SharedNetwork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\moderation\models\SharedNetwork $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="shared-network-form">

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($model, 'cidr')->textInput([
    'maxlength' => true,
    'placeholder' => 'a.b.c.d/prefix',
    'disabled' => !$model->isNewRecord,
  ])->hint(Yii::t('app', 'IPv4 network, eg. 10.0.0.0/8 or 172.16.0.0/255.240.0.0')) ?>

  <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

  <?php if ($model->isNewRecord): ?>
    <?= $form->field($model, 'absorb')->checkbox()->hint(Yii::t('app', 'Remove existing entries covered by the new network')) ?>
  <?php endif; ?>

  <div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
