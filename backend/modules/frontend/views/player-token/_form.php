<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerToken $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="player-token-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <div class="col"><?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
                            'multiple' => false,
                            'url' => ['/frontend/player/ajax-search'],
                            'options' => ['placeholder' => 'Find player by email, username, id or profile.']
                          ])->Label('Player')->hint('Choose the player for the token') ?></div>
    <div class="col"><?= $form->field($model, 'description')->textInput(['maxlength' => true,'placeholder'=>'my token description'])->hint("A small description for the token") ?></div>
    </div>

    <div class="row">
    <div class="col"><?= $form->field($model, 'type')->dropDownList($model->types)->hint('The type of this token') ?></div>
    <div class="col"><?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?></div>
    <?php if(!$model->isNewRecord):?>
    <div class="col"><?= $form->field($model, 'expires_at')->textInput()->hint('Token expiration date (default: in 60 days)') ?></div>
    <?php endif;?>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
