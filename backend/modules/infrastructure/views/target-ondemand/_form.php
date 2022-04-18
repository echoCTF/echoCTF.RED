<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetOndemand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-ondemand-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->widget(AutocompleteAjax::class, [
        'id'=>'targetAjax',
        'multiple' => false,
        'url' => ['/infrastructure/target/ajax-search'],
        'options' => ['placeholder' => 'Find target by name or ip.']
    ]) ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'id'=>'playerAjax',
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ]) ?>

    <?= $form->field($model, 'state')->dropDownList([
      '-1' => 'Powered Off',
      '1' => 'Powered On',
      ]) ?>

    <?= $form->field($model, 'heartbeat')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
