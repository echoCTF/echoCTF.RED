<?php
/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Faq */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Country;
use app\models\Avatar;

$this->_fluid="-fluid";
?>

<div class="profile-form">
    <?php $form = ActiveForm::begin([
      'id'=>'profile-form',
     ]); ?>
    <div class="row">
      <div class="col-lg-6">
        <?=$form->field($model, 'visibility')->dropDownList($model->visibilities,['prompt'=>'Select your profile visibility'])->hint('Select the desired visibility setting for your profile')?>
      </div>
      <div class="col-lg-6">
	      <?=$form->field($model, 'country')->dropDownList(ArrayHelper::map(Country::find()->all(), 'id', 'name'),['prompt'=>'Select your Country'])->hint('Select your country')?>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-3">
        <?=$form->field($model, 'gravatar')->checkbox(['label'=>'Use Gravatar'])->hint('')->label(false) ?>
        <?=$form->field($model, 'twitter_avatar')->checkbox(['label'=>'Use Twitter avatar'])->hint('')->label(false) ?>
        <?=$form->field($model, 'github_avatar')->checkbox(['label'=>'Use github avatar'])->hint('')->label(false) ?>
      </div>
      <div class="col-lg-7">
		      <?=$form->field($model, 'avatar')->dropDownList(ArrayHelper::map(Avatar::find()->all(), 'id', 'id'),['prompt'=>'Select your avatar'])->hint('Select an avatar from the list')?>
      </div>
      <div class="col-lg-2">
		      <img class="thumbnail img-fluid pull-right" id="preview_avatar" src="/images/avatars/<?=$model->avatar?>" alt="<?=$model->avatar?>"/>
      </div>
    </div>
		<?=$form->field($model, 'bio')->textarea() ?>
    <div class="row">
      <div class="col-lg-3">
        <?=$form->field($model, 'discord')->textInput(['maxlength' => true])->Label('<i class="fab fa-discord"></i> Discord') ?>
      </div>
      <div class="col-lg-3">
		    <?=$form->field($model, 'twitter')->textInput(['maxlength' => true])->Label('<i class="fab fa-twitter"></i> Twitter') ?>
      </div>
      <div class="col-lg-3">
    		<?=$form->field($model, 'github')->textInput(['maxlength' => true])->Label('<i class="fab fa-github"></i> Github') ?>
      </div>
      <div class="col-lg-3">
    		<?=$form->field($model, 'htb')->textInput(['maxlength' => true]) ?>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
		      <?=$form->field($model, 'terms_and_conditions')->checkbox(['label'=>$model->attributeLabels()['terms_and_conditions']])->hint('')->label(false) ?>
      </div>
      <div class="col-lg-4">
        <?=$form->field($model, 'mail_optin')->checkbox(['label'=>$model->attributeLabels()['mail_optin']])->hint('')->label(false) ?>
      </div>
      <div class="col-lg-4">
        <?=$form->field($model, 'gdpr')->checkbox(['label'=>$model->attributeLabels()['gdpr']])->hint('')->label(false) ?>
      </div>
    </div>
    <div class="form-group">
        <?=Html::submitButton(Yii::t('app', 'Update Profile'), ['class' => 'btn btn-info pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>
</div>
