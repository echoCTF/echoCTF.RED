<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\NetworkPlayer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="network-player-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'network_id')->dropDownList(ArrayHelper::map(app\modules\gameplay\models\Network::find()->all(), 'id', 'name'),
            ['prompt'=>'Select Network'])->Label('Network')->hint('The network this Player will belong') ?>

    <?= $form->field($model, 'player_id')->widget(\app\widgets\sleifer\autocompleteAjax\AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search','active'=>1,'status'=>10],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player that this entry will belong to.');  ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
