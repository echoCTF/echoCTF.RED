<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Inquiry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inquiry-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->orderBy(['username'=>SORT_ASC])->all(), 'id', 'username'), ['prompt'=>'Select player'])->Label('Player')->hint('The player id that the inquiry came from.') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Name provided by the user') ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true])->hint('Contact email') ?>

    <?= $form->field($model, 'answered')->checkBox()->hint('Have we answered this inquiry?') ?>

    <?= $form->field($model, 'category')->textInput(['maxlength' => true])->hint('Free form category, corresponds to teh source of this inquiry') ?>

    <?= $form->field($model, 'serialized')->textarea(['rows' => 6])->hint('Serialized form data. Depending on the source this object may be completely different') ?>

    <?= $form->field($model, 'body')->textarea(['rows' => 6])->hint('Formatted message body to resemble the original form and submitted inquiry.') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
