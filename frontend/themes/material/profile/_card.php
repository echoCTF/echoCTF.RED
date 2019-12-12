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
    <h6 class="card-category text-gray"><?=$profile->rank->ordinalPlace?> Place / Level <?=$profile->experience->id?> (<?=$profile->experience->name?>) <img src="/images/flags/shiny/24/<?=$profile->country?>.png"/></h6>
    <h4 class="card-title"><?=Html::encode($profile->owner->username)?>/<?=Html::encode($profile->owner->fullname)?></h4>
    <p class="card-description">
      <?=Html::encode($profile->bio)?>
    </p>
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
