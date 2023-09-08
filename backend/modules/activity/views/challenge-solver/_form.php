<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Challenge;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\ChallengeSolver */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="challenge-solver-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'challenge_id')->dropDownList(ArrayHelper::map(Challenge::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', function($model){return sprintf("%s (ID: %d)",$model->name, $model->id);}), ['prompt'=>'Select Challenge'])->hint('The challenge to solve.') ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player id that the solve will be given.');  ?>

    <?= $form->field($model, 'timer')->textInput() ?>

    <?= $form->field($model, 'rating')->textInput() ?>
    <?= $form->field($model, 'first')->textInput() ?>

    <div class="form-group">
      <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'value'=>'save', 'name'=>'submit[]','id'=>'saveBtn']) ?>
      <?= Html::submitButton(Yii::t('app', 'Give'), ['class' => 'btn btn-primary', 'value'=>'give', 'name'=>'submit[]', 'id'=>'giveBtn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
