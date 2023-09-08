<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\settings\models\Country;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Profile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profile-form">

    <?php $form=ActiveForm::begin();?>

    <div class="row form-group">
    <div class="col-sm-2"><?= $form->field($model, 'id')->textInput(['maxlength' => true])->hint('The profile id used for the player url.') ?></div>

    <div class="col-sm-4">
      <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
        ])->hint('The player that this profile will belong to.');  ?>
    </div>
    <div class="col-sm-3"><?= $form->field($model, 'country')->dropDownList(ArrayHelper::map(Country::find()->all(),'id','name'),['prompt'=>'Select country']) ?></div>
    <div class="col-sm-3"><?= $form->field($model, 'visibility')->dropDownList($model->visibilities)->hint('Player profile visibility') ?></div>

    </div>
    <?= $form->field($model, 'bio')->textarea(['rows' => 6]) ?>

    <div class="row form-group">
      <div class="col-sm-2"><?= $form->field($model, 'pending_progress')->checkbox()?></div>
      <div class="col-sm-2"><?= $form->field($model, 'approved_avatar')->checkbox()?></div>
      <div class="col-sm-3"><?= $form->field($model, 'avatar')->textInput(['maxlength' => true]) ?></div>
    </div>

    <h4>Social links</h4>
    <div class="row form-group">
      <div class="col-sm-2"><?= $form->field($model, 'discord')->textInput(['maxlength' => true]) ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'twitter')->textInput(['maxlength' => true]) ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'github')->textInput(['maxlength' => true]) ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'echoctf')->textInput(['maxlength' => true]) ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'htb')->textInput(['maxlength' => true]) ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'youtube')->textInput(['maxlength' => true]) ?></div>
      <div class="col-sm-2"><?= $form->field($model, 'twitch')->textInput(['maxlength' => true]) ?></div>
    </div>

    <h4>Legal</h4>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'terms_and_conditions')->checkBox()->hint('Terms and conditions accepted?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'mail_optin')->checkBox()->hint('User opted to receive mails from us?') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'gdpr')->checkBox()->hint('Privacy policy agreed?') ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
