<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\HintSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hint-search">

    <?php $form=ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'player_type') ?>

    <?= $form->field($model, 'message') ?>

    <?= $form->field($model, 'category') ?>

    <?php // echo $form->field($model, 'badge_id') ?>

    <?php // echo $form->field($model, 'finding_id') ?>

    <?php // echo $form->field($model, 'treasure_id') ?>

    <?php // echo $form->field($model, 'question_id') ?>

    <?php // echo $form->field($model, 'points_user') ?>

    <?php // echo $form->field($model, 'points_team') ?>

    <?php // echo $form->field($model, 'timeafter') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'ts') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
