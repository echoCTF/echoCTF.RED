<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Sysconfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sysconfig-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'val')->textarea(['rows' => '16','style'=>"font-family:monospace;"])->hint('Enter your desired html code to be displayed on the frontpage for guest visitors.') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
