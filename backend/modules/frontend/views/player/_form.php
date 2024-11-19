<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Player */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="player-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true,'autocomplete'=>"off"])->hint('The username of the player') ?>

    <?= $form->field($model, 'fullname')->textInput(['maxlength' => true,'autocomplete'=>"off"])->hint('The fullname of the player') ?>

    <?php if (\Yii::$app->sys->academic_grouping !== false): ?>
      <?= $form->field($model, 'academic')->dropDownList((new \app\components\columns\AcademicColumn(['attribute'=>'academic']))->filter)->hint('Academic grouping number for the player (if any)') ?>
    <?php else:?>
      <?= $form->field($model, 'academic')->textInput()->hint('Academic grouping number for the player (if any)') ?>
    <?php endif;?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true,'autocomplete'=>"off"])->hint('The email address of the player') ?>

    <?= $form->field($model, 'type')->dropDownList(['offense' => 'Offense', 'defense' => 'Defense', ], ['prompt' => 'Choose player type'])->hint('Choose the type of the player. Either offense or defense') ?>

    <?= $form->field($model, 'new_password')->textInput(['maxlength' => true,'autocomplete'=>"off"])->hint('Choose a password for the player') ?>

    <?= $form->field($model, 'activkey')->textInput(['maxlength' => true])->hint('The activation key, generated automatically by the application. TODO') ?>

    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true])->hint('The authentication key used for cookie validation. Generated automatically by the application if empty.') ?>

    <?= $form->field($model, 'active')->checkbox()->hint('Whether the player is active or not') ?>

    <?= $form->field($model, 'status')->dropDownList([10 => 'Enabled', 9 => 'Inactive', 8 => "Change", 0 => "Deleted"], ['prompt' => 'Choose player status'])->hint('Account status') ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
