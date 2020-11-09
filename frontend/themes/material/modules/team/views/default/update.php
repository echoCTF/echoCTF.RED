<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title=Yii::$app->sys->event_name.' Update team details ['.Html::encode($model->name).']';
$this->_fluid="-fluid";

?>
<div class="team-update">
  <div class="body-content">
    <h2>Update team [<code><?=$model->name?></code>]</h2>
    <hr />
    <div class="card">
      <div class="card-header card-header-primary">
        <h4 class="card-title">Team update</h4>
        <p class="card-category">Update your team details</p>
      </div>
      <div class="card-body">
        <?php $form=ActiveForm::begin(['action' => ['update'],'id' => 'team-update-form']);?>
        <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'description')->textArea() ?>
        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
          <div class="fileinput-new thumbnail img-circle img-raised">
            <img src="/images/avatars/team/<?=$model->validLogo?>" rel="nofollow" class="rounded img-thumbnail" alt="Logo of <?=Html::encode($model->name)?>">
          </div>
          <div class="fileinput-preview fileinput-exists thumbnail img-circle img-raised"></div>
          <div>
          <?= $form->field($model, 'uploadedAvatar')->label('Choose a team logo (300x300 PNG)',['class'=>'btn btn-raised btn-round btn-warning btn-file'])->fileInput() ?>
          </div>
        </div>

        <div class="form-group">
          <?=Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'update-button']) ?>
        </div>
        <?php ActiveForm::end();?>
      </div>
    </div>
  </div>
</div>
