<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Faq */
/* @var $form yii\widgets\ActiveForm */

/*$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		'id'=>'player-form',
		'type'=>'horizontal',
		'action'=>array('/profile/update'),
		'enableAjaxValidation'=>true,
		'enableClientValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
	));
 $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'url'=>array('/profile/update'),
		'type'=>'primary',
		'label'=>'Update profile',
	));
*/
?>

<div class="account-form">

    <?php $form = ActiveForm::begin([
      'id'=>'player-form',
      'options'=>['autocomplete'=>"off"]
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
		<?= $form->field($model, 'fullname')->textInput(['maxlength' => true,'autocomplete'=>"off"]) ?>
		<?= $form->field($model, 'email')->textInput(['maxlength' => true,'autocomplete'=>"off"]) ?>
		<?= $form->field($model, 'password')->passwordInput(['autocomplete'=>"new-password"]) ?>
		<?= $form->field($model, 'confirm_password')->passwordInput(['autocomplete'=>"new-password"]) ?>
    <?=$model->password?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
