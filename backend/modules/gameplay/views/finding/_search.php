<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\FindingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="finding-search">

    <?php $form=ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'pubname') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'pubdescription') ?>

    <?php // echo $form->field($model, 'points') ?>

    <?php // echo $form->field($model, 'stock') ?>

    <?php // echo $form->field($model, 'protocol') ?>

    <?php // echo $form->field($model, 'target_id') ?>

    <?php // echo $form->field($model, 'port') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
