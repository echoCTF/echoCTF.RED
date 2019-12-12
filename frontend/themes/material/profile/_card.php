<?php
use yii\helpers\Html;
?>
<div class="card card-profile">
  <div class="card-avatar bg-primary">
    <a href="#pablo">
      <img class="img" src="/images/avatars/<?=$profile->avatar?>" />
    </a>
  </div>
  <div class="card-body">
    <h6 class="badge badge-secondary"><?=$profile->experience->name?> Level <?=$profile->experience->id?></h6>
<!--    <h6 class="card-category text-gray"><?=$profile->rank->ordinalPlace?> Place / Level <?=$profile->experience->id?> (<?=$profile->experience->name?>) <img src="/images/flags/shiny/24/<?=$profile->country?>.png"/></h6>-->
    <h4 class="card-title"><?=Html::encode($profile->owner->username)?></h4>
    <p class="card-description">
      <?=Html::encode($profile->bio)?>
    </p>
    <ul class="nav flex-column">
  <?php if(intval(Yii::$app->user->id)===intval($profile->player_id)):?>
          <li class="nav-item text-left"><strong>Visibility</strong> <span class="pull-right"><?=$profile->visibilities[$profile->visibility]?></span></li>
          <li class="nav-item text-left"><strong>Spins</strong> <span class="pull-right"><abbr title="Spins today"><?=intval($playerSpin['counter'])?></abbr> / <abbr title="Total Spins"><?=$playerSpin['total']?></abbr></span></li>
  <?php endif;?>
          <li class="nav-item text-left"><strong>Real name</strong> <span class="pull-right"><?=Html::encode($profile->owner->fullname)?></span></li>
          <li class="nav-item text-left"><strong>Country</strong> <span class="pull-right"><?=$profile->rCountry->name?></span></li>
          <li class="nav-item text-left"><strong>Joined</strong> <span class="pull-right"><?=date("d.m.Y",strtotime($profile->owner->created))?></span></li>
          <li class="nav-item text-left"><strong>Last seen</strong> <span class="pull-right"><?=date("d.m.Y",strtotime($profile->last->on_pui))?></span></li>
          <?php if (trim($profile->twitter)):?><li class="nav-item text-left"><strong>Twitter</strong> <span class="pull-right"><?=Html::a('@'.Html::encode($profile->twitter),"https://twitter.com/".Html::encode($profile->twitter),['target'=>'_blank'])?></span></li><?php endif;?>
          <?php if (trim($profile->github)):?><li class="nav-item text-left"><strong>Github</strong> <span class="pull-right"><?=Html::a(Html::encode($profile->github),"https://github.com/".Html::encode($profile->github),['target'=>'_blank'])?></span></li><?php endif;?>
          <?php if (trim($profile->discord)):?><li class="nav-item text-left"><strong>Discord</strong> <span class="pull-right"><?=Html::encode($profile->discord)?></span></li><?php endif;?>
          <?php if (trim($profile->htb)):?><li class="nav-item text-left"><strong>HTB</strong> <span class="pull-right"><small><?=Html::a("https://hackthebox.eu/profile/".Html::encode($profile->htb),"https://hackthebox.eu/profile/".Html::encode($profile->htb),['target'=>'_blank'])?></small></span></li><?php endif;?>
      </ul>
      <hr/>
      <ul class="nav flex-column">
            <li class="nav-header text-left"><h6>Details</h6></li>
            <li class="nav-item text-left"><strong><i class="icon-signal"></i>Current Rank</strong> <span class="pull-right"><?=$profile->rank->ordinalPlace?></span></li>
            <li class="nav-item text-left"><strong><i class="icon-user"></i>Level <?=intval($profile->experience->id)?></strong> <span class="pull-right"><?=$profile->experience->name?></span></li>
            <li class="nav-item text-left"><strong><i class="icon-list"></i>Points</strong> <span class="pull-right"><?=number_format($profile->owner->playerScore->points)?></span></li>
            <li class="nav-item text-left"><strong><i class="icon-screenshot"></i>Headshosts</strong> <span class="pull-right"><?=$profile->headshotsCount?></span></li>
            <li class="nav-item text-left"><strong><i class="icon-large icon-flag"></i>Flags</strong> <span class="pull-right"><?php echo $profile->totalTreasures;?></span></li>
            <li class="nav-item text-left"><strong><i class="icon-large icon-fire"></i>Findings</strong> <span class="pull-right"><?php echo $profile->totalFindings;?></span></li>
        </ul>
    <hr/>
    <?php if($profile->twitter):?>
    <a target="_blank" href="https://twitter.com/<?=Html::encode($profile->twitter)?>" class="btn btn-primary btn-round fab fa-twitter" style="font-size:1.5rem;"></a>
    <?php endif;?>

    <?php if($profile->github):?>
      <a target="_blank" href="https://github.com/<?=Html::encode($profile->github)?>" class="btn btn-primary btn-round fab fa-github" style="font-size:1.5rem;"></a>
    <?php endif;?>

    <?php if($profile->discord):?>
    <a target="_blank" href="https://discordapp.com/channel/<?=Html::encode($profile->discord)?>" class="btn btn-primary btn-round fab fa-discord" style="font-size:1.5rem;"></a>
    <?php endif;?>

    <?php if($profile->htb):?>
    <a target="_blank" href="https://hackthebox.eu/profile/<?=Html::encode($profile->htb)?>" class="btn btn-primary btn-round fab fa-hackerrank" style="font-size:1.5rem;"></a>
    <?php endif;?>
  </div>
</div><!-- // end user profile card -->
