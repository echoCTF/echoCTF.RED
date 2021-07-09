<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetMetadataSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-metadata-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'target_id') ?>

    <?= $form->field($model, 'scenario') ?>

    <?= $form->field($model, 'instructions') ?>

    <?= $form->field($model, 'solution') ?>

    <?= $form->field($model, 'pre_credits') ?>

    <?php // echo $form->field($model, 'post_credits') ?>

    <?php // echo $form->field($model, 'pre_exploitation') ?>

    <?php // echo $form->field($model, 'post_exploitation') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
