<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerBandwidth $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="player-bandwidth-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'vpn_local_address')->textInput() ?>

    <?= $form->field($model, 'bytes_received')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bytes_sent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'duration')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
