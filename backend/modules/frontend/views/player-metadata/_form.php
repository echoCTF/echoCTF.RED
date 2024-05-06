<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerMetadata $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="player-metadata-form">

    <?php $form = ActiveForm::begin(); ?>
      <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
        ])->hint('The player that this profile will belong to.');  ?>


    <?= $form->field($model, 'identificationFile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'affiliation')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
