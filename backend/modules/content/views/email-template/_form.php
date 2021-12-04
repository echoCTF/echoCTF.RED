<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\EmailTemplate */
/* @var $form yii\widgets\ActiveForm */
?>
<small>These are full PHP/HTML templates. Only one variable is assigned at the moment <kbd>$user</kbd>, which holds the player model.</small>
<div class="email-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'html')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'txt')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
