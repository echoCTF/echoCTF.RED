<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetOndemand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-ondemand-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['name'=>SORT_ASC])->all(), 'id','name'), ['prompt'=>'Select the target'])->Label('Target') ?>

    <?= $form->field($model, 'player_id')->textInput() ?>

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
