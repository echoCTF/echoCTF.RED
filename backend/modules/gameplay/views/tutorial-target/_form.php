<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TutorialTarget */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tutorial-target-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tutorial_id')->textInput() ?>

    <?= $form->field($model, 'target_id')->textInput() ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
