<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Player */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true])->hint('The username of the player') ?>

    <?= $form->field($model, 'academic')->checkbox()->hint('Whether the player is academic or not') ?>

    <?= $form->field($model, 'fullname')->textInput(['maxlength' => true])->hint('The fullname of the player') ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true])->hint('The email address of the player') ?>

    <?= $form->field($model, 'type')->dropDownList(['offense' => 'Offense', 'defense' => 'Defense', ], ['prompt' => 'Choose player type'])->hint('Choose the type of the player. Either offense or defense') ?>

    <?= $form->field($model, 'new_password')->textInput(['maxlength' => true])->hint('Choose a password for the player') ?>

    <?= $form->field($model, 'activkey')->textInput(['maxlength' => true])->hint('The activation key, generated automatically by the application. TODO') ?>

    <?= $form->field($model, 'active')->checkbox()->hint('Whether the player is active or not') ?>

    <?= $form->field($model, 'status')->dropDownList(['0' => 'Disabled', '9' => 'Banned', '10' => 'Enabled'], ['prompt' => 'Choose player status'])->hint('Account status') ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
