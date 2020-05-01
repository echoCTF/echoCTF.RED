<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Finding;
use app\modules\gameplay\models\Treasure;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Badge */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="badge-form">

    <?php $form=ActiveForm::begin();?>

      <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('The name of the badge') ?>
      <?= $form->field($model, 'pubname')->textInput(['maxlength' => true])->hint('The name of the badge to be shown on public locations (e.g. streams)') ?>
      <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('A description of the scenario of the badge and what players have to do to get it') ?>
      <?= $form->field($model, 'pubdescription')->textarea(['rows' => 6])->hint('A description of the scenario of the badge without leaking any information like target hostnames, IPs, services, ports etc.') ?>
      <?= $form->field($model, 'points')->textInput(['maxlength' => true])->hint('The amount of points to be awarded when a player/team gets this badge') ?>
        <?= $form->field($model, 'findings')->dropDownList(ArrayHelper::map(Finding::find()->all(), 'id', 'name', 'target.name'),
          [
          'class'=>'chosen-select input-md required',
          'multiple'=>'multiple',
          'size'=>"10",
          ]
        )->label("Select Findings");?>
        <?= $form->field($model, 'treasures')->dropDownList(ArrayHelper::map(Treasure::find()->all(), 'id', 'name', 'target.name'),
          [
          'class'=>'chosen-select input-md required',
          'multiple'=>'multiple',
          'size'=>"10",
          ]
        )->label("Select Treasures");?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
