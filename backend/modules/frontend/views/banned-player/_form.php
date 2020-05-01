<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\BannedPlayer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="banned-player-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'old_id')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'registered_at')->textInput() ?>

    <?= $form->field($model, 'banned_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
