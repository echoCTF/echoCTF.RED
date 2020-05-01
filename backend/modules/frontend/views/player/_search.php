<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-search">

    <?php $form=ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);?>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'id') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'username') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'email')->textInput(['maxlength' => true])->hint('The email address of the player') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'fullname')->textInput(['maxlength' => true])->hint('The fullname of the player') ?></div>
    </div>
    <div class="row form-group">
      <div class="col-sm-3"><?= $form->field($model, 'academic')->checkbox()->hint('Whether the player is academic or not') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'active')->checkbox()->hint('Whether the player is active or not') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'type')->dropDownList(['offense' => 'Offense', 'defense' => 'Defense', ], ['prompt' => 'Choose player type'])->hint('Choose the type of the player. Either offense or defense') ?></div>
      <div class="col-sm-3"><?= $form->field($model, 'activkey')->textInput(['maxlength' => true])->hint('The activation key, generated automatically by the application. TODO') ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
