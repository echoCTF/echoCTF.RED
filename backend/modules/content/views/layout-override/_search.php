<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\LayoutOverrideSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layout-override-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'route') ?>

    <?= $form->field($model, 'guest') ?>

    <?= $form->field($model, 'player_id') ?>

    <?php // echo $form->field($model, 'css') ?>

    <?php // echo $form->field($model, 'js') ?>

    <?php // echo $form->field($model, 'valid_from') ?>

    <?php // echo $form->field($model, 'valid_until') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
