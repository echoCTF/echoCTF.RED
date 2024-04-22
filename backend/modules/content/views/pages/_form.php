<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\EmailTemplate */
/* @var $form yii\widgets\ActiveForm */
?>
<small>These are full HTML pages.</small>
<div class="pages-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'metatags')->textArea(['maxlength' => true,'style'=>'font-family: monospace']) ?>

    <?= $form->field($model, 'body')->textArea(['maxlength' => true,'rows'=>20,'style'=>'font-family: monospace']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
