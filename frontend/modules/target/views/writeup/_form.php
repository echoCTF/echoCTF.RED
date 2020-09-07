<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Player;
use app\modules\target\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="writeup-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'content')->textArea(['rows'=>15,'style'=>'font-family: monospace; color: lightgray','wrap'=>"hard"]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
