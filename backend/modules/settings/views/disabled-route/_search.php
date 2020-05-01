<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\DisabledRouteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="disabled-route-search">

    <?php $form=ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);?>

    <?= $form->field($model, 'route') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
