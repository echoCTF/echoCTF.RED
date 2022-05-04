<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Treasure;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TreasureAction */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="treasure-action-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'treasure_id')->dropDownList(ArrayHelper::map(Treasure::find()->all(), 'id', 'name', 'target.fqdn'),
            ['prompt'=>'Select Treasure'])->Label('Treasure')->hint('The treasure this action will fire upon') ?>

    <?= $form->field($model, 'ipoctet')->textInput()->Label('IP') ?>

    <?= $form->field($model, 'port')->textInput() ?>

    <?= $form->field($model, 'command')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
