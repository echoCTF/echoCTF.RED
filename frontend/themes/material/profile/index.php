<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use app\widgets\stream\StreamWidget as Stream;
use app\widgets\target\TargetWidget;
use yii\widgets\Pjax;
use app\widgets\leaderboard\Leaderboard;
use app\modules\target\models\PlayerTargetHelp as PTH;
use app\modules\target\models\Writeup;
use yii\data\ActiveDataProvider;
$this->loadLayoutOverrides=true;
$query = Writeup::find()->where(['player_id'=>$profile->player_id]);
$provider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => [
        'pageSize' => 10,
    ],
]);
$game=Yii::$app->getModule('game');
$this->_fluid="-fluid";
$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Profile of:').' '.Html::encode($profile->owner->username);
$this->_url=\yii\helpers\Url::to(['index', 'id'=>$profile->id], 'https');
$profile->scenario='validator';
$this->_description=$this->title;

?>
<div class="profile-index">
  <div class="body-content">
<?php if(!$profile->isMine):?>
    <div class="row d-flex justify-content-center">
      <div class="col-xl-9 d-flex justify-content-center">
        <?=Html::img(['profile/badge','id'=>$profile->id], ['class'=>'img-fluid'])?>
      </div>
    </div>
    <div class="row d-flex justify-content-center">
      <div class="col-lg-9 d-flex justify-content-center">
        <p class="h1">
        <?php if(\Yii::$app->sys->profile_echoctf===true && trim($profile->echoctf) && $profile->validate('echoctf')):?><?=Html::a('<i class="fas fa-flag fa-flip-horizontal text-danger"></i>', "https://echoctf.red/profile/".Html::encode($profile->echoctf), ['target'=>'_blank','title'=>"echoCTF.RED profile",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
        <?php if(\Yii::$app->sys->profile_discord===true && trim($profile->discord) && $profile->validate('discord')):?><?=Html::a('<i class="fab fa-discord text-discord"></i>', "https://discordapp.com/users/".Html::encode($profile->discord), ['target'=>'_blank','title'=>"Discord profile",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
        <?php if(\Yii::$app->sys->profile_twitter===true && trim($profile->twitter) && $profile->validate('twitter')):?><?=Html::a('<i class="fab fa-twitter text-twitter"></i>', "https://twitter.com/".Html::encode($profile->twitter), ['target'=>'_blank','title'=>"Twitter profile",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
        <?php if(\Yii::$app->sys->profile_github===true && trim($profile->github) && $profile->validate('github')):?><?=Html::a('<i class="fab fa-github"></i>', "https://github.com/".Html::encode($profile->github), ['target'=>'_blank','style'=>'color: #808080;','title'=>"Github profile",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
        <?php if(\Yii::$app->sys->profile_htb===true && trim($profile->htb) && $profile->validate('htb')):?><?=Html::a('<i class="fab fa-codepen text-primary"></i>', "https://www.hackthebox.eu/profile/".Html::encode($profile->htb), ['target'=>'_blank','title'=>"HTB profile",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
        <?php if(\Yii::$app->sys->profile_twitch===true && trim($profile->twitch) && $profile->validate('twitch')):?><?=Html::a('<i class="fab fa-twitch text-twitch"></i>', "https://twitch.tv/".Html::encode($profile->twitch), ['target'=>'_blank','title'=>"TwitchTV Channel",'rel'=>'nofollow'])?><?php endif;?>
        <?php if(\Yii::$app->sys->profile_youtube===true && trim($profile->youtube) && $profile->validate('youtube')):?><?=Html::a('<i class="fab fa-youtube text-youtube"></i>', "https://youtube.com/channel/".Html::encode($profile->youtube), ['target'=>'_blank','title'=>"Youtube channel",'rel'=>'noopener noreferrer nofollow'])?><?php endif;?>
        </p>
      </div>
    </div>
<?php endif;?>
    <div class="row d-flex justify-content-center">
<?php if($profile->isMine || $profile->pending_progress):?>
      <div class="col-lg-9">
        <?php
        \yii\widgets\Pjax::begin(['id'=>'target-listing-pjax', 'enablePushState'=>false, 'linkSelector'=>'#target-pager a', 'formSelector'=>false]);
        $title_prefix="";
        if($profile->isMine && $profile->pending_progress===false)
          $title_prefix='<b><i rel="tooltip" title="Progress will NOT be visible to others" class="fas fa-eye-slash"></i></b> ';
        else if($profile->isMine)
          $title_prefix='<b><i rel="tooltip" title="Progress will be visible to others" class="fas fa-eye"></i></i></b> ';

        $category='Pending progress of '.Html::encode($profile->owner->username).' on platform targets.';
        if(\Yii::$app->user->isGuest) $hidden_attributes=['id','ip'];
        else $hidden_attributes=['id'];
        echo TargetWidget::widget(['dataProvider' => null, 'player_id'=>$profile->player_id, 'profile'=>$profile, 'title'=>$title_prefix.'Progress', 'category'=>$category, 'personal'=>true,'hidden_attributes'=>$hidden_attributes]);
        \yii\widgets\Pjax::end();
        ?>
      </div>
<?php endif;?>
<?php if($profile->isMine):?>
      <div class="col-xl-3">
      <?=$this->render('_card', ['profile'=>$profile]);?>
      </div><!-- // end profile card col-md-4 -->
<?php endif;?>
    </div>
    <?=$this->render('_profile_tabs',['profile'=>$profile,'game'=>$game,'headshots'=>$headshots]);?>

    <div class="row">
      <div class="col">
        <?php
        Pjax::begin(['id'=>'global-leaderboard-listing', 'enablePushState'=>false, 'linkSelector'=>'#leaderboard-pager a', 'formSelector'=>false]);
        echo Leaderboard::widget(['divID'=>"Leaderboard", 'player_id'=>$profile->player_id, 'pageSize'=>8, 'title'=>\Yii::t('app','Global Leaderboard'), 'category'=>\Yii::t('app','Listing current player page on a global scale. <small>Updated every 10 minutes</small>')]);
        Pjax::end();
        ?>
      </div>
      <div class="col">
        <?php
        Pjax::begin(['id'=>'country-leaderboard-listing', 'enablePushState'=>false, 'linkSelector'=>'#country-leaderboard-pager a', 'formSelector'=>false]);
        echo Leaderboard::widget(['divID'=>"CountryLeaderboard", 'country'=>$profile->country,'player_id'=>$profile->player_id, 'pageSize'=>8, 'title'=>\Yii::t('app','Leaderboard for {country_name}',['country_name'=>$profile->fullCountry->name]), 'category'=>\Yii::t('app','Listing current player page for {country_name}. <small>Updated every 10 minutes</small>',['country_name'=>$profile->fullCountry->name]),'pagerID'=>'country-leaderboard-pager']);
        Pjax::end();
        ?>
      </div>
    </div>
    <div class="row">
      <div class="col"><?php
      \yii\widgets\Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
      echo Stream::widget(['divID'=>'stream', 'dataProvider' => null, 'player_id'=>$profile->player_id, 'pagerID'=>'stream-pager','title'=>'User History','category'=>'User history on the platform']);
      \yii\widgets\Pjax::end();
      ?></div>
    </div><!--/row-->

  </div><!--//body-content-->
</div><!--//profile-index-->
