<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\NetworkTargetSchedule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="network-target-schedule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->widget(app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/infrastructure/target/ajax-search'],
        'options' => ['placeholder' => 'Find target by name or id']
    ]) ?>
    <?= $form->field($model, 'network_id')->widget(app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/infrastructure/network/ajax-search'],
        'options' => ['placeholder' => 'Find network by name, codename or id ']
    ]) ?>

    <?= $form->field($model, 'migration_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
