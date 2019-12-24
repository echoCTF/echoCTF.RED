<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\SpinQueue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="spin-queue-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->all(),'id',function($model) {
        return sprintf("(id:%d) %s/%s", $model['id'],$model['fqdn'],$model['ipoctet']);}),['prompt'=>'Select the target'])->Label('Target') ?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
