<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\grid\GridView;
use app\components\JustGage;
use app\widgets\stream\StreamWidget as Stream;
use app\widgets\target\TargetWidget;
use yii\widgets\Pjax;
use app\widgets\leaderboard\Leaderboard;

$game=Yii::$app->getModule('game');
$this->_fluid="-fluid";
$this->title=Yii::$app->sys->event_name.' Profile of: '.Html::encode($profile->owner->username);
$this->_description=Html::encode($profile->bio);
$this->_url=\yii\helpers\Url::to(['index', 'id'=>$profile->id], 'https');
?>
<div class="profile-index">
  <div class="body-content">
    <div class="row">
      <div class="col-md-8">
        <?php \yii\widgets\Pjax::begin(['id'=>'target-listing', 'enablePushState'=>false, 'linkSelector'=>'#target-pager a', 'formSelector'=>false]);?>
        <?php echo TargetWidget::widget(['dataProvider' => null, 'player_id'=>$profile->player_id, 'profile'=>$profile, 'title'=>'Progress', 'category'=>'Progress of '.Html::encode($profile->owner->username).' on platform targets.', 'personal'=>true]);?>
        <?php \yii\widgets\Pjax::end()?>
      </div>
      <div class="col-md-4">
        <?=$this->render('_card', ['profile'=>$profile]);?>
      </div><!-- // end profile card col-md-4 -->
    </div><!--/row-->

    <?php if($profile->headshotsCount>0):?><h3>Headshots <small>(ordered by date)</small></h3>
    <div class="row">
      <?php foreach($profile->owner->headshots as $headshot):?>
      <div class="col col-sm-1 col-md-5 col-lg-3">
        <div class="iconic-card">
          <img align="right" src="/images/targets/_<?=$headshot->target->name?>-thumbnail.png"/>
          <p><b><?=Html::a(
                      $headshot->target->name.' / '.long2ip($headshot->target->ip) ,
                        Url::to(['/target/default/versus', 'id'=>$headshot->target_id, 'profile_id'=>$profile->id]),
                        [
                          //'class'=>'btn-primary',
                          'style'=>'float: bottom;',
                          'title' => 'View target vs player card',
                          'aria-label'=>'View target vs player card',
                          'data-pjax' => '0',
                        ]
                    );?></b></p>
          <p><b><i class="fas fa-stopwatch text-danger"></i> <?=\Yii::$app->formatter->asDuration($headshot->timer)?></b></br>
          <b><i class="far fa-calendar-alt text-warning"></i> <?=\Yii::$app->formatter->asDate($headshot->created_at,'long')?></b></p>

        </div>
      </div>
      <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if($game->badges !== null && $game->badges->received_by($profile->player_id)->count() > 0):?><h3>Badges</h3>
    <div class="row game-badges">
      <?php foreach($game->badges->received_by($profile->player_id)->all() as $badge):?>
      <div class="col col-sm-12 col-md-4 col-lg-3">
        <div class="iconic-card">
          <center><?=$badge->pubname?></center>
          <h3><?=$badge->name?></h3>
          <?php if(!Yii::$app->user->isGuest && $profile->player_id===Yii::$app->user->id):?>
            <p><?=$badge->description?></p>
          <?php else:?>
            <p><?=$badge->pubdescription?></p>
          <?php endif;?>
        </div>
      </div>
      <?php endforeach;?>
    </div>
    <?php endif;?>

    <div class="row">
      <div class="col-sm-8"><?php
      \yii\widgets\Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
      echo Stream::widget(['divID'=>'stream', 'dataProvider' => null, 'player_id'=>$profile->player_id, 'pagerID'=>'stream-pager','title'=>'User History','category'=>'User history on the platform']);
      \yii\widgets\Pjax::end();
      ?></div>
      <div class="col-sm-4">
        <?php
        Pjax::begin(['id'=>'leaderboard-listing', 'enablePushState'=>false, 'linkSelector'=>'#leaderboard-pager a', 'formSelector'=>false]);
        echo Leaderboard::widget(['divID'=>"Leaderboard", 'player_id'=>$profile->player_id, 'pageSize'=>8, 'title'=>'Leaderboard', 'category'=>'Listing current player page. <small>Updated every 10 minutes</small>']);
        Pjax::end();
        ?>
      </div>
    </div>
  </div><!--//body-content-->
</div><!--//profile-index-->
