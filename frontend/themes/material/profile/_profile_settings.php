<?php
/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Faq */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Country;
use app\models\Avatar;

$this->_fluid="-fluid";
?>

<div class="profile-form">
    <div class="row">
    <?php if($model->_cf('visibility')):?>
      <div class="col-lg-6">
        <?=$form->field($model, 'visibility')->dropDownList($model->visibilities, ['prompt'=>'Select your profile visibility', 'class'=>'form-control selectpicker', 'data-size'=>'5', 'data-style'=>"btn-info"])->hint('Select the desired visibility setting for your profile')?>
      </div>
    <?php endif;?>
    <?php if($model->_cf('country')):?>
      <div class="col-lg-6">
	      <?=$form->field($model, 'country')->dropDownList(ArrayHelper::map(Country::find()->all(), 'id', 'name'), ['prompt'=>'Select your Country', 'class'=>'form-control selectpicker', 'data-size'=>'5', 'data-style'=>"btn-info"])->hint('Select your country')?>
      </div>
      <?php endif;?>
    </div>
    <?php if ($model->_cf('avatar')):?>
    <div class="row">
      <div class="col-md-12">
        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
          <div class="fileinput-new thumbnail img-circle img-raised">
         	  <img id="avatarPreview" src="/images/avatars/<?=$model->avatar?>?<?=time()?>" rel="nofollow" class="rounded img-thumbnail" alt="Avatar of <?=Html::encode($model->username)?>">
          </div>
          <div class="fileinput-preview fileinput-exists thumbnail img-circle img-raised"></div>
          <div>
            <?= $form->field($model, 'uploadedAvatar')->label('Choose avatar (300x300 PNG)',['class'=>'btn btn-raised btn-round btn-rose btn-file'])->fileInput()->hint('Choose an image to use as your avatar. Please be considerate of what you upload.') ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif;?>
<?php
$this->registerJs(
  "
  document.getElementById('settingsform-uploadedavatar').addEventListener(
    'change',
    function(event){
      const [file] = document.getElementById('settingsform-uploadedavatar').files
      if (file && isFileImage(file)) {
        document.getElementById('avatarPreview').style='max-width: 300px; max-height: 300px;';
        document.getElementById('avatarPreview').src=URL.createObjectURL(file);
      }
    },
    false
  );
  ",
  \yii\web\View::POS_READY,
  'img-preview-handler'
);
?>

<?php if ($model->_cf('bio')):?>
    <div class="row">
      <div class="col-lg-12">
        <?=$form->field($model, 'bio')->textarea(['rows'=>'4']) ?>
      </div>
    </div>
<?php endif;?>

    <div class="row">
<?php if ($model->_cf('echoctf')):?>
      <div class="col-lg-4">
    		<?=$form->field($model, 'echoctf',['errorOptions' => ['class'=>'text-danger text-bold','encode' => false]])->textInput(['maxlength' => true,'autocomplete'=>'off'])->input('text',['placeholder'=>'1234'])->label('echoCTF.RED Profile')->hint('Your echoCTF.RED profile ID') ?>
      </div>
<?php endif;?>
<?php if ($model->_cf('discord')):?>
      <div class="col-lg-4">
        <?=$form->field($model, 'discord',['errorOptions' => ['class'=>'text-danger text-bold','encode' => false]])->textInput(['maxlength' => true,'autocomplete'=>'off'])->input('text', ['placeholder' => "DiscordUsername"])->Label('<i class="fab fa-discord"></i> Discord')->hint('Enter your discord user and number') ?>
      </div>
<?php endif;?>
<?php if ($model->_cf('twitter')):?>
      <div class="col-lg-4">
		    <?=$form->field($model, 'twitter',['errorOptions' => ['class'=>'text-danger text-bold','encode' => false]])->textInput(['maxlength' => true,'autocomplete'=>'off'])->input('text',['placeholder'=>'TwitterHandle'])->Label('<i class="fab fa-twitter"></i> Twitter')->hint('Your Twitter handle')?>
      </div>
<?php endif;?>
<?php if ($model->_cf('youtube')):?>
      <div class="col-lg-4">
        <?=$form->field($model, 'youtube',['errorOptions' => ['class'=>'text-danger text-bold','encode' => false]])->textInput(['maxlength' => true,'autocomplete'=>'off'])->input('text', ['placeholder' => "YoutubeChannelID"])->Label('<i class="fab fa-youtube"></i> Youtube')->hint('Enter your Youtube channel ID') ?>
      </div>
<?php endif;?>
<?php if ($model->_cf('twitch')):?>
      <div class="col-lg-4">
        <?=$form->field($model, 'twitch',['errorOptions' => ['class'=>'text-danger text-bold','encode' => false]])->textInput(['maxlength' => true,'autocomplete'=>'off'])->input('text', ['placeholder' => "TwitchUsername"])->Label('<i class="fab fa-twitch"></i> Twitch')->hint('Enter your Twitch username') ?>
      </div>
<?php endif;?>
<?php if ($model->_cf('github')):?>
      <div class="col-lg-4">
    		<?=$form->field($model, 'github',['errorOptions' => ['class'=>'text-danger text-bold','encode' => false]])->textInput(['maxlength' => true,'autocomplete'=>'off'])->input('text',['placeholder'=>'username'])->Label('<i class="fab fa-github"></i> Github')->hint('Your Github username') ?>
      </div>
<?php endif;?>
<?php if ($model->_cf('htb')):?>
      <div class="col-lg-4">
    		<?=$form->field($model, 'htb',['errorOptions' => ['class'=>'text-danger text-bold','encode' => false]])->textInput(['maxlength' => true,'autocomplete'=>'off'])->input('text',['placeholder'=>'1234'])->label('HTB Profile')->hint('Your HTB profile ID') ?>
      </div>
<?php endif;?>
    </div>
<?php if ($model->_cf('pending_progress')):?>
    <div class="row">
      <div class="col-lg-12">
          <?=$form->field($model, 'pending_progress')->checkBox(['label'=>'Show pending target progress?'])->Label('<i class="fas fa-bullhorn"></i> Progress')?>
      </div>
    </div>
<?php endif;?>

    <div class="form-group">
        <?=Html::submitButton(Yii::t('app', 'Update Profile'), ['class' => 'btn btn-info pull-right']) ?>
    </div>
    <div class="clearfix"></div>
</div>
