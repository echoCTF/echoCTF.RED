<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetMetadata */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-metadata-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['fqdn'=>SORT_ASC])->all(), 'id', 'fqdn'), ['prompt'=>'Select Target'])->hint('The target for these metadata') ?>

    <?= $form->field($model, 'scenario')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'instructions')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'solution')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pre_credits')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'post_credits')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pre_exploitation')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'post_exploitation')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
