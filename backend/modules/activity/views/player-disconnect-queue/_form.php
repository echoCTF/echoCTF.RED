<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerDisconnectQueue $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="player-disconnect-queue-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'player_id',['inputOptions' => ['autofocus' => 'autofocus',]])->widget(AutocompleteAjax::class, [
      'multiple' => false,
      'url' => ['/frontend/player/ajax-search'],
      'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that we initiate a disconnect.');  ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
