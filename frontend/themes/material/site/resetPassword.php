<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Reset password');
?>
<div class="site-reset-password">
  <div class="body-content">
    <h2><?=Html::encode($this->title) ?></h2>

    <p><?=\Yii::t('app','Please choose your new password:')?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form=ActiveForm::begin(['id' => 'reset-password-form']);?>

                <?=$form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?=Html::submitButton(\Yii::t('app','Save'), ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end();?>
        </div>
    </div>
  </div>
</div>
