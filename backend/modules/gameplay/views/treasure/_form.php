<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Treasure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="treasure-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['name'=>SORT_ASC])->all(), 'id', function($model) {
        return sprintf("%s/%s", $model['fqdn'], $model['ipoctet']);}), ['prompt'=>'Select the target'])->Label('Target') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('A name for the treasure') ?>

    <?= $form->field($model, 'pubname')->textInput(['maxlength' => true])->hint('A public name for the treasure (will be shown on steams and other public places)') ?>

    <?= $form->field($model, 'category')->textInput(['maxlength' => true])->hint('Add a category for this flag') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('A short description of the treasure') ?>

    <?= $form->field($model, 'pubdescription')->textarea(['rows' => 6])->hint('A short description of the treasure which will be shown on public places') ?>

    <?= $form->field($model, 'points')->textInput(['maxlength' => true])->hint('The amount of points to be awarded to a player which claims this treasure/flag') ?>

    <?= $form->field($model, 'player_type')->dropDownList(['offense' => 'Offense', 'defense' => 'Defense', ], ['prompt' => 'Choose the type of the player']) ?>

    <?= $form->field($model, 'csum')->textInput(['maxlength' => true])->hint('TODO') ?>

    <?= $form->field($model, 'appears')->textInput()->hint('The number of times this treasure may be claimed during the competition ( -1 for unlimited)') ?>

    <?= $form->field($model, 'effects')->dropDownList(['player' => 'Once per player claim', 'team' => 'Once per team claim', 'total' => 'Treasure can only be claimed once', ], ['prompt' => 'Choose how the appearances of this treasure are retracted']) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true])->hint('The actual treasure/flag') ?>
    <?= $form->field($model, 'location')->textInput(['maxlength' => true])->hint('The location where this flag can be found') ?>
    <?= $form->field($model, 'suggestion')->textarea(['rows' => 6])->hint('A suggestion for the user on how to get the flag') ?>
    <?= $form->field($model, 'solution')->textarea(['rows' => 6])->hint('The actual steps required to get the flag (sort of like a write-up)') ?>
    <?= $form->field($model, 'weight')->textInput(['maxlength' => true])->hint('Weight for the treasure to be ordered') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
