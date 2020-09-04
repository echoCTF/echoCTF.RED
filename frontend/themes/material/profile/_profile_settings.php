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
    <?php $form=ActiveForm::begin([
      'id'=>'profile-form',

      'options' => ['enctype' => 'multipart/form-data']
      ]);?>
    <div class="row">
      <div class="col-lg-6">
        <?=$form->field($model, 'visibility')->dropDownList($model->visibilities, ['prompt'=>'Select your profile visibility', 'class'=>'form-control selectpicker', 'data-size'=>'5', 'data-style'=>"btn-info"])->hint('Select the desired visibility setting for your profile')?>
      </div>
      <div class="col-lg-6">
	      <?=$form->field($model, 'country')->dropDownList(ArrayHelper::map(Country::find()->all(), 'id', 'name'), ['prompt'=>'Select your Country', 'class'=>'form-control selectpicker', 'data-size'=>'5', 'data-style'=>"btn-info"])->hint('Select your country')?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
          <div class="fileinput-new thumbnail img-circle img-raised">
         	  <img src="/images/avatars/<?=$model->avatar?>" rel="nofollow" class="rounded img-thumbnail" alt="Avatar of <?=Html::encode($model->owner->username)?>">
          </div>
          <div class="fileinput-preview fileinput-exists thumbnail img-circle img-raised"></div>
          <div>
            <?= $form->field($model, 'uploadedAvatar')->label('Choose avatar (300x300 PNG)',['class'=>'btn btn-raised btn-round btn-rose btn-file'])->fileInput()->hint('Choose an image to use as your avatar. Please be considerate of what you upload.') ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <?=$form->field($model, 'bio')->textarea(['rows'=>'4']) ?>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-3">
        <?=$form->field($model, 'discord')->textInput(['maxlength' => true,'autocomplete'=>'off'])->Label('<i class="fab fa-discord"></i> Discord')->hint('ex. DiscordUsername#Number') ?>
      </div>
      <div class="col-lg-3">
		    <?=$form->field($model, 'twitter')->textInput(['maxlength' => true,'autocomplete'=>'off'])->Label('<i class="fab fa-twitter"></i> Twitter')->hint('ex. TwitterHandle')?>
      </div>
      <div class="col-lg-3">
    		<?=$form->field($model, 'github')->textInput(['maxlength' => true,'autocomplete'=>'off'])->Label('<i class="fab fa-github"></i> Github')->hint('ex. GithubUsername') ?>
      </div>
      <div class="col-lg-3">
    		<?=$form->field($model, 'htb')->textInput(['maxlength' => true,'autocomplete'=>'off'])->hint('ex. HTBProfileID') ?>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <?=$form->field($model, 'mail_optin')->checkbox(['label'=>$model->attributeLabels()['mail_optin']])->hint('')->label(false) ?>
      </div>
    </div>
    <div class="form-group">
        <?=Html::submitButton(Yii::t('app', 'Update Profile'), ['class' => 'btn btn-info pull-right']) ?>
    </div>

    <?php ActiveForm::end();?>

    <div class="clearfix"></div>
</div>
