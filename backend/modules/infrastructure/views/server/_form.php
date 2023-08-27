<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\Server */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="server-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ipoctet')->textInput()->hint('The IP address of the target')->label('IP Address') ?>
    <?= $form->field($model, 'provider_id')->textInput()->hint('Machine provider unique identifier.')->label('Provider ID') ?>

    <?= $form->field($model, 'network')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'service')->dropDownList([ 'docker' => 'Docker', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'connstr')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'ssl')->checkBox() ?>
    <?= $form->field($model, 'timeout')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
