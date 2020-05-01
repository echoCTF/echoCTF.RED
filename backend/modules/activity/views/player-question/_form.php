<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Question;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerQuestion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-question-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'question_id')->dropDownList(ArrayHelper::map(Question::find()->all(), 'id', 'name'), ['prompt'=>'Select question'])->hint('The question id you want to award points for') ?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->all(), 'id', 'username'), ['prompt'=>'Select player'])->Label('Player')->hint('The player id you want to award points to') ?>

    <?= $form->field($model, 'points')->textInput(['maxlength' => true])->hint('The amount of points you want to award (This must almost always be the same is as the points automatically awarded when answering the above question, unless you really now what you are doing...)') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
