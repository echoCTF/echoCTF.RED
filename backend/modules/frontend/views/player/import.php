<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title='Import Players';
$this->params['breadcrumbs'][]=['label' => 'Players', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-import">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="player-import-form">
    <?php $form=ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($model, 'heading_first')->checkbox() ?>
        <?= $form->field($model, 'player_ssl')->checkbox()?>

        <?= $form->field($model, 'csvFile')->fileInput()->Label('CSV file to import') ?>

        <div class="form-group">
            <?= Html::submitButton('Import', ['class' => 'btn btn-success']) ?>
        </div>


    <?php ActiveForm::end();?>
    </div>
</div>
