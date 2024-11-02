<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerDisconnectQueueHistory $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="player-disconnect-queue-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
