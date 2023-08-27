<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\infrastructure\models\Server;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetInstance */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-instance-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ]) ?>
    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Select target'])->hint('Choose the target to spawn instance for') ?>

    <?= $form->field($model, 'server_id')->dropDownList(ArrayHelper::map(Server::find()->orderBy(['name'=>SORT_ASC,'ip'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Select Server'])->hint('Choose the server this instance will or has spawned') ?>

    <?= $form->field($model, 'ipoctet')->textInput() ?>

    <?= $form->field($model, 'reboot')->dropDownList(['0'=>'Nop','1'=>'Reboot','2'=>'Destroy']); ?>
    <?= $form->field($model, 'team_allowed')->checkBox();?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
