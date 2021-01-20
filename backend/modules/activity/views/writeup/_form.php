<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="writeup-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['fqdn'=>SORT_ASC])->all(), 'id', 'fqdn'), ['prompt'=>'Select Target'])->hint('The target for the headshot.') ?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->orderBy(['username'=>'asc'])->all(), 'id', function($model) { return Html::encode($model->username).': '.$model->id;}, function($model) {return mb_strtolower(mb_substr($model->username, 0, 1));}), ['prompt'=>'Select the player'])->Label('Player')->hint('The id of the player that this badge will be given') ?>

    <?= $form->field($model, 'formatter')->dropDownList([ 'text' => 'TEXT', 'markdown' => 'Markdown' ], ['prompt' => '']) ?>

    <?= $form->field($model, 'content')->textArea(['rows'=>15]) ?>

    <?= $form->field($model, 'approved')->checkbox() ?>


    <?= $form->field($model, 'status')->dropDownList([ 'PENDING' => 'PENDING', 'NEEDS FIXES' => 'NEEDS FIXES', 'REJECTED' => 'REJECTED', 'OK' => 'OK', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'comment')->textArea() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
