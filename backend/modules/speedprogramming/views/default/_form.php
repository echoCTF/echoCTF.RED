<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\speedprogramming\models\SpeedProblem;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\models\SpeedSolution */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="speed-solution-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
      <div class="col-md-6">
      <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
                            'multiple' => false,
                            'url' => ['/frontend/player/ajax-search'],
                            'options' => ['placeholder' => 'Find player by email, username, id or profile.']
                          ])->Label('Player')->hint('Choose the player that will own this solution') ?>
      </div>
      <div class="col-md-6">
      <?= $form->field($model, 'problem_id')->dropDownList(ArrayHelper::map(SpeedProblem::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Problem'])->Label('Problem')->hint('Problem that this solution belongs.')?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4"><?= $form->field($model, 'status')->dropDownList($model->statuses, ['prompt'=>'Status'])->Label('Status')->hint('Select submission status')?></div>
      <div class="col-md-4"><?= $form->field($model, 'language')->dropDownList($model->languages, ['prompt'=>'Language'])->Label('Language')->hint('Select submission language')?></div>
      <div class="col-md-4"><?= $form->field($model, 'points')->textInput() ?></div>
    </div>

    <?= $form->field($model, 'sourcecode')->textarea(['rows'=>10]) ?>




    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
