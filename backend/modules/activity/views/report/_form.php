<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Report */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-form">

    <?php $form=ActiveForm::begin();?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true])->hint('Filled in by the player: The title of the Report') ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that the report will belong to.');  ?>

    <?= $form->field($model, 'body')->textarea(['rows' => 6])->hint('Filled in by the player: The body of the report, including full details') ?>

    <?= $form->field($model, 'status')->dropDownList(['pending' => 'Pending', 'invalid' => 'Invalid', 'approved' => 'Approved', ], ['prompt' => ''])->hint('You may either consider a pending report invalid or approve it which will grant the player with the point you fill in below') ?>

    <?= $form->field($model, 'points')->textInput()->hint('Moderators must fillin the points to be awarded to the player/team for this report') ?>

    <?= $form->field($model, 'modcomment')->textarea(['rows' => 6])->hint('Moderators may add any kind of comment here (e.g. Your report is considered invalid, because .... Please submit a better report.)') ?>

    <?= $form->field($model, 'pubtitle')->textInput(['maxlength' => true])->hint('A message to be shown on the public stream when this report is approved (e.g. has reported a 0day vulnerabiity on a modbus service)') ?>

    <?= $form->field($model, 'pubbody')->textarea(['rows' => 6])->hint('A detailed description of the report to be displayed in public spots of the pUI') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
