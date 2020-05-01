<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\QuestionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="question-search">

    <?php $form=ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'challenge_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'points') ?>

    <?php // echo $form->field($model, 'player_type') ?>

    <?php // echo $form->field($model, 'code') ?>

    <?php // echo $form->field($model, 'weight') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
