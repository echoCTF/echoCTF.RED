<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Badge;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerBadge */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-badge-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->orderBy(['username'=>'asc'])->all(),'id', function($model) { return Html::encode($model->username).': '.$model->id;}, function($model) {return mb_strtolower(mb_substr($model->username,0,1));}),['prompt'=>'Select the player'])->Label('Player')->hint('The id of the player that this badge will be given') ?>

    <?= $form->field($model, 'badge_id')->dropDownList(ArrayHelper::map(Badge::find()->all(),'id', function($model) { return '['.$model->name.']: '.$model->points;}),['prompt'=>'Select the badge'])->Label('Badge')->hint('The badge that will be given to the player') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
