<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

  <?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-md-5">
    <?= $form->field($model, 'player_id',['inputOptions' => ['autofocus' => 'autofocus',]])->widget(AutocompleteAjax::class, [
      'multiple' => false,
      'url' => ['/frontend/player/ajax-search'],
      'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that we will send the notification.');  ?>
    </div>
    <div class="col-md-5">
    <?= $form->field($model, 'category')->dropDownList($model->supportedCategories())->hint('Choose the notification type. <code>swal:</code> prefixed notifications invoke a modal popup.') ?>
    </div>
    <div class="col-md-2">
    <?= $form->field($model, 'archived')->checkBox(['label'=>false])->label('Archived')->hint('Should the notification be archived? (default: no)') ?>
    </div>
  </div>
  <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>


  <div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>