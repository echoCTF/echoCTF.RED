<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerSsl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-ssl-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'subject')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'csr')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'crt')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'txtcrt')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'privkey')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
