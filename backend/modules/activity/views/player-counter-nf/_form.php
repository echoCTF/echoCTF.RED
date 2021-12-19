<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerCounterNf */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-counter-nf-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'metric')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'counter')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
