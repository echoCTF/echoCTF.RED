<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Arpdat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="arpdat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ipoctet')->textInput()->hint('The IP address associated with the MAC address chosen below. TODO') ?>

    <?= $form->field($model, 'mac')->textInput(['maxlength' => true])->hint('The MAC address associated with the IP address chosen above. TODO') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
