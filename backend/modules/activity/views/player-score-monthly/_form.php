<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScoreMonthly */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-score-monthly-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->widget(app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that this SSL will belong to.');  ?>

    <?= $form->field($model, 'points')->textInput() ?>

    <?= $form->field($model, 'dated_at')->textInput() ?>

    <?php if(!$model->isNewRecord):?>
    <p><b>Last update:</b> <?= $model->ts?></p>
    <?php endif;?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
