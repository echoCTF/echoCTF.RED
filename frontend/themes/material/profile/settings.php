<?php
use yii\helpers\Html;
?>
<div class="dashboard-index">
  <div class="body-content">
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header card-header-info">
            <h4 class="card-title">Profile Settings</h4>
            <p class="card-category">Update your profile settings...</p>
          </div>
          <div class="card-body">
              <?php echo $this->render('_profile_settings',['model'=>$profileForm]); ?>
              <div class="clearfix"></div>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="card">
          <div class="card-header card-header-warning">
            <h4 class="card-title">Account Settings</h4>
            <p class="card-category">Update your account settings...</p>
          </div>
          <div class="card-body">
            <?php echo $this->render('_account_settings',['model'=>$accountForm]); ?>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <?=$this->render('_card',['profile'=>$profile,'playerSpin'=>$playerSpin]);?>
      </div><!-- // end profile card col-md-4 -->

    </div>
  </div>
</div>
