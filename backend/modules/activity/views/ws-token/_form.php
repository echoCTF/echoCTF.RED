<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\WsToken $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="ws-token-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
      'multiple' => false,
      'url' => ['/frontend/player/ajax-search'],
      'options' => ['placeholder' => 'Find player by email, username, id or profile.']
      ])->hint('The player that the token will belong to.');  ?>

    <?= $form->field($model, 'token')->textInput() ?>

    <?= $form->field($model, 'subject_id')->textInput() ?>

    <?= $form->field($model, 'is_server')->checkbox() ?>

    <?= $form->field($model, 'expires_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
