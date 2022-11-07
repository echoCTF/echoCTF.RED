<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Account Email Verification');
?>
<div class="site-verify-email">
    <h2><?=Html::encode($this->title) ?></h2>

    <p><?=\Yii::t('app',"Hi there, we're glad you made it this far, click on the button to activate your account.")?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form=ActiveForm::begin(['id' => 'verify-email-request']);?>

                <?=$form->field($model, 'token')->textInput()->hiddenInput(['value'=>Html::encode($token)])->label(false);?>

                <div class="form-group">
                    <?=Html::submitButton(\Yii::t('app','Activate'), ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end();?>
        </div>
    </div>
</div>
