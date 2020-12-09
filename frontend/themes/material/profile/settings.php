<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile('@web/js/plugins/bootstrap-selectpicker.min.js', ['depends'=>['app\assets\MaterialAsset']]);
$this->registerCssFile("@web/css/bootstrap-select.min.css", [
    'depends' => ['app\assets\MaterialAsset'],
], 'css-print-theme');
$this->registerJs('$.fn.selectpicker.Constructor.BootstrapVersion = "4";');

?>
<div class="dashboard-index">
  <div class="body-content">
<?php $form=ActiveForm::begin(['id'=>'settings-form',]);?>
    <div class="row">
      <div class="col-xl-7">
        <div class="card">
          <div class="card-header card-header-info">
            <h4 class="card-title">Profile Settings</h4>
            <p class="card-category">Update your profile settings...</p>
          </div>
          <div class="card-body">
              <?php echo $this->render('_profile_settings', ['model'=>$settingsForm,'form'=>$form]);?>
              <div class="clearfix"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-5">
        <div class="card">
          <div class="card-header card-header-warning">
            <h4 class="card-title">Account Settings</h4>
            <p class="card-category">Update your account settings...</p>
          </div>
          <div class="card-body">
            <?php echo $this->render('_account_settings', ['model'=>$settingsForm,'form'=>$form]);?>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>
<?php ActiveForm::end();?>
  </div>
</div>
