<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Team;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\TeamAudit $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="team-audit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'team_id')->dropDownList(ArrayHelper::map(Team::find()->all(), 'id', 'name'), ['prompt'=>'Select team'])->hint('Choose the team to add the player chosen below') ?>
    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
          'multiple' => false,
          'url' => ['/frontend/player/ajax-search'],
          'options' => ['placeholder' => 'Find player by email, username, id or profile.']
        ])->Label('Player')->hint('Choose the player who will appear to produced this entry')?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
