<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetStateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-state-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'total_headshots') ?>

    <?= $form->field($model, 'total_findings') ?>

    <?= $form->field($model, 'total_treasures') ?>

    <?= $form->field($model, 'player_rating') ?>

    <?php // echo $form->field($model, 'timer_avg') ?>

    <?php // echo $form->field($model, 'total_writeups') ?>

    <?php // echo $form->field($model, 'approved_writeups') ?>

    <?php // echo $form->field($model, 'finding_points') ?>

    <?php // echo $form->field($model, 'treasure_points') ?>

    <?php // echo $form->field($model, 'total_points') ?>

    <?php // echo $form->field($model, 'on_network') ?>

    <?php // echo $form->field($model, 'on_ondemand') ?>

    <?php // echo $form->field($model, 'ondemand_state') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
