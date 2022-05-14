<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\OpenvpnSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="openvpn-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'provider_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'net') ?>

    <?= $form->field($model, 'mask') ?>

    <?php // echo $form->field($model, 'management_ip') ?>

    <?php // echo $form->field($model, 'management_port') ?>

    <?php // echo $form->field($model, 'management_passwd') ?>

    <?php // echo $form->field($model, 'conf') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
