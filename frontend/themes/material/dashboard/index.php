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
                'icon'=>'<i class="fas fa-flag"></i>',
                'color'=>'primary',
                'title'=>number_format($treasureStats->claims) /*sprintf('%d / %d', $treasureStats->claimed, $treasureStats->total)*/,
                'subtitle'=>'Flag Claims',
                'footer'=>'<div class="stats"></div>',
            ]);Card::end();?>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-globe"></i>',
                'color'=>'danger',
                'title'=>sprintf('%d', $totalHeadshots),
                'subtitle'=>'Countries',
                'footer'=>'<div class="stats"></div>',
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
                'footer'=>'<div class="stats"></div>',
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
                'footer'=>'<div class="stats"></div>',
            ]);Card::end();?>
        </div>
    </div>


    <div class="row">
      <div class="col">
        <?php
        Pjax::begin(['id'=>'leaderboard-listing', 'enablePushState'=>false, 'linkSelector'=>'#leaderboard-pager a', 'formSelector'=>false]);
        echo Leaderboard::widget(['dataProvider'=>null, 'player_id'=>Yii::$app->user->id, 'divID'=>"Leaderboard", 'title'=>'Leaderboard','pageSize'=>10]);
        Pjax::end();
        ?>
      </div>
    </div><!-- //row -->
  </div><!-- //body-content -->
</div>
