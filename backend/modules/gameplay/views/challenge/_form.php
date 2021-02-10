<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Challenge */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="challenge-form">

    <?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('The name of the challenge') ?>

    <?= $form->field($model, 'active')->checkbox()->hint('Is challenge active?') ?>
    <?= $form->field($model, 'public')->checkbox()->hint('Is challenge public?') ?>
    <?= $form->field($model, 'timer')->checkbox()->hint('Timer for solving the challenge?') ?>

    <?= $form->field($model, 'icon')->textInput(['maxlength' => true])->hint('Challenge icon (<code>raw html</code>)') ?>

    <?= $form->field($model, 'category')->textInput(['maxlength' => true])->hint('The category this chellange belongs to TODO') ?>

    <?= $form->field($model, 'difficulty')->textInput(['maxlength' => true])->hint('The difficulty of the challenge (can be easy, medium, hard or very hard) TODO') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('The full description/scenario of the challenge (do not add any questions here, they should be created on "Gameplay"->"Challenge Questions")') ?>

    <?= $form->field($model, 'player_type')->dropDownList(['offense' => 'Offense', 'defense' => 'Defense', ], ['prompt' => ''])->hint('Whether this challenge will be available to offense or defense players/teams') ?>

    <?= $form->field($model, 'filename')->textInput()->hint('What name to show to the player for download') ?>

    <?= $form->field($model, 'file')->fileInput()->hint('Upload the challenge file') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
