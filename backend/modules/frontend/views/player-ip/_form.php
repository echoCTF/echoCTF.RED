<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerIp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-ip-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->all(),'id',function($model) {
        return $model['id'].' '.$model['username'].'/'.$model['email'];}),['prompt'=>'Select player'])->Label('Player')->hint('Choose the player on which you want to add an additional IP address to be recognised as attacking source') ?>

    <?= $form->field($model, 'ipoctet')->textInput()->label('IP')->hint('An IP address belonging to the player chosen above') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
