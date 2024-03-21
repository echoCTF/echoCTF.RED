<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Openvpn */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="openvpn-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'provider_id')->textInput(['maxlength' => true,'placeholder'=>'vpn01-eu01.example.com'])->hint("An string to help you distinguish the server instance that this entry refers to (eg. vpn01-eu01.example.com)") ?>

    <?= $form->field($model, 'server')->textInput(['maxlength' => true,'placeholder'=>'vpn.example.com'])->hint("The server name for this entry)") ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder'=>'openvpn_tun0.conf'])->hint("The configuration file name for this instance of openvpn (eg. server_tun0.conf)") ?>

    <?= $form->field($model, 'net_octet')->textInput(['maxlength' => true,'placeholder'=>'10.10.0.0'])->hint("Network address that this OpenVPN instance serves. This must reflect your <kbd>server</kbd> block ie <code>server 10.10.0.0 255.255.0.0</code>") ?>

    <?= $form->field($model, 'mask_octet')->textInput(['maxlength' => true,'placeholder'=>'255.255.0.0'])->hint("The mask for the network (eg 255.255.0.0)") ?>

    <?= $form->field($model, 'management_ip_octet')->textInput(['maxlength' => true,'placeholder'=>'127.0.0.1'])->hint("IP address this OpenVPN instance will listen for management connections (eg 127.0.0.1). This refers to your <kbd>management</kbd> block eg <code>management 127.0.0.1 11195</code>") ?>

    <?= $form->field($model, 'management_port')->textInput(['maxlength' => true,'placeholder'=>'11198'])->hint("The port it listens to") ?>

    <?= $form->field($model, 'management_passwd')->textInput(['maxlength' => true,'placeholder'=>'vpnpass'])->hint("Password required to connect (if any)") ?>

    <?= $form->field($model, 'status_log')->textInput(['maxlength' => true,'placeholder'=>'/var/log/openvpn-status.log'])->hint("The location of the special OpenVPN status log file") ?>

    <?= $form->field($model, 'conf')->textarea(['rows' => 6,"class"=>"form-control",'style'=>'font-family: monospace'])->hint("Paste the configuration file contents.") ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
