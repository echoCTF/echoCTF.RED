<?php
use yii\helpers\Html;
?>
<div class="dashboard-index">
  <div class="body-content">
    <div class="row">
      <div class="col-lg-6">
        <?php echo $this->render('_account_settings',['model'=>$accountForm]); ?>
      </div>
      <div class="col-lg-6">

      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header card-header-primary">
        <h4 class="card-title">Edit Profile</h4>
        <p class="card-category">Complete your profile</p>
      </div>
      <div class="card-body">
        <?php echo $this->render('_profile_settings',['model'=>$profileForm]); ?>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-profile">
      <div class="card-avatar bg-danger">
        <a href="#pablo">
          <img class="img" src="/images/avatars/<?=$profile->avatar?>" />
        </a>
      </div>
      <div class="card-body">
        <h6 class="card-category text-gray"><?=$profile->rank->ordinalPlace?> Place / Level <?=$profile->experience->id?> (<?=$profile->experience->name?>)</h6>
        <h4 class="card-title"><?=Html::encode($profile->owner->fullname)?></h4>
        <p class="card-description">
          <?=Html::encode($profile->bio)?>
        </p>
        <a href="#pablo" class="btn btn-primary btn-round">Follow</a>
      </div>
    </div><!-- // end user profile card -->
  </div><!-- // end profile card col-md-4 -->
</div>
