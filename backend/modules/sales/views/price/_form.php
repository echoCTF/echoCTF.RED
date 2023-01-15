<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\models\Price */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'active')->checkbox() ?>
    <?= $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'metadata')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'recurring_interval')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'interval_count')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'unit_amount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
