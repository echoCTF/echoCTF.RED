<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::$app->sys->event_name .' Login';
?>
<div class="site-login" style="color: red-100;">
  <div class="body-content">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
          <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
          <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
          <?= $form->field($model, 'password')->passwordInput() ?>
          <?= $form->field($model, 'rememberMe')->checkbox() ?>
          <div class="form-group">
            <?=Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
          </div>
          <?php ActiveForm::end(); ?>
        </div>
    </div>
  </div>
</div>
