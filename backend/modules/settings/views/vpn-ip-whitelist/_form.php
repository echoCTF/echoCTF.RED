<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\VpnIpWhitelist */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vpn-ip-whitelist-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'network')->textInput(['maxlength' => true])->hint('An IP address, IP/prefix (eg 203.0.113.0/24) or IP/netmask (eg 203.0.113.0/255.255.255.0)') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
