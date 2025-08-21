<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;
?>
<div class="player-abuser-form">
  <?php $form = ActiveForm::begin(['action' =>['/moderation/abuser/create'], 'id' => 'create-abuser', 'method' => 'post',]); ?>
  <div class="row d-flex">
    <div class="col">
    <?= $form->field($abuserModel, 'model_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->label(false) ?>
    </div>
    <div class="col"><?= $form->field($abuserModel, 'reason')
        ->dropDownList(
          ['fake_account'=>'Fake account'],           // Flat array ('id'=>'label')
          ['prompt'=>'Choose a reason']    // options
          )->label(false)?>
    </div>
    <div class="col justify-content-center align-self-center">
      <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <?= $form->field($abuserModel, 'player_id')->hiddenInput(['value'=> $model->player_id])->label(false); ?>

    <?= $form->field($abuserModel, 'model')->hiddenInput(['value'=> 'player'])->label(false); ?>
    <?php ActiveForm::end(); ?>
  </div>
</div>