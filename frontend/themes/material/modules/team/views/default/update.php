<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Update team details').' ['.$model->name.']';
$this->_fluid="-fluid";
$this->loadLayoutOverrides=true;

?>
<div class="team-update">
  <div class="body-content">
    <h2><?=\Yii::t('app','Update team')?> [<code><?=Html::encode($model->name)?></code>]</h2>
    <hr />
    <div class="card bg-dark">
      <div class="card-header card-header-primary">
        <h4 class="card-title"><?=\Yii::t('app','Team update')?></h4>
        <p class="card-category"><?=\Yii::t('app','Update your team details')?></p>
      </div>
      <div class="card-body">
        <?php $form=ActiveForm::begin(['action' => ['update'],'id' => 'team-update-form']);?>
        <div class="row">
          <div class="col">
            <div class="fileinput fileinput-new text-center" data-provides="fileinput">
              <div class="fileinput-new thumbnail img-circle img-raised">
                <img id="avatarPreview" src="/images/avatars/team/<?=$model->validLogo?>" rel="nofollow" class="rounded img-thumbnail" alt="Logo of <?=Html::encode($model->name)?>">
              </div>
              <div class="fileinput-preview fileinput-exists thumbnail img-circle img-raised"></div>
              <div>
              <?= $form->field($model, 'uploadedAvatar')->label(\Yii::t('app','Choose a team logo (300x300 PNG)'),['class'=>'btn btn-raised btn-round btn-warning btn-file text-dark'])->fileInput() ?>
              </div>
            </div>
          </div>
          <?php
$this->registerJs(
  "
  document.getElementById('team-uploadedavatar').addEventListener(
    'change',
    function(event){
      const [file] = document.getElementById('team-uploadedavatar').files
      if (file && isFileImage(file)) {
        document.getElementById('avatarPreview').style='max-width: 300px; max-height: 300px;';
        document.getElementById('avatarPreview').src=URL.createObjectURL(file);
      }
    },
    false
  );
  ",
  \yii\web\View::POS_READY,
  'img-preview-handler'
);
?>

          <div class="col">
            <div class="row">
              <div class="col"><?= $form->field($model, 'name')->textInput() ?></div>
              <div class="col"><?= $form->field($model, 'inviteonly')->checkbox() ?></div>
              <div class="col"><?= $form->field($model, 'locked')->checkbox() ?></div>
            </div>
            <?= $form->field($model, 'recruitment')->textArea([]) ?>
            <?= $form->field($model, 'description')->textArea([]) ?>
          </div>
        </div>

        <div class="form-group text-center">
          <?=Html::submitButton(\Yii::t('app','Update'), ['class' => 'btn btn-primary btn-block text-dark text-bold', 'name' => 'update-button']) ?>
        </div>
        <?php ActiveForm::end();?>
      </div>
    </div>
  </div>
</div>
