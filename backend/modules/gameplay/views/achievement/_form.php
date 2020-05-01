<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Achievement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="achievement-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('The name of the achievement') ?>

    <?= $form->field($model, 'pubname')->textInput(['maxlength' => true])->hint('The name of the achievement to be shown on public locations (e.g. streams)') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('A description of the scenario of the achievement and what players have to do') ?>

    <?= $form->field($model, 'pubdescription')->textarea(['rows' => 6])->hint('A description of the scenario of the achievement without leaking any information like target hostnames, IPs, services, ports etc.') ?>

    <?= $form->field($model, 'points')->textInput(['maxlength' => true])->hint('The amount of points to be awarded when a player/team completes this achievement') ?>

    <?= $form->field($model, 'player_type')->dropDownList(['offense' => 'Offense', 'defense' => 'Defense', ], ['prompt' => ''])->hint('The type of the player/team') ?>

    <?= $form->field($model, 'appears')->textInput()->hint('TODO') ?>

    <?= $form->field($model, 'effects')->dropDownList(['users_id' => 'Users', 'team' => 'Team', 'total' => 'Total', ], ['prompt' => ''])->hint('Whether this achievement is only for users, only for teams or both') ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true])->hint('The code/flag players/teams must provide in order to claim this achievement') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
