<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;
use app\modules\gameplay\models\Tutorial;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TutorialTarget */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tutorial-target-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tutorial_id')->dropDownList(ArrayHelper::map(Tutorial::find()->orderBy('title')->all(), 'id', function($model) { return Html::encode($model->title);},function($model) { return ucfirst(mb_substr($model->title,0,1));}))->Label('Tutorial')->hint('Choose the tutorial to assign a target.')?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->where(['active' => 1])->orderBy('name')->all(), 'id', function($model) { return Html::encode($model->name);},function($model) { return ucfirst(mb_substr($model->name,0,1));}))->Label('Target')->hint('Choose the target to attach this tutorial.')?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
