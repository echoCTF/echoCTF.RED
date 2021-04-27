<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
$this->title=Yii::$app->sys->event_name.' Signup';
?>
<div class="site-signup">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
    <p class="text-primary">Please fill out the following fields to register for an <code style="color: red"><?=Yii::$app->sys->event_name?></code> account</p>
    <p class="text-warning">All our email communications come from the following address: <small><code class="text-warning"><?=\app\widgets\Obfuscator::widget(['email' => Html::encode(Yii::$app->sys->mail_from)])?></code></small></p>
    <div class="row">
        <div class="col-lg-5">
            <?php $form=ActiveForm::begin(['id' => 'form-signup']);?>

                <?=$form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?=$form->field($model, 'email')->hint('<small class="text-warning">We will email instructions to activate your account once registered.</small>') ?>

                <?=$form->field($model, 'password')->passwordInput() ?>

                <?php echo $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::class, ['options'=>['placeholder'=>'enter captcha code','autocomplete'=>'off']])->label(false) ?>

                <p><small>By signing up you accept the <?=\Yii::$app->sys->{"event_name"}?> <b><a href="/terms_and_conditions" target="_blank">Terms and Conditions</a></b>
                  and <b><a href="/privacy_policy" target="_blank">Privacy Policy</a></b>.</small>
                </p>
                <div class="form-group">
                    <?=Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end();?>
        </div>
    </div>
  </div>
</div>
