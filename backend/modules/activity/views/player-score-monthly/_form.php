<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScoreMonthly */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-score-monthly-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'points')->textInput() ?>

    <?= $form->field($model, 'dated_at')->textInput() ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
