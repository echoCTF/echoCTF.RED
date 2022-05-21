<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerSslSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-ssl-search">

    <?php $form=ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);?>

    <?= $form->field($model, 'player_id') ?>

    <?= $form->field($model, 'subject') ?>

    <?= $form->field($model, 'csr') ?>

    <?= $form->field($model, 'crt') ?>

    <?php // echo $form->field($model, 'privkey') ?>

    <?php // echo $form->field($model, 'ts') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
