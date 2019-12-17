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
$this->title = Yii::$app->sys->event_name .' - Profile of '.Html::encode($profile->owner->username);
$this->_description = Html::encode($profile->bio);


?>
<!-- <center><img src="/images/logo.png" width="60%"/></center>
<hr>-->
<div class="profile-index">
  <div class="body-content">
    <div class="row">
      <div class="col-md-8">
        <?php \yii\widgets\Pjax::begin(['id'=>'target-listing','enablePushState'=>false,'linkSelector'=>'#target-pager a', 'formSelector'=>false]);?>
        <?php echo TargetWidget::widget(['dataProvider' => null,'player_id'=>$profile->player_id,'title'=>'Progress','category'=>'Progress of '.Html::encode($profile->owner->username).' on platform targets','personal'=>true]);?>
        <?php \yii\widgets\Pjax::end()?>
      </div>
      <div class="col-md-4">
        <?=$this->render('_card',['profile'=>$profile]);?>
      </div><!-- // end profile card col-md-4 -->
    </div><!--/row-->
    <div class="row game-badges">
<?php foreach($game->badges->received_by($profile->player_id)->all() as $badge):?>
      <div class="col-sm-1" style="font-size: 550%">
        <?php printf('<abbr title="%s">%s</abbr>', $badge->name, $badge->pubname);?>
      </div>
<?php endforeach;?>
    </div>
    <div class="row">
    <div class="col-sm-8"><?php
    \yii\widgets\Pjax::begin(['id'=>'stream-listing','enablePushState'=>false,'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
    echo Stream::widget(['divID'=>'stream','dataProvider' => $streamProvider,'pagerID'=>'stream-pager']);
    \yii\widgets\Pjax::end();
    ?></div>
    <div class="col-sm-4">
      <?php
      Pjax::begin(['id'=>'leaderboard-listing','enablePushState'=>false,'linkSelector'=>'#leaderboard-pager a', 'formSelector'=>false]);
      echo Leaderboard::widget(['divID'=>"Leaderboard",'player_id'=>$profile->player_id, 'pageSize'=>8,'title'=>'Leaderboard','category'=>'Listing current player page']);
      Pjax::end();
      ?>
    </div>
    </div>
  </div><!--//body-content-->
</div><!--//profile-index-->
