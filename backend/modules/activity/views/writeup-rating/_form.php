<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\activity\models\Writeup;
use app\modules\activity\models\Headshot;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\WriteupRating */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="writeup-rating-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'writeup_id')->dropDownList(ArrayHelper::map(Writeup::find()->orderBy(['created_at'=>SORT_ASC])->all(), 'id', 'target.name','player.username'), ['prompt'=>'Select writeup'])->hint('The writeup to be rated.') ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that the rating will be belong to.');  ?>

    <?= $form->field($model, 'rating')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
