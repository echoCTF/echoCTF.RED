<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Team */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="team-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('The name of the team (will be shown on scoreboards, streams and other public locations)') ?>

    <?= $form->field($model, 'academic')->checkbox()->hint('Whether the team is academic or not') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('A description of the team, usually filled in by the team leader (the player who created a team)') ?>

    <?= $form->field($model, 'owner_id')->dropDownList(ArrayHelper::map(Player::find()->orderBy(['username'=>SORT_ASC])->all(), 'id', 'username'), ['prompt'=>'Select the owner for team'])->Label('Owner')->hint('Choose the player who will be the owner of the team')?>
    <?= $form->field($model, 'token')->textInput(['maxlength' => true])->hint('A token to be given to other players so they may join the team in a less error prone manner (automatically generated by the applicaion)') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
