<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\models\PlayerSubscription */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-subscription-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'stripe_customer_id')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
