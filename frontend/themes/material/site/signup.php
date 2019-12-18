<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
$this->title = Yii::$app->sys->event_name. ' Signup';
?>
<div class="site-signup">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
    <p class="text-primary">Please fill out the following fields to signup</p>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?=$form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?=$form->field($model, 'email') ?>

                <?=$form->field($model, 'password')->passwordInput() ?>
                <?=$form->field($model, 'terms_and_conditions')->checkbox([])->hint('')->label($model->attributeLabels()['terms_and_conditions']) ?>
                <?=$form->field($model, 'gdpr')->checkbox(['label'=>$model->attributeLabels()['gdpr']])->hint('') ?>


                <?php echo $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname(), ['options'=>['placeholder'=>'enter captcha code']])->label(false) ?>

                <div class="form-group">
                    <?=Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
  </div>
</div>
