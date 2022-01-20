<?php if($profile->headshotsCount>0 || count($profile->writeups)>0 || count($profile->owner->challengeSolvers)>0 || intval($game->badges->received_by($profile->player_id)->count())>0):?>
<div class="card card-nav-tabs bg-dark">
  <div class="card-header card-header-primary">
    <!-- colors: "header-primary", "header-info", "header-success", "header-warning", "header-danger" -->
    <div class="nav-tabs-navigation">
      <div class="nav-tabs-wrapper">
        <ul class="nav nav-tabs" data-tabs="tabs">

<?php if($profile->headshotsCount>0):?>
          <li class="nav-item">
            <a class="nav-link" href="#headshots" data-toggle="tab">
            <i class="material-icons">track_changes</i> Headshots
            </a>
          </li>
<?php endif;?>
<?php if(count($profile->writeups)>0):?>
          <li class="nav-item">
            <a class="nav-link" href="#writeups" data-toggle="tab">
            <i class="material-icons">history_edu</i> Writeups
            </a>
          </li>
<?php endif;?>
<?php if(count($profile->owner->challengeSolvers)>0):?>
          <li class="nav-item">
            <a class="nav-link" href="#challenge_solves" data-toggle="tab">
            <i class="material-icons">checklist</i> Challenge Solves
            </a>
          </li>
<?php endif;?>
<?php if(intval($game->badges->received_by($profile->player_id)->count())>0):?>
          <li class="nav-item">
            <a class="nav-link" href="#badges" data-toggle="tab">
            <i class="material-icons">admin_panel_settings</i> Badges
            </a>
          </li>
<?php endif;?>
        </ul>
      </div>
    </div>
  </div><!--/card-header-->

  <div class="card-body">
    <div class="tab-content text-center">

      <?php if($profile->headshotsCount>0):?>
      <div class="tab-pane" id="headshots">

      <?=$this->render('_headshots',['profile'=>$profile,'headshots'=>$headshots]);?>
      </div>
      <?php endif;?>

      <?php if(count($profile->writeups)>0):?>
      <div class="tab-pane" id="writeups">
      <?=$this->render('_writeups',['profile'=>$profile]);?>
      </div>
      <?php endif;?>

      <?php if(count($profile->owner->challengeSolvers)>0):?>
      <div class="tab-pane" id="challenge_solves">
      <?=$this->render('_challenge_solves',['profile'=>$profile]);?>
      </div>
      <?php endif;?>
      <?php if($game->badges !== null && $game->badges->received_by($profile->player_id)->count() > 0):?>
      <div class="tab-pane" id="badges">
      <?=$this->render('_badges',['game'=>$game,'profile'=>$profile]);?>
      </div>
      <?php endif;?>
    </div>
  </div><!--/card-body-->
</div><!--/card-nav-tabs-->
<?php endif;?>
