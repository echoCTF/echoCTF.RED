<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\LayoutOverride */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layout-override-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'route')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'guest')->checkBox() ?>

    <?= $form->field($model, 'repeating')->checkBox() ?>

    <?= $form->field($model, 'player_id')->widget(\app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search','active'=>1,'status'=>10],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that this entry will belong to.');  ?>

    <?= $form->field($model, 'css')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'js')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'valid_from')->widget(\yii\jui\DatePicker::class, ['dateFormat' => 'yyyy-MM-dd',]) ?>
    <?= $form->field($model, 'valid_until')->widget(\yii\jui\DatePicker::class, ['dateFormat' => 'yyyy-MM-dd',]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
