<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
$this->title=\Yii::$app->sys->event_name.' '.\Yii::t('app','Signup');
$this->registerJsFile("@web/js/plugins/jquery.pwstrength.js", [
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);

?>
<div class="site-signup">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
    <p class="text-primary"><?=\Yii::t('app','Please fill out the following fields to register for an <code style="color: red">{event_name}</code> account',['event_name'=>Html::encode(\Yii::$app->sys->event_name)])?></p>
    <p class="text-warning"><?=\Yii::t('app','All our email communications come from the following address:')?> <small><code class="text-warning"><?=\app\widgets\Obfuscator::widget(['email' => Html::encode(Yii::$app->sys->mail_from)])?></code></small></p>
    <div class="row">
        <?=$this->render('_referrer_card',['referred'=>$referred]);?>
        <div class="col-lg-5">
            <?php $form=ActiveForm::begin(['id' => 'form-signup']);?>

                <?=$form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?=$form->field($model, 'email')->hint('<small class="text-warning">'.\Yii::t('app','We will email instructions to activate your account once registered.').'</small>') ?>

                <?=$form->field($model, 'password')->passwordInput() ?>

                <?php echo $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::class, ['options'=>['placeholder'=>'enter captcha code','autocomplete'=>'off']])->label(false)->hint('<small class="text-warning">You can click on the image to load a new captcha code.</small>') ?>

                <p><small><?=\Yii::t('app','By signing up you accept the {event_name} <b><a href="/terms_and_conditions" target="_blank">Terms and Conditions</a></b> and <b><a href="/privacy_policy" target="_blank">Privacy Policy</a></b>.',['event_name'=>\Yii::$app->sys->{"event_name"}])?></small></p>
                <div class="form-group">
                    <?=Html::submitButton(\Yii::t('app','Signup'), ['class' => 'btn btn-primary text-dark text-bold', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end();?>
        </div>
    </div>
  </div>
</div>
