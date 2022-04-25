<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerSpin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-spin-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that this entry will belong to.');  ?>

    <?= $form->field($model, 'counter')->textInput() ?>
    <?= $form->field($model, 'perday')->textInput() ?>

    <?= $form->field($model, 'total')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
