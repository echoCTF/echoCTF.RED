<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\WsTokenSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="ws-token-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'token') ?>

    <?= $form->field($model, 'player_id') ?>

    <?= $form->field($model, 'subject_id') ?>

    <?= $form->field($model, 'is_server') ?>

    <?= $form->field($model, 'expires_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
