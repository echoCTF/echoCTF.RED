<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\smartcity\models\Infrastructure;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\InfrastructureTarget */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="infrastructure-target-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'infrastructure_id')->dropDownList(ArrayHelper::map(Infrastructure::find()->all(), 'id', 'name'),
            ['prompt'=>'Select Infrastructure'])->Label('Infrastructure')->hint('The infrastructure this target belongs') ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->all(), 'id', 'name'),
            ['prompt'=>'Select Target'])->Label('Target')->hint('The target associated with the infrastructure') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
