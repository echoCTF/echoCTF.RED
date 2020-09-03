<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\ChallengeSolver */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="challenge-solver-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'challenge_id')->textInput() ?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'timer')->textInput() ?>

    <?= $form->field($model, 'rating')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
