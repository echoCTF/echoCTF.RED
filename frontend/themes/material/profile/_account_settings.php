<?php
/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Faq */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Html;
?>
<div class="account-form">
    <?=$form->field($model, 'username')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
		<?=$form->field($model, 'fullname')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
		<?=$form->field($model, 'email')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->hint('<small class="text-danger">Changing your email address will require verification again. Your account will be deactivated in the meantime.</small>')?>
    <div class="row">
      <div class="col-lg-6">
		      <?=$form->field($model, 'new_password')->passwordInput(['autocomplete'=>"new-password"]) ?>
      </div>
      <div class="col-lg-6">
		      <?=$form->field($model, 'confirm_password')->passwordInput(['autocomplete'=>"new-password"]) ?>
      </div>
    </div>
    <div class="form-group pull-right">
        <?=Html::submitButton(Yii::t('app', 'Update account'), ['class' => 'btn btn-warning pull']) ?>
    </div>

</div>
