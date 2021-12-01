<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerLast */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-last-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'on_pui')->textInput() ?>

    <?= $form->field($model, 'on_vpn')->textInput() ?>

    <?= $form->field($model, 'vpn_remote_address_octet')->textInput() ?>

    <?= $form->field($model, 'vpn_local_address_octet')->textInput() ?>

    <?= $form->field($model, 'signin_ipoctet')->textInput() ?>

    <?= $form->field($model, 'signup_ipoctet')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
