<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-search">

    <?php $form=ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'fqdn') ?>

    <?= $form->field($model, 'purpose') ?>

    <?= $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'mac') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'net') ?>

    <?php // echo $form->field($model, 'server') ?>

    <?php // echo $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'dns') ?>

    <?php // echo $form->field($model, 'parameters') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
