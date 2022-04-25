<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;
use app\modules\frontend\models\Player;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\SpinHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="spin-history-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->all(), 'id', function($model) {
        return sprintf("(id:%d) %s/%s", $model['id'], $model['fqdn'], $model['ipoctet']);}), ['prompt'=>'Select the target'])->Label('Target') ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that the spin history entry will belong to.');  ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
