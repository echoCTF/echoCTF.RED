<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Sysconfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sysconfig-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true])->hint('The name of the sysconfig key')  ?>

    <?= $form->field($model, 'val')->textarea()->hint('The desired value of the sysconfig key') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
