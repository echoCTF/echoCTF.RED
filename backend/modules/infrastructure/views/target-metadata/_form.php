<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetMetadata */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-metadata-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->textInput() ?>

    <?= $form->field($model, 'scenario')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'instructions')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'solution')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pre_credits')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'post_credits')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pre_exploitation')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'post_exploitation')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
