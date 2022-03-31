<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\infrastructure\models\Server;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetInstance */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-instance-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if($model->isNewRecord):?>
    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::classname(), [
        'multiple' => true,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ]) ?>
    <?php else: ?>
    <?= $form->field($model, 'player_id')->hiddenInput(['value'=> $model->player_id])->label(false) ?>
    <?php endif;?>
    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Select team'])->hint('Choose the team to add the player chosen below') ?>

    <?= $form->field($model, 'server_id')->dropDownList(ArrayHelper::map(Server::find()->orderBy(['name'=>SORT_ASC,'ip'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Select team'])->hint('Choose the team to add the player chosen below') ?>

    <?= $form->field($model, 'ip')->textInput() ?>

    <?= $form->field($model, 'reboot')->checkbox() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
