<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Badge;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerBadge */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-badge-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that the badge will be given to.');  ?>

    <?= $form->field($model, 'badge_id')->dropDownList(ArrayHelper::map(Badge::find()->all(), 'id', function($model) { return '['.$model->name.']: '.$model->points;}), ['prompt'=>'Select the badge'])->Label('Badge')->hint('The badge that will be given to the player') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
