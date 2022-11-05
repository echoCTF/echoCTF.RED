<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title = 'Contact';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
  <section class="mb-4">
    <!--Section heading-->
    <h2 class="h1-responsive font-weight-bold text-center my-4"><?=\Yii::t('app','Get in touch')?></h2>
    <!--Section description-->
    <p class="text-center w-responsive mx-auto mb-5"><?=\Yii::t('app','Interested in running your own CTF, hosting a cyber security exercise or provide hands on training to your engineers? Then you are on the right place, we have delivered numerous successful events throughout the years. Let us do the same for you!')?></p>
    <div class="row">
      <div class="col-sm-6">
        <!--Grid column-->
        <div class="col-md-12 mb-md-0 mb-5">
        <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
          <div class="row">
            <div class="col-md-6">
              <?= $form->field($model, 'name')->textInput(['placeholder'=>Html::encode(Yii::$app->user->identity->fullname)]) ?>
            </div>

            <div class="col-md-6">
              <?= $form->field($model, 'email')->textInput(['placeholder'=>Html::encode(Yii::$app->user->identity->email)]) ?>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <?= $form->field($model, 'subject')->textInput(['placeholder'=>Html::encode('Run a CTF for me')]) ?>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <?= $form->field($model, 'body')->textArea(['rows' => 6,'placeholder'=>Html::encode('Hi, can you help me run a CTF for my team?')]) ?>
            </div>
          </div>
          <div class="form-group">
              <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
          </div>
          <div class="status"></div>
        <?php ActiveForm::end(); ?>
        </div>
      </div>
      <div class="col-sm-6">
      </div>
    </div>
  </section>
</div>
