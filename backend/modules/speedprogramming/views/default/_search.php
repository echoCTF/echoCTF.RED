<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SpeedSolutionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="speed-solution-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'player_id') ?>

    <?= $form->field($model, 'problem_id') ?>

    <?= $form->field($model, 'language') ?>

    <?php // echo $form->field($model, 'sourcecode') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'points') ?>

    <?php // echo $form->field($model, 'modcomments') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
