<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Finding;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerFinding */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-finding-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->all(), 'id', 'username'), ['prompt'=>'Select player'])->Label('Player')->hint('The player you want to give this hint') ?>

    <?= $form->field($model, 'finding_id')->dropDownList(ArrayHelper::map(Finding::find()->all(), 'id', 'name', 'target.fqdn'), ['prompt'=>'Select finding']) ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
