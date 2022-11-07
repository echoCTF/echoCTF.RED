<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Resend verification email');
?>
<div class="site-resend-verification-email">
  <div class="body-content">
    <h2><?=Html::encode($this->title) ?></h2>

    <p><?=\Yii::t('app','Please fill out your email. A verification email will be sent there.')?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form=ActiveForm::begin(['id' => 'resend-verification-email-form']);?>

            <?=$form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <div class="form-group">
                <?=Html::submitButton(\Yii::t('app','Send'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end();?>
        </div>
    </div>
  </div>
</div>
