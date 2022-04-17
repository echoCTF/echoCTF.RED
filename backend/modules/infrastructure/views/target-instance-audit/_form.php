<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetInstanceAudit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-instance-audit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'op')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'target_id')->textInput() ?>

    <?= $form->field($model, 'server_id')->textInput() ?>

    <?= $form->field($model, 'ip')->textInput() ?>

    <?= $form->field($model, 'reboot')->textInput() ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
