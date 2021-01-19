<?php
use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\target\TargetWidget;
use app\widgets\leaderboard\Leaderboard;
use app\widgets\stream\StreamWidget as Stream;
//$this->_fluid="-fluid";
$this->title=Yii::$app->sys->event_name.' Dashboard';
$this->_description="The echoCTF dashboard page";
$this->registerJsFile('/js/plugins/chartist.min.js',['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile('/js/plugins/chartist-plugin-legend.js',['depends' => 'yii\web\JqueryAsset']);

?>

<div class="dashboard-index">
  <div class="body-content">
    <div class="row justify-content-center">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-flag"></i>',
                'color'=>'primary',
                'title'=>number_format($dashboardStats->claims) /*sprintf('%d / %d', $treasureStats->claimed, $treasureStats->total)*/,
                'subtitle'=>'Flag Claims',
                'footer'=>'<div class="stats"></div>',
            ]);Card::end();?>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-chart-line"></i>',
                'color'=>'primary',
                'title'=>number_format(\app\models\Stream::find()->count()),
                'subtitle'=>'Activities',
                'footer'=>'<div class="stats"></div>',
            ]);Card::end();?>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-globe"></i>',
                'color'=>'danger',
                'title'=>sprintf('%d', $dashboardStats->countries),
                'subtitle'=>'Countries',
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
          <div class="card bg-dark">
            <div class="card-body">
                <h3 class="card-title text-center">10 day activity</h3>
            </div>
            <div class="card-img-top ct-chart" id="LastDaysActivityChart"></div>

          </div>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
          <?php Card::begin([
              'type'=>'card-stats',
              'header'=>'header-icon',
              'icon'=>'<i class="fas fa-level-up-alt"></i>',
              'color'=>'warning',
              'title'=>"Progress",
              'subtitle'=>"Current level: ".Yii::$app->user->identity->profile->experience->name,
              'footer'=>'<div class="stats"></div>',
          ]);
          $pct=intval((Yii::$app->user->identity->profile->experience->max_points - Yii::$app->user->identity->playerScore->points)/ ((Yii::$app->user->identity->profile->experience->max_points + Yii::$app->user->identity->playerScore->points)/2) * 100);
          ?>
          <div class="progress">
            <div class="progress-bar text-dark" role="progressbar" style="width: <?=$pct?>%" aria-valuenow="<?=Yii::$app->user->identity->playerScore->points?>" aria-valuemin="<?=Yii::$app->user->identity->profile->experience->min_points?>" aria-valuemax="<?=Yii::$app->user->identity->profile->experience->max_points?>"><b><?=$pct?>%</b></div>
          </div>
          <?php Card::end();?>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
          <?php Card::begin([
              'type'=>'card-stats',
              'header'=>'header-icon',
              'icon'=>'<img src="/images/headshot.svg" class="img-fluid" style="max-height: 60px;"/>',
              'color'=>'danger',
              'title'=>"Completed: ".Yii::$app->user->identity->profile->HeadshotsCount,
              'subtitle'=>"Targets: ".\app\modules\target\models\Target::find()->active()->count(),
              'footer'=>'<div class="stats"></div>',
          ]);
            $headshotsPct=intval((Yii::$app->user->identity->profile->headshotsCount/intval(\app\modules\target\models\Target::find()->active()->count()))*100);
          ?>
          <div class="progress">
            <div class="progress-bar text-dark" role="progressbar" style="width: <?=$headshotsPct?>%" aria-valuenow="<?=Yii::$app->user->identity->profile->headshotsCount?>" aria-valuemin="0" aria-valuemax="<?=\app\modules\target\models\Target::find()->active()->count()?>"><b><?=$headshotsPct?>%</b></div>
          </div>
          <?php Card::end();?>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
          <?php Card::begin([
              'type'=>'card-stats',
              'header'=>'header-icon',
              'icon'=>'<i class="fas fa-clipboard-list"></i>',
              'color'=>'warning',
              'title'=>"Completed: ".Yii::$app->user->identity->profile->challengesSolverCount,
              'subtitle'=>"Challenges: ".\app\modules\challenge\models\Challenge::find()->count(),
              'footer'=>'<div class="stats"></div>',
          ]);
            $headshotsPct=intval((Yii::$app->user->identity->profile->challengesSolverCount/intval(\app\modules\challenge\models\Challenge::find()->count()))*100);
          ?>
          <div class="progress">
            <div class="progress-bar text-dark" role="progressbar" style="width: <?=$headshotsPct?>%" aria-valuenow="<?=Yii::$app->user->identity->profile->headshotsCount?>" aria-valuemin="0" aria-valuemax="<?=\app\modules\target\models\Target::find()->active()->count()?>"><b><?=$headshotsPct?>%</b></div>
          </div>
          <?php Card::end();?>
        </div>

    </div>


  </div><!-- //body-content -->
</div>
<?php
$this->registerJs(
    "dataLastDaysActivityChart = {
      labels: [".implode($dayActivity['labels'],",")."],
      series: [
        [".implode($dayActivity['playerSeries'],",")."],
        [".implode($dayActivity['overallSeries'],",")."],
      ]
    };
    LastDaysActivityChart.update(dataLastDaysActivityChart);
",
    4
);
