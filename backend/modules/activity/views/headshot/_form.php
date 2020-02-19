<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Headshot */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="headshot-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['fqdn'=>SORT_ASC])->all(),'id','fqdn'),['prompt'=>'Select Target'])->hint('The target for the headshot') ?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->orderBy(['username'=>SORT_ASC])->all(),'id','username'),['prompt'=>'Select player'])->Label('Player')->hint('The player id you want to award points to') ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
