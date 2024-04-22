<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\LayoutOverride */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layout-override-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('A name to identify this override') ?>

    <?= $form->field($model, 'route')->textInput(['maxlength' => true])->hint('A URL route on the frontend to load activate this override') ?>

    <?= $form->field($model, 'guest')->checkBox()->hint('Whether or not guests are able to see this override on the frontend') ?>

    <?= $form->field($model, 'repeating')->checkBox()->hint('Whether or not this override will be repeating on a yearly basis') ?>

    <?= $form->field($model, 'player_id')->widget(\app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search','active'=>1,'status'=>10],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that this entry will affect.');  ?>

    <?= $form->field($model, 'css')->textarea(['rows' => 6])->label('CSS')->hint('A CSS snippet to load when this override takes effect') ?>

    <?= $form->field($model, 'js')->textarea(['rows' => 6])->label('Javascript')->hint('A javascript snippet to load when this override takes effect') ?>

    <?= $form->field($model, 'valid_from')->widget(\yii\jui\DatePicker::class, ['dateFormat' => 'yyyy-MM-dd',])->hint('A date this override will take effect') ?>

    <?= $form->field($model, 'valid_until')->widget(\yii\jui\DatePicker::class, ['dateFormat' => 'yyyy-MM-dd',])->hint('A date this override will finish taking effect') ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
