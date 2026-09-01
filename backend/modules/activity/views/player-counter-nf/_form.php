<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerCounterNf */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-counter-nf-form">

    <?php $form = ActiveForm::begin(); ?>
<?php if($model->isNewRecord):?>
    <?= $form->field($model, 'player_id',['inputOptions' => ['autofocus' => 'autofocus',]])->widget(\app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
      'multiple' => false,
      'url' => ['/frontend/player/ajax-search'],
      'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that we will assign this counter.');  ?>
<?php endif;?>
    <?= $form->field($model, 'metric')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'counter')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
