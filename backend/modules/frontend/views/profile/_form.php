<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Profile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profile-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->all(),'id',function($model) {
        return $model['id'].' '.$model['username'].'/'.$model['email'];}),['prompt'=>'Select player'])->Label('Player')->hint('Choose the player on which you want to add an additional IP address to be recognised as attacking source') ?>
    <?= $form->field($model, 'visibility')->dropDownList($model->visibilities) ?>

    <?= $form->field($model, 'bio')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'avatar')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'twitter')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'github')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'discord')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'terms_and_conditions')->checkBox() ?>
    <?= $form->field($model, 'mail_optin')->checkBox() ?>
    <?= $form->field($model, 'gdpr')->checkBox() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
