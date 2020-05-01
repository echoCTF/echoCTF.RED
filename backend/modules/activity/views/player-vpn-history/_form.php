<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerVpnHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-vpn-history-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'vpn_remote_address')->textInput() ?>

    <?= $form->field($model, 'vpn_local_address')->textInput() ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
