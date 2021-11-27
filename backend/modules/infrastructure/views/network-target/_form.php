<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\NetworkTarget */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="network-target-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'network_id')->dropDownList(ArrayHelper::map(app\modules\gameplay\models\Network::find()->all(), 'id', 'name'),
            ['prompt'=>'Select Network'])->Label('Network')->hint('The network this Target will belong') ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(app\modules\gameplay\models\Target::find()->orderBy('name')->all(), 'id', 'name'),
                    ['prompt'=>'Select Target'])->Label('Target')->hint('The Target to assign to the network') ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
