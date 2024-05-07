<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="writeup-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['fqdn'=>SORT_ASC])->all(), 'id', 'fqdn'), ['prompt'=>'Select Target'])->hint('The target for the headshot.') ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that the writeup will be attributed.');  ?>

    <?= $form->field($model, 'formatter')->dropDownList([ 'text' => 'TEXT', 'markdown' => 'Markdown' ], ['prompt' => '']) ?>

    <?= $form->field($model, 'language_id')->dropDownList(ArrayHelper::map(\app\modules\settings\models\Language::find()->orderBy('l')->all(), 'id', 'l'))?>
    <?= $form->field($model, 'content')->textArea(['rows'=>15]) ?>

    <?= $form->field($model, 'approved')->checkbox() ?>


    <?= $form->field($model, 'status')->dropDownList([ 'PENDING' => 'PENDING', 'NEEDS FIXES' => 'NEEDS FIXES', 'REJECTED' => 'REJECTED', 'OK' => 'OK', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'comment')->textArea() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
