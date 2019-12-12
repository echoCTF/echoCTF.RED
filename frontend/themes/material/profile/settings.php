<?php
use yii\helpers\Html;
?>
<div class="dashboard-index">
  <div class="body-content">
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header card-header-info">
            <h4 class="card-title">Profile Settings</h4>
            <p class="card-category">Update your profile settings...</p>
          </div>
          <div class="card-body">
              <?php echo $this->render('_profile_settings',['model'=>$profileForm]); ?>
              <?php echo $this->render('_account_settings',['model'=>$accountForm]); ?>
              <div class="clearfix"></div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <?=$this->render('_card',['profile'=>$profile]);?>
      </div><!-- // end profile card col-md-4 -->

    </div>
  </div>
</div>
