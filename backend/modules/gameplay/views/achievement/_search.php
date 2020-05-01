<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\AchievementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="achievement-search">

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

    <?php // echo $form->field($model, 'player_type') ?>

    <?php // echo $form->field($model, 'appears') ?>

    <?php // echo $form->field($model, 'effects') ?>

    <?php // echo $form->field($model, 'code') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
