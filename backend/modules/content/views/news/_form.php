<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true])->hint('Category that this article belongs, supports HTML.') ?>
    <?= $form->field($model, 'category')->textInput(['maxlength' => true])->hint('Category that this article belongs, supports HTML.') ?>
    <?= $form->field($model, 'body')->textArea(['rows'=>5])->hint('The body of the news article, supports HTML and Markdown. Use <code>&lt!--TEASER--&gt;</code> to split large text.') ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
