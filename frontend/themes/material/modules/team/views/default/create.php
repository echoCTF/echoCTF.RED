<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->loadLayoutOverrides=true;

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Create Team');

?>
<div class="team-create">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
    <?=\Yii::t('app','Create your own team :)')?>
    <hr />
    <div class="row">
      <div class="col-md-6">
        <p class="text-primary"><?=\Yii::t('app','Please fill out the following fields to create your own team:')?></p>
        <?php $form=ActiveForm::begin(['action' => ['create'],'id' => 'team-create-form']);?>
        <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'description')->textArea() ?>

        <div class="form-group">
          <?=Html::submitButton(\Yii::t('app','Create'), ['class' => 'btn btn-primary', 'name' => 'create-button']) ?>
        </div>
        <?php ActiveForm::end();?>
      </div>
    </div>
  </div>
</div>
