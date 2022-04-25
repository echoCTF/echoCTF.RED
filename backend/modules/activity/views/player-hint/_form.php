<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Hint;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerHint */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-hint-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that the hint will be given.');  ?>

    <?= $form->field($model, 'hint_id')->dropDownList(ArrayHelper::map(Hint::find()->all(), 'id', 'title'), ['prompt'=>'Select Hint']) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
