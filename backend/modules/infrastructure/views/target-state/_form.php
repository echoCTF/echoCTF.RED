<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\gameplay\models\Target;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetState */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-state-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Select target'])->hint('Choose the target to spawn instance for') ?>

    <?= $form->field($model, 'total_headshots')->textInput() ?>

    <?= $form->field($model, 'total_findings')->textInput() ?>

    <?= $form->field($model, 'total_treasures')->textInput() ?>

    <?= $form->field($model, 'player_rating')->textInput() ?>

    <?= $form->field($model, 'timer_avg')->textInput() ?>

    <?= $form->field($model, 'total_writeups')->textInput() ?>

    <?= $form->field($model, 'approved_writeups')->textInput() ?>

    <?= $form->field($model, 'finding_points')->textInput() ?>

    <?= $form->field($model, 'treasure_points')->textInput() ?>

    <?= $form->field($model, 'total_points')->textInput() ?>

    <?= $form->field($model, 'on_network')->textInput() ?>

    <?= $form->field($model, 'on_ondemand')->textInput() ?>

    <?= $form->field($model, 'ondemand_state')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
