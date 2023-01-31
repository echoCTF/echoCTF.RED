<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Network */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="network-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'codename')->textInput(['maxlength' => true])->hint('A short name that is used to create the firewall tables for this network') ?>
        </div>
    </div>
    <div class="row">
    <div class="col-md-6">
    <?= $form->field($model, 'icon')->textInput(['maxlength' => true])->hint('The URL for the icon to be used for the network') ?>
    </div>
    <div class="col-md-6">
    <?= $form->field($model, 'weight')->textInput(['maxlength' => true])->hint('Set the weight of the network to determine ordering. Larger values place the network towards the end.') ?>
    </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'public')->checkbox()->hint('Whether the network will be accessible by all user VPN users') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'guest')->checkbox()->hint('Whether Guest users can see this network on the frontend') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'active')->checkbox()->hint('Whether the network is active') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'announce')->checkbox()->hint("Whether announcements for additions and removals of targets should take place for this network") ?>
        </div>
    </div>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>