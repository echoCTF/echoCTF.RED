<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetVariable */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-variable-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->all(), 'id', function($model) {
        return sprintf("(id:%d) %s/%s", $model['id'], $model['fqdn'], $model['ipoctet']);}), ['prompt'=>'Select the target'])->Label('Target') ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true])->hint('The name of the environment variable for the target chosen above') ?>

    <?= $form->field($model, 'val')->textInput(['maxlength' => true])->hint('The desired value of the variable above') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
