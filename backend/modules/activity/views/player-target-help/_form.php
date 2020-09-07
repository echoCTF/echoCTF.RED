<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerTargetHelp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-target-help-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['fqdn'=>SORT_ASC])->all(), 'id', 'fqdn'), ['prompt'=>'Select Target'])->hint('The target for the headshot.') ?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->orderBy(['username'=>'asc'])->all(), 'id', function($model) { return Html::encode($model->username).': '.$model->id;}, function($model) {return mb_strtolower(mb_substr($model->username, 0, 1));}), ['prompt'=>'Select the player'])->Label('Player')->hint('The id of the player that this badge will be given') ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
