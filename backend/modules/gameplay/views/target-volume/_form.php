<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetVolume */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-volume-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->all(), 'id', function($model) {
        return sprintf("(id:%d) %s/%s", $model['id'], $model['fqdn'], $model['ipoctet']);}), ['prompt'=>'Select the target'])->Label('Target') ?>

    <?= $form->field($model, 'volume')->textInput(['maxlength' => true])->hint('The host server directory you want to make available to the target(docker)') ?>

    <?= $form->field($model, 'bind')->textInput(['maxlength' => true])->hint('The directory, within the target(docker), the volume above will be available to') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
