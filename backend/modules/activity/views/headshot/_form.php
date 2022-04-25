<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Headshot */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="headshot-form">

    <?php $form=ActiveForm::begin(['validateOnSubmit' => false]);?>
    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['fqdn'=>SORT_ASC])->all(), 'id', 'fqdn'), ['prompt'=>'Select Target'])->hint('The target for the headshot.') ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that the headshot will be given.');  ?>

    <?= $form->field($model, 'timer')->textInput()->hint('Headshot timer in seconds. Leave empty for random') ?>
    <?= $form->field($model, 'first')->checkbox()->hint('Headshot is first for the target') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'value'=>'save', 'name'=>'submit[]','id'=>'saveBtn']) ?>
        <?= Html::submitButton(Yii::t('app', 'Give'), ['class' => 'btn btn-primary', 'value'=>'give', 'name'=>'submit[]', 'id'=>'giveBtn']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
