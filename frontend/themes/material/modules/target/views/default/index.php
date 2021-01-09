<?php
use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\target\TargetWidget;
use app\widgets\leaderboard\Leaderboard;
use app\widgets\stream\StreamWidget as Stream;
//$this->_fluid="-fluid";
$this->title=Yii::$app->sys->event_name.' Targets';
$this->_description="The echoCTF dashboard page";
$hidden_attributes=['id'];
?>

<div class="target-index">
  <div class="body-content">
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fa fa-flag"></i>',
                'color'=>'primary',
                'title'=>\app\modules\target\models\Treasure::find()->count(),
                'subtitle'=>'Flags',
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">flag</i>'.$treasureStats->claimed.' claimed by you
                      </div>',
            ]);Card::end();?>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-fingerprint"></i>',
                'color'=>'warning',
                'title'=>\app\modules\target\models\Finding::find()->count(),
                'subtitle'=>'Services',
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">track_changes</i> '.count(Yii::$app->user->identity->playerFindings).' services found by you
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
                        <i class="material-icons text-danger">memory</i> '.count(Yii::$app->user->identity->headshots).' headshots by you
                      </div>',
            ]);Card::end();?>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-medal"></i>',
                'color'=>'info',
                'title'=>number_format($totalPoints),
                'subtitle'=>'Points',
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">format_list_numbered</i> '.number_format(Yii::$app->user->identity->playerScore->points).' yours
                      </div>',
            ]);Card::end();?>
        </div>

    </div>
    <div class="row justify-content-center">
      <div class="col">
      <?php Pjax::begin(['id'=>'target-listing', 'enablePushState'=>false, 'linkSelector'=>'#target-pager a', 'formSelector'=>false]);?>
      <?php echo TargetWidget::widget(['dataProvider' => null, 'hidden_attributes'=>$hidden_attributes,'player_id'=>Yii::$app->user->id,'pageSize'=>10,'buttonsTemplate'=>null]);?>
      <?php Pjax::end();?>
      </div>
    </div><!-- //row -->
      <?php
      Pjax::begin(['id'=>'stream-listing','enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
      echo Stream::widget(['divID'=>'stream', 'dataProvider' => null, 'pagerID'=>'stream-pager']);
      Pjax::end();
      ?>

  </div><!-- //body-content -->
</div>
