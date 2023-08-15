<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Finding */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="finding-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('The name of the finding (will not show in public places)') ?>

    <?= $form->field($model, 'pubname')->textInput(['maxlength' => true])->hint('The name of the finding which will show in public places like streams') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('A short description (not to be shown in public places)') ?>

    <?= $form->field($model, 'pubdescription')->textarea(['rows' => 6])->hint('A short description which will show in public places ') ?>

    <?= $form->field($model, 'points')->textInput(['maxlength' => true])->hint('The amount of points you want to award when a player discovers this finding') ?>

    <?= $form->field($model, 'stock')->textInput()->hint('The maximum amount of times this finding can be awarded before it is depleted ( -1 for unlimited)') ?>

    <?= $form->field($model, 'protocol')->dropDownList(['icmp' => 'ICMP', 'tcp' => 'TCP', 'udp' => 'UDP', ], ['prompt' => 'Choose the protocol of the finding']) ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['fqdn'=>SORT_ASC])->all(), 'id', function($model) {
        return sprintf("%s/%s", $model['fqdn'], $model['ipoctet']);}), ['prompt'=>'Select the target'])->Label('Target') ?>

    <?= $form->field($model, 'port')->textInput()->hint('The port where the service listens to (use 0 for icmp)') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
