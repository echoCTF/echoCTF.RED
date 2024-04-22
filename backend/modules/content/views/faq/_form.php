<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Faq */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="faq-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true])->hint('The FAQ entry title (supports HTML)') ?>

    <?= $form->field($model, 'body')->textarea(['rows' => 6])->hint('The FAQ entry body (supports HTML)') ?>

    <?= $form->field($model, 'weight')->textInput()->hint('The entry weight used for ordering') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
