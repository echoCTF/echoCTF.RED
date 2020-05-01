<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Challenge;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Question */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="question-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'challenge_id')->dropDownList(ArrayHelper::map(Challenge::find()->all(), 'id', 'name'),
            ['prompt'=>'Select Challenge'])->Label('Challenge')->hint('The challenge this question is part of') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('The name/title of the question') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('The actual question in full detail') ?>

    <?= $form->field($model, 'points')->textInput(['maxlength' => true])->hint('The points you want to award for answering this question') ?>

    <?= $form->field($model, 'player_type')->dropDownList(['offense' => 'Offense', 'defense' => 'Defense', ], ['prompt' => 'Choose player type']) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true])->hint('The answer to the question in the form of a treasure/flag to be claimed (the format should be described on the description of the question or the challenge e.g. "C#1Q#2 answer")') ?>

    <?= $form->field($model, 'weight')->textInput()->hint('Define ordering of the displayed questions by weight in asceding order (default to 0 for all questions)') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
