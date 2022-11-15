<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\TargetPlayerState */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-player-state-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'player_treasures')->textInput() ?>

    <?= $form->field($model, 'player_findings')->textInput() ?>

    <?= $form->field($model, 'player_points')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
