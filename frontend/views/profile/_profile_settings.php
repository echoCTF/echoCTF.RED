<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Faq */
/* @var $form yii\widgets\ActiveForm */
/* $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'profile-form',
	'type'=>'horizontal',
	'action'=>array('/profile/update'),
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
<?php $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'url'=>array('/profile/update'),
		'type'=>'primary',
		'label'=>'Update profile',
	)); ?>
 */
?>

<div class="profile-form">

    <?php $form=ActiveForm::begin(['id'=>'profile-form']);?>
    <?= $form->field($model, 'visibility')->dropDownList($model->visibilities, ['prompt'=>'Select your profile visibility'])->Label('Visibility')->hint('Select the desired visibility setting for your profile')?>
		<?= $form->field($model, 'country')->dropDownList($model->visibilities, ['prompt'=>'Select your profile visibility'])->Label('Visibility')->hint('Select the desired visibility setting for your profile')?>
		<?= $form->field($model, 'avatar')->dropDownList($model->visibilities, ['prompt'=>'Select your profile visibility'])->Label('Visibility')->hint('Select the desired visibility setting for your profile')?>
		<div class="row"><div class="span3 col-sm"><img class="thumbnail pull-right" id="preview_avatar" width="50px" src="/images/avatars/<?=$model->avatar?>" alt="<?=$model->avatar?>"/></div></div>
		<?= $form->field($model, 'bio')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'discord')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'twitter')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'github')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'htb')->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'terms_and_conditions')->checkbox()->hint('') ?>
		<?= $form->field($model, 'mail_optin')->checkbox()->hint('') ?>
		<?= $form->field($model, 'gdpr')->checkbox()->hint('') ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
