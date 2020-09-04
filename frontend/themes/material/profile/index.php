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
    <?php if($game->badges !== null && $game->badges->received_by($profile->player_id)->count() > 0):?><h3>Player badges</h3><?php endif;?>
    <div class="row game-badges">

<?php if($game->badges !== null) foreach($game->badges->received_by($profile->player_id)->all() as $badge):?>

  <div class="col-md-2">
    <div class="iconic-card">
      <center><?=$badge->pubname?></center>
      <h3><?=$badge->name?></h3>
      <p><?=$badge->pubdescription?></p>
    </div>
  </div>

<?php endforeach;?>
    </div>
    <div class="row">
    <div class="col-sm-8"><?php
    \yii\widgets\Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
    echo Stream::widget(['divID'=>'stream', 'dataProvider' => null, 'player_id'=>$profile->player_id, 'pagerID'=>'stream-pager']);
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
