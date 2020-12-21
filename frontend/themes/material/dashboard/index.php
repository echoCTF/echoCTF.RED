<?php
use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\target\TargetWidget;
use app\widgets\leaderboard\Leaderboard;
use app\widgets\stream\StreamWidget as Stream;
$this->_fluid="-fluid";
$this->title=Yii::$app->sys->event_name.' Dashboard';
$this->_description="The echoCTF dashboard page";
?>

<div class="dashboard-index">
  <div class="body-content">
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fa fa-flag"></i>',
                'color'=>'primary',
                'title'=>sprintf('%d / %d', $treasureStats->claimed, $treasureStats->total),
                'subtitle'=>'Claimed / Total Flags',
                'footer'=>'<div class="stats">
                        <i class="material-icons flag-claim">flag</i>'.number_format($treasureStats->claims).' total claims
                      </div>',
            ]);Card::end();?>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fa fa-skull"></i>',
                'color'=>'danger',
                'title'=>sprintf('%d', $totalHeadshots),
                'subtitle'=>'Headshots',
                'footer'=>'<div class="stats">
                        <i class="material-icons">memory</i> You have '.count(Yii::$app->user->identity->headshots).' headshots
                      </div>',
            ]);Card::end();?>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-server"></i>',
                'color'=>'warning',
                'title'=>\app\modules\target\models\Target::find()->active()->count(),
                'subtitle'=>'Targets',
                'footer'=>'<div class="stats">
                        <i class="material-icons">memory</i> '.number_format($totalPoints).' Total points
                      </div>',
            ]);Card::end();?>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'type'=>'card-stats',
                'header'=>'header-icon',
                'icon'=>'<i class="fas fa-user-secret"></i>',
                'color'=>'info',
                'title'=>\app\models\Player::find()->active()->count(),
                'subtitle'=>'Users',
                'footer'=>'<div class="stats">
                        <i class="material-icons">update</i> '.\app\models\Player::find()->active()->with_score()->count().' with score
                      </div>',
            ]);Card::end();?>
        </div>
    </div>


    <div class="row">
      <div class="col-xl-8 col-lg-12 col-sm-12">
      <?php Pjax::begin(['id'=>'target-listing', 'enablePushState'=>false, 'linkSelector'=>'#target-pager a', 'formSelector'=>false]);?>
      <?php echo TargetWidget::widget(['dataProvider' => null, 'player_id'=>Yii::$app->user->id,'pageSize'=>8,'buttonsTemplate'=>'{tweet}']);?>
      <?php Pjax::end();?>
      </div>
      <div class="col-xl-4 col-sm-12">
<?php
Pjax::begin(['id'=>'leaderboard-listing', 'enablePushState'=>false, 'linkSelector'=>'#leaderboard-pager a', 'formSelector'=>false]);
echo Leaderboard::widget(['dataProvider'=>null, 'player_id'=>Yii::$app->user->id, 'divID'=>"Leaderboard", 'title'=>'Leaderboard','pageSize'=>10]);
Pjax::end();
?>
      </div>
    </div><!-- //row -->
<?php
Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
echo Stream::widget(['divID'=>'stream', 'dataProvider' => null, 'pagerID'=>'stream-pager']);
Pjax::end();
?>
  </div><!-- //body-content -->
</div>
