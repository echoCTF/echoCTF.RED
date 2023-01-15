<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'active')->checkbox() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'shortcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'livemode')->checkbox() ?>
    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'metadata')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'htmlOptions')->textarea(['rows' => 1,'placeholder' => '{"title":"Pro","class":"green"}'])->hint('Enter htmlOptions in json format eg. <code>{"title":"Pro","class":"green"}</code>') ?>
    <?= $form->field($model, 'perks')->textarea(['rows' => 8]) ?>
    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
