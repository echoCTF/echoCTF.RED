<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::$app->sys->event_name.' Request password reset';
?>
<div class="site-request-password-reset">
  <div class="body-content">
    <h2><?=Html::encode($this->title) ?></h2>

    <p>Please fill out your email. A link to reset your password will be sent.</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?=$form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?=Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
  </div>
</div>
