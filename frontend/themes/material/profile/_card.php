<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Twitter;
use app\modules\game\models\Headshot;
if(array_key_exists('subscription',Yii::$app->modules)!==false)
{
  $subscription=Yii::$app->getModule('subscription');
}
else {
  $subscription=new \app\models\DummySubscription;
}
?>
<div class="card card-profile">
  <div class="card-avatar bg-primary">
    <a href="<?=$profile->linkTo?>">
      <img class="img" src="/images/avatars/<?=$profile->avtr?>" />
    </a>
  </div>
  <div class="card-body">
    <?php if($profile->isMine):?><p></p><?php endif;?>
    <h6 class="badge badge-secondary">Level <?=$profile->experience->id?> / <?=$profile->experience->name?></h6>
    <h4 class="card-title"><?=Html::encode($profile->owner->username)?></h4>
    <p class="card-description">
      <?=Html::encode($profile->bio)?>
    </p>
    <?php if($subscription->exists):?>
      <?php if($subscription->isActive):?>
        <h5 class="rounded text-success font-weight-bold"><?=$subscription->product->name?> expires in <?=$subscription->expires?></h5>
        <?=$subscription->getPortalButton($this)?>
      <?php else:?>
        <p class="rounded text-danger font-weight-bold">Your <?=$subscription->product->name?> has expired<br/><?=Html::a('Subscribe',['/subscription/default/index'],['class'=>'btn btn-primary text-dark font-weight-bold']);?></p>

      <?php endif;?>
    <?php endif;?>
    <?php if($profile->isMine):?>
      <?php echo Html::a(Url::to(['profile/index', 'id'=>$profile->id], 'https'), ['profile/index', 'id'=>$profile->id]);?> <?php echo Twitter::widget([
            'message'=>sprintf('Checkout my profile at %s! %s', \Yii::$app->sys->{"event_name"}, $profile->braggingRights),
            'url'=>Url::to(['profile/index', 'id'=>$profile->id], 'https'),
            'linkOptions'=>['class'=>'profile-tweet', 'target'=>'_blank', 'style'=>'font-size: 1.3em;', 'rel'=>'noopener noreferrer nofollow'],
        ]);?>
    <?php else: ?>
      <?php echo Html::a(Url::to(['profile/index', 'id'=>$profile->id], 'https'), ['profile/index', 'id'=>$profile->id]);?> <?php echo Twitter::widget([
            'message'=>sprintf('Checkout the profile of %s at %s', $profile->twitterHandle, \Yii::$app->sys->{"event_name"}),
            'linkOptions'=>['class'=>'profile-tweet', 'target'=>'_blank', 'style'=>'font-size: 1.3em;','rel'=>'noopener noreferrer nofollow'],
        ]);?>
    <?php endif;?>
    <ul class="nav flex-column">
  <?php if($profile->isMine || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin)):?>
          <li class="nav-item text-center">
              <?php if(\Yii::$app->user->identity->sSL):?><?=Html::a("<i class='fas fa-user-shield'></i> OpenVPN", ['profile/ovpn'], ['class'=>'btn btn-primary btn-sm','alt'=>'Download OpenVPN Configuration'])?><?php endif;?>
              <?=Html::a("<i class='fas fa-user'></i> Edit", ['profile/settings'], ['class'=>'btn btn-danger btn-sm','alt'=>'Edit profile and account settings'])?>
              <?=Html::a("<i class='fas fa-id-badge'></i> Badge", ['profile/badge','id'=>$profile->id], ['class'=>'btn btn-success btn-sm'])?>
          </li>
          <li class="nav-item text-left"><strong><i class="fa fa-eye"></i> Visibility</strong> <span class="pull-right"><?=$profile->visibilities[$profile->visibility]?></span></li>
          <li class="nav-item text-left"><strong><i class="fas fa-sync-alt"></i> Spins</strong> <span class="pull-right"><abbr title="Spins today"><?=intval($profile->spins->counter)?></abbr> / <abbr title="Total Spins"><?=intval($profile->spins->total)?></abbr></span></li>
          <li class="nav-item text-left"><strong><i class="fas fa-file-signature"></i> Real name</strong> <span class="pull-right"><?=Html::encode($profile->owner->fullname)?></span></li>
  <?php endif;?>
          <li class="nav-item text-left"><strong><i class="fas fa-globe"></i> Country</strong> <span class="pull-right"><?=$profile->rCountry->name?></span></li>
          <li class="nav-item text-left"><strong><i class="fas fa-calendar-check"></i> Joined</strong> <span class="pull-right"><?=date("d.m.Y", strtotime($profile->owner->created))?></span></li>
          <li class="nav-item text-left"><strong><i class="far fa-calendar-alt"></i> Last seen</strong> <span class="pull-right"><?=date("d.m.Y", strtotime($profile->last->on_pui))?></span></li>
          <li class="nav-item text-center" style="font-size: 2.3em"><?php if(trim($profile->twitter)):?><?=Html::a('<i class="fab fa-twitter text-twitter"></i>', "https://twitter.com/".Html::encode($profile->twitter), ['target'=>'_blank','title'=>"Twitter profile",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
          <?php if(trim($profile->github)):?><?=Html::a('<i class="fab fa-github text-github"></i>', "https://github.com/".Html::encode($profile->github), ['target'=>'_blank','title'=>"Github profile",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
          <?php if(trim($profile->htb)):?><?=Html::a('<i class="fab fa-codepen text-primary"></i>', "https://www.hackthebox.eu/profile/".Html::encode($profile->htb), ['target'=>'_blank','title'=>"HTB profile",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
          <?php if(trim($profile->twitch)):?><?=Html::a('<i class="fab fa-twitch text-twitch"></i>', "https://twitch.tv/".Html::encode($profile->twitch), ['target'=>'_blank','title'=>"TwitchTV Channel",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
          <?php if(trim($profile->youtube)):?><?=Html::a('<i class="fab fa-youtube text-youtube"></i>', "https://youtube.com/channel/".Html::encode($profile->youtube), ['target'=>'_blank','title'=>"Youtube channel",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
        </li>

      </ul>
      <hr/>
      <ul class="nav flex-column">
        <li class="nav-header text-left"><h6>Details</h6></li>
        <li class="nav-item text-left"><strong><i class="fa fa-signal"></i> Current Rank</strong> <span class="pull-right"><?php if($profile->rank) echo $profile->rank->ordinalPlace?></span></li>
        <li class="nav-item text-left"><strong><i class="fa fa-list"></i> Points</strong> <span class="pull-right"><?=number_format($profile->owner->playerScore->points)?></span></li>
        <li class="nav-item text-left"><strong><i class="fas fa-flag"></i> Flags</strong> <span class="pull-right"><?php echo $profile->totalTreasures;?></span></li>
        <li class="nav-item text-left"><strong><i class="fas fa-fire"></i> Findings</strong> <span class="pull-right"><?php echo $profile->totalFindings;?></span></li>
        <?php if(count($profile->owner->challengeSolvers)>0):?><li class="nav-item text-left"><strong><i class="fas fa-tasks"></i> Challenges</strong> <span class="pull-right"><?=count($profile->owner->challengeSolvers)?></span></li><?php endif;?>
      </ul>
<?php if($profile->owner->networks && $profile->isMine):?>
      <hr/>
      <ul class="nav flex-column">
        <li class="nav-header text-left"><h6>Networks Access</h6></li>
        <?php foreach($profile->owner->networks as $network):?>
          <li class="nav-item text-left"><strong><img class="img" src="<?=$network->icon?>" height="30px"/> <?=$network->name?></strong></li>
        <?php endforeach;?>
      </ul>
<?php endif;?>
  </div>
</div><!-- // end user profile card -->
