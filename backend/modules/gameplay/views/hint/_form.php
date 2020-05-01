<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Badge;
use app\modules\gameplay\models\Finding;
use app\modules\gameplay\models\Treasure;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Hint */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hint-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true])->hint('Short hint title') ?>

    <?= $form->field($model, 'player_type')->dropDownList(['offense' => 'Offense', 'defense' => 'Defense', 'both' => 'Both', ], ['prompt' => 'Choose player type']) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6])->hint('Descriptive hint message details') ?>

    <?= $form->field($model, 'category')->textInput(['maxlength' => true])->hint('Who knows? TODO') ?>

    <?= $form->field($model, 'badge_id')->dropDownList(ArrayHelper::map(Badge::find()->all(), 'id', 'name'),
            ['prompt'=>'Select Badge'])->Label('Badge')->hint('Give this hint to users who have the given badge') ?>

    <?= $form->field($model, 'finding_id')->dropDownList(ArrayHelper::map(Finding::find()->all(), 'id', 'name', 'target.fqdn'),
            ['prompt'=>'Select Finding'])->Label('Finding')->hint('Give this hint to users who have the given finding') ?>

    <?= $form->field($model, 'treasure_id')->dropDownList(ArrayHelper::map(Treasure::find()->all(), 'id', 'name', 'target.fqdn'),
            ['prompt'=>'Select Treasure'])->Label('Treasure')->hint('Give this hint to users who have the given treasure') ?>

    <?= $form->field($model, 'points_user')->textInput()->hint('Give this hint to users who have reached the given points') ?>

    <?= $form->field($model, 'points_team')->textInput()->hint('Give this hint to teams who have reached the given points') ?>

    <?= $form->field($model, 'timeafter')->textInput()->hint('Give this hint after this many seconds to active users') ?>

    <?= $form->field($model, 'active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
