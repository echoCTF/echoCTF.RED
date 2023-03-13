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
        'multiple' => true,
        'url' => ['/infrastructure/target/ajax-search'],
        'options' => ['placeholder' => 'Find target by name or id']
    ])->label('Target') ?>
    <?= $form->field($model, 'network_id')->widget(app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
        'multiple' => true,
        'url' => ['/infrastructure/network/ajax-search'],
        'options' => ['placeholder' => 'Find network by name, codename or id ']
    ])->label('Network')->hint('Network that the target will be placed under on the given datetime') ?>

        <?php if($model->migration_date===null) $model->migration_date=date('Y-m-d H:i:s');?>
    <?= $form->field($model, 'migration_date')->textInput(['placeholder'=>'YYYY-MM-DD HH:MM:SS'])->hint("Date and Time of the migration to the network") ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
