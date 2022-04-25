<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;
/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerRelation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-relation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search','active'=>1,'status'=>10],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that is credited with the referral.');  ?>
    <?= $form->field($model, 'referred_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that will appear as reffered.');  ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
