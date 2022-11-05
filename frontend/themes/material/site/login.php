<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Login');
?>
<div class="site-login">
  <div class="body-content">
    <h2><?= Html::encode($this->title) ?></h2>

    <p class="text-primary"><?=\Yii::t('app','Please fill out the following fields to login:')?></p>

    <div class="row">
        <div class="col-lg-5">
          <?php $form=ActiveForm::begin(['id' => 'login-form']);?>
          <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'autocomplete'=>'off'])->label(\Yii::t('app',"Username or Email")) ?>
          <?= $form->field($model, 'password')->passwordInput() ?>
          <?= $form->field($model, 'rememberMe')->checkbox() ?>

          <p class="small"><?=\Yii::t('app','If you cant login, you can always request a {passwordResetLink} or a resend of the {verificationResendLink}',['passwordResetLink'=>Html::a(\Yii::t('app','password reset'), ['/site/request-password-reset']),
          'verificationResendLink'=>Html::a(\Yii::t('app','verification email'), ['/site/resend-verification-email'])])?></p>
          <div class="form-group">
            <?=Html::submitButton(\Yii::t('app','Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
          </div>
          <?php ActiveForm::end();?>
        </div>
    </div>
  </div>
</div>
